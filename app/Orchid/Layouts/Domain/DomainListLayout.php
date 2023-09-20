<?php

namespace App\Orchid\Layouts\Domain;

use App\Models\Domain;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class DomainListLayout extends Table
{
    protected $target = 'domains';

    protected function columns() : iterable
    {
        return [
            TD::make('id')->render(function(Domain $domain) {
                return "<a href='/domains/$domain->id'>$domain->id</a>";
            }),
            TD::make('domain', 'Домен')->render(function(Domain $domain) {
                return "<a href='/domains/$domain->id'>$domain->domain</a>";
            }),
            TD::make('real_domain')->render(function (Domain $domain) {
                return isset($domain['real_domain']) ? "<a href='$domain->real_domain' target='_blank'>$domain->real_domain</a>" : '';
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
