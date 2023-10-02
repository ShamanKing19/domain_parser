<?php

namespace App\Orchid\Screens\Domain;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;

class DomainListScreen extends Screen
{
    public function query(): iterable
    {
        $domains = \App\Models\Domain::with(['emails', 'companies', 'companies.financeYears'])->filters()->defaultSort('id')->paginate(20);
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
    public function parsePage()
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

    public function layout(): iterable
    {
        return [
            \App\Orchid\Layouts\Domain\DomainListLayout::class
        ];
    }
}
