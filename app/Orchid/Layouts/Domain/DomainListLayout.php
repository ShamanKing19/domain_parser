<?php

namespace App\Orchid\Layouts\Domain;

use App\Models\Domain;
use App\Repositories\DomainRepository;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class DomainListLayout extends Table
{
    protected $target = 'domains';
    private array $cmsList;
    private mixed $statusList;

    public function __construct(DomainRepository $repository)
    {
        $cmsList = $repository->getCmsList();
        foreach($cmsList as $cms) {
            $this->cmsList[$cms] = $cms;
        }

        $statusList = $repository->getStatusList();
        foreach($statusList as $status) {
            $this->statusList[$status] = $status;
        }
    }


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

            TD::make('status', 'Статус')->filter(TD::FILTER_SELECT, $this->statusList)->sort(),

            TD::make('cms', 'CMS')
                ->filter(TD::FILTER_SELECT, $this->cmsList)
                ->sort(),

            TD::make('title', 'Заголовок')
                ->render(function(Domain $domain) {
                    return $domain->title ? \App\Helpers::truncate($domain->title, 60, '...') : '';
                })
                ->filter(Input::make()),

            TD::make('description', 'Описание')
                ->render(function(Domain $domain) {
                    return $domain->description ? \App\Helpers::truncate($domain->description, 60, '...') : '';
                })
                ->filter(Input::make())
                ->defaultHidden(),

            TD::make('keywords', 'Ключевые слова')
                ->render(function(Domain $domain) {
                    return $domain->keywords ? \App\Helpers::truncate($domain->keywords, 60, '...') : '';
                })
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

            TD::make('emails_string', 'Почты')
                ->render(function(Domain $domain) {
                    return $domain->emails_string ? \App\Helpers::truncate($domain->emails_string, 60, '...') : '';
                }),
        ];
    }
}
