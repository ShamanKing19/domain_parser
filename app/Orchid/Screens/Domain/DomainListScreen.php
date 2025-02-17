<?php

namespace App\Orchid\Screens\Domain;

use App\Models\Domain;
use App\Orchid\Layouts\Domain\DomainListLayout;
use App\Orchid\Layouts\Domain\ImportDomainsLayout;
use App\Repositories\DomainRepository;
use App\Services\DomainService;
use App\Services\DomainsFileReaderService;
use Exception;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class DomainListScreen extends Screen
{
    private DomainRepository $repository;
    private DomainService $service;

    public function __construct(DomainRepository $repository, DomainService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
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

            DropDown::make()
                ->icon('bs.three-dots')
                ->list([
                    Button::make('Спарсить')
                        ->method('parsePage')
                        ->parameters($params)
                        ->icon('bs.cpu'),

                    Button::make('Экспорт')
                        ->icon('bs.arrow-through-heart'),

                    Button::make('Удалить')
                        ->icon('trash')
                        ->confirm('Вы точно хотите удалить выбранные записи?')
                        ->method('removeSelected')
                ])
        ];
    }

    public function query(): iterable
    {
        $domains = Domain::with(['emails', 'companies', 'companies.financeYears'])->filters()->defaultSort('id')->paginate(20);
        $domains->map(function ($domain) {
            $emails = $domain->emails()->get('email')->implode('email', ', ');
            $domain['emails_string'] = $emails;
        });

        return [
            'domains' => $domains,
        ];
    }

    /**
     * Парсинг всех записей на странице
     *
     * @return void
     */
    public function parsePage(Request $request): void
    {
        $domainIdList = $request->post('domain_id_list');
        if (!empty($domainIdList)) {
            $domainsList = $this->repository->getListById($domainIdList);
        } else {
            $data = current($this->query()['domains']);
            $domainsList = $data->pluck('domain')->toArray();
        }

        $result = $this->service->parse($domainsList);
        if (!$result) {
            Alert::success('Данные успешно обновлены!');

            return;
        }

        Alert::withoutEscaping()->error('<pre style="background-color: rgba(0, 0, 0, 0);">' . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</pre>');
    }

    /**
     * Импорт доменов
     *
     * @param Request $request
     *
     * @return void
     * @throws Exception
     */
    public function import(Request $request): void
    {
        set_time_limit(0);
        $domainList = [];
        $requestDomainList = $request->post('domain_list');
        if (!empty($requestDomainList)) {
            $domainList = array_filter(array_merge($domainList, array_column($requestDomainList, 'value')));
        }

        $file = $request->file('file');
        if (!empty($file)) {
            $fileService = new DomainsFileReaderService($file);
            $domainList = array_merge($domainList, $fileService->parse());
        }

        $createdCount = $this->service->import($domainList);
        $this->showResultAlert(count($domainList), $createdCount, 'Добавлено');
    }

    /**
     * Отображение статуса
     *
     * @param int $all
     * @param int $done
     *
     * @return void
     */
    private function showResultAlert(int $all, int $done, string $message = ''): void
    {
        if ($done === $all) {
            Alert::success($message ? "$message $done/$all" : "$done/$all");
        } elseif ($done > 0 && $done < $all) {
            Alert::warning($message ? "$message $done/$all" : "$done/$all");
        } else {
            Alert::error($message ? "$message $done/$all" : "$done/$all");
        }
    }

    /**
     * Удаление выбранных доменов
     *
     * @param Request $request
     *
     * @return void
     */
    public function removeSelected(Request $request)
    {
        $idList = $request->post('domain_id_list');
        if (empty($idList)) {
            Alert::error('Не было выбрано ни одной записи');
        }

        $deletedCount = $this->service->remove($idList);
        $this->showResultAlert($deletedCount, $deletedCount, 'Удалено');
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
