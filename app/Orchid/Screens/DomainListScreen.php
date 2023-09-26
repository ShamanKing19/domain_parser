<?php

namespace App\Orchid\Screens;

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

    // TODO: Сделать кнопку "Спарсить все на странице"
    public function commandBar(): iterable
    {
        $actions = [];
        $request = request();
        $filtersApplied = !empty($request->get('filter'));
        $sortApplied = !empty($request->get('sort'));

        /* Сброс фильтров */
        if($filtersApplied) {
            $clearFiltersUrl = $request->fullUrlWithQuery(['filter' => null]);
            $actions[] = Link::make('Сбросить фильтры')
                ->href($clearFiltersUrl)
                ->icon('bs.arrow-clockwise');
        }

        /* Сброс сортировки */
        if($sortApplied) {
            $clearSortUrl = $request->fullUrlWithQuery(['sort' => null]);
            $actions[] = Link::make('Сбросить сортировку')
                ->href($clearSortUrl)
                ->icon('bs.arrow-clockwise');
        }

        /* Парсинг всех доменов на странице */
        $params = request()->query();
        $actions[] = Button::make('Спарсить всю страницу')
            ->method('parsePage')
            ->parameters($params)
            ->icon('bs.cpu');

        return $actions;
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

        exec("~/.nvm/versions/node/v16.17.1/bin/node /home/production/web/domainsparse.dev.skillline.ru/public_html/parser.js --domains=\"$domainsString\"", $result, $errorCode);
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
