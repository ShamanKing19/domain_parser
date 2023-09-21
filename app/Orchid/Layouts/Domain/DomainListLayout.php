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
            TD::make('id')
                ->render(function(Domain $domain) {
                    return "<a href='/domains/$domain->id'>$domain->id</a>";
                })
                ->filter(Input::make())
                ->sort(),

            TD::make('domain', 'Домен')
                ->render(function(Domain $domain) {
                    return "<a href='/domains/$domain->id'>$domain->domain</a>";
                }),

            TD::make('real_domain', 'Конечная ссылка')
                ->render(function (Domain $domain) {
                    return isset($domain['real_domain']) ? "<a href='$domain->real_domain' target='_blank'>$domain->real_domain</a>" : '';
                }),

            TD::make('status')->sort(),

            TD::make('cms', 'CMS')
                ->filter(Input::make())
                ->sort(),

            TD::make('title', 'Заголовок')
                ->filter(Input::make()),

            TD::make('description', 'Описание')
                ->filter(Input::make())
                ->defaultHidden(),

            TD::make('keywords', 'Ключевые слова')
                ->filter(Input::make())
                ->defaultHidden(),

            TD::make('has_ssl', 'SSL')
                ->filter(Input::make())
                ->sort(),

            TD::make('has_https_redirect', 'HTTPS редирект')
                ->filter(Input::make())
                ->sort(),

            TD::make('has_catalog', 'Каталог')
                ->filter(Input::make())
                ->sort(),

            TD::make('has_basket', 'Корзина')
                ->filter(Input::make())
                ->sort(),

            TD::make('emails_string', 'Почты'),
        ];
    }
}
