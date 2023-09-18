<?php

namespace App\Orchid\Layouts;

use App\Models\Domain;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class DomainLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'domains';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id'),
            TD::make('domain', 'Домен'),
            TD::make('real_domain')->render(function(Domain $domain) {
                return isset($domain['real_domain']) ? "<a href='$domain[real_domain]' target='_blank'>$domain[real_domain]</a>" : '';
            }),
            TD::make('status'),
            TD::make('cms')->filter(Input::make()),
            TD::make('title'),
//            TD::make('description'),
//            TD::make('keywords'),
            TD::make('has_ssl')->filter(Input::make()),
            TD::make('has_https_redirect')->filter(Input::make()),
            TD::make('has_catalog')->filter(Input::make()),
            TD::make('has_basket')->filter(Input::make()),
            TD::make('shit', 'Почты'),
        ];
    }
}
