<?php

namespace App\Orchid\Screens\Domain;

use App\Models\Domain;
use App\Orchid\Layouts\Domain\DomainListLayout;
use App\Orchid\Layouts\Domain\ImportDomainsLayout;
use App\Services\DomainService;
use App\Services\DomainsFileReaderService;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class DomainListScreen extends Screen
{
    private DomainService $service;

    public function __construct(DomainService $service)
    {
        $this->service = $service;
    }

    public function query(): iterable
    {
        $domains = Domain::with(['emails', 'companies', 'companies.financeYears'])->filters()->defaultSort('id')->paginate(20);
        $domains->map(function($domain) {
            $emails = $domain->emails()->get('email')->implode('email', ', ');
            $domain['emails_string'] = $emails;
        });

        return [
            'domains' => $domains
        ];
    }

    public function name(): ?string
    {
        return 'Домены';
    }

    public function commandBar(): iterable
    {
        $request = request();
        $filtersApplied = !empty($request->get('filter'));
        $sortApplied = !empty($request->get('sort'));

        $clearFiltersUrl = $request->fullUrlWithQuery(['filter' => null]);
        $clearSortUrl = $request->fullUrlWithQuery(['sort' => null]);

        $params = request()->query();

        return [
            Link::make('Сбросить фильтры')
                ->href($clearFiltersUrl)
                ->icon('bs.arrow-clockwise')
                ->canSee($filtersApplied),

            Link::make('Сбросить сортировку')
                ->href($clearSortUrl)
                ->icon('bs.arrow-clockwise')
                ->canSee($sortApplied),

            ModalToggle::make('Импорт')
                ->modal('import')
                ->method('import')
                ->icon('bs.plus-circle'),

            Button::make('Спарсить всю страницу')
                ->method('parsePage')
                ->parameters($params)
                ->icon('bs.cpu')
        ];
    }

    /**
     * Парсинг всех записей на странице
     *
     * @return void
     */
    public function parsePage() : void
    {
        $data = current($this->query()['domains']);
        $domainsString = $data->pluck('domain')->implode(',');

        $nodePath = config('parser.node_path');
        $parserPath = config('parser.parser_path');
        exec("$nodePath $parserPath --domains=\"$domainsString\"", $result, $errorCode);
        if($errorCode === 0) {
            Alert::success('Данные успешно обновлены!');
            return;
        }

        $response = implode('', $result);
        $response = json_decode($response, true);
        Alert::withoutEscaping()->error('<pre style="background-color: rgba(0, 0, 0, 0);">'.json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).'</pre>');
    }

    /**
     * Импорт доменов
     *
     * @param Request $request
     *
     * @return void
     * @throws \Exception
     */
    public function import(Request $request) : void
    {
        set_time_limit(0);
        $domainList = [];
        $requestDomainList = $request->post('domain_list');
        if(!empty($requestDomainList)) {
            $domainList = array_filter(array_merge($domainList, array_column($requestDomainList, 'value')));
        }

        $file = $request->file('file');
        if(!empty($file)) {
            $fileService = new DomainsFileReaderService($file);
            $domainList = array_merge($domainList, $fileService->parse());
        }

        $domainsCount = count($domainList);
        $createdCount = 0;
        if($domainsCount === 0) {
            return;
        }

        foreach($domainList as $domain) {
            $domain = $this->service->createOrUpdate(['domain' => $domain]);
            if($domain && $domain->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        if($createdCount === $domainsCount) {
            Alert::success("Добавлено $createdCount/$domainsCount");
        } elseif ($createdCount > 0 && $createdCount < $domainsCount) {
            Alert::warning("Добавлено $createdCount/$domainsCount");
        } else {
            Alert::error("Добавлено $createdCount/$domainsCount");
        }
    }

    public function layout(): iterable
    {
        return [
            DomainListLayout::class,
            Layout::modal('import', [
                ImportDomainsLayout::class
            ])->title('Импорт'),
        ];
    }
}
