<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class DomainListScreen extends Screen
{
    public function query(): iterable
    {
        $domains =  \App\Models\Domain::with(['emails'])->filters()->defaultSort('id')->paginate(20);
        // TODO: Переделать
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
        return 'DomainList';
    }

    // TODO: Сделать кнопку "Спарсить все на странице"
    public function commandBar(): iterable
    {
        $request = request();
        $clearFiltersUrl = $request->fullUrlWithQuery(['filter' => null]);
        $clearSortUrl = $request->fullUrlWithQuery(['sort' => null]);

        return [
            Link::make('Сбросить фильтры')
                ->href($clearFiltersUrl)
                ->icon('bs.arrow-clockwise'),

            Link::make('Сбросить сортировку')
                ->href($clearSortUrl)
                ->icon('bs.arrow-clockwises'),
        ];
    }

    public function layout(): iterable
    {
        return [
            \App\Orchid\Layouts\Domain\DomainListLayout::class
        ];
    }
}
