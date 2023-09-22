<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class DomainListScreen extends Screen
{
    public function query(): iterable
    {
        $domains =  \App\Models\Domain::with(['emails', 'companies', 'companies.financeYears'])->filters()->defaultSort('id')->paginate(20);
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
        $request = request();

        $filtersApplied = !empty($request->get('filter'));
        $sortApplied = !empty($request->get('sort'));

        $actions = [];
        if($filtersApplied) {
            $clearFiltersUrl = $request->fullUrlWithQuery(['filter' => null]);
            $actions[] = Link::make('Сбросить фильтры')
                ->href($clearFiltersUrl)
                ->icon('bs.arrow-clockwise');
        }

        if($sortApplied) {
            $clearSortUrl = $request->fullUrlWithQuery(['sort' => null]);
            $actions[] = Link::make('Сбросить сортировку')
                ->href($clearSortUrl)
                ->icon('bs.arrow-clockwise');
        }

        return $actions;
    }

    public function layout(): iterable
    {
        return [
            \App\Orchid\Layouts\Domain\DomainListLayout::class
        ];
    }
}
