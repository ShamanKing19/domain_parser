<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;

class DomainScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        $domains =  \App\Models\Domain::with(['emails'])->filters()->defaultSort('id')->paginate(20);
        $domains->map(function($domain) {
            $emails = $domain->emails()->get('email')->implode('email', ', ');
            $domain['shit'] = $emails;
        });

        return [
            'domains' => $domains,
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'DomainScreen';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            \App\Orchid\Layouts\DomainLayout::class
        ];
    }
}
