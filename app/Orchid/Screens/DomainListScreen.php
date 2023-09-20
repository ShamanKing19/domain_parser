<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;

class DomainListScreen extends Screen
{
    public function query(): iterable
    {
        $domains =  \App\Models\Domain::with(['emails'])->filters()->defaultSort('id')->paginate(20);
        // TODO: Переделать
        $domains->map(function($domain) {
            $emails = $domain->emails()->get('email')->implode('email', ', ');
            $domain['shit'] = $emails;
        });

        return [
            'domains' => $domains,
        ];
    }

    public function name(): ?string
    {
        return 'DomainList';
    }

    // TODO: Сделать кнопку "Спарсить все на странице"
    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [
            \App\Orchid\Layouts\Domain\DomainListLayout::class
        ];
    }
}
