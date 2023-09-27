<?php

namespace App\Orchid\Layouts\Domain;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class DomainLayout extends Rows
{
//    protected $title = 'Основная информация';

    protected function fields(): iterable
    {
        return [
            'id' => Label::make('domain.id')
                ->horizontal()
                ->title('id'),
            'domain' => Label::make('domain.domain')
                ->horizontal()
                ->title('Домен'),
            'real_domain' => Label::make('domain.real_domain')
                ->horizontal()
                ->title('Реальная ссылка'),
            'status' => Label::make('domain.status')
                ->horizontal()
                ->title('HTTP Статус'),
            'cms' => Label::make('domain.cms')
                ->horizontal()
                ->title('CMS / Framework'),
            'type' => Select::make('domain.type_id')
                ->fromModel(\App\Models\WebsiteType::class, 'name')
                ->empty('Не определён')
                ->horizontal()
                ->title('Тип сайта'),
            'title' => Label::make('domain.title')
                ->horizontal()
                ->title('Заголовок'),
            'description' => Label::make('domain.description')
                ->horizontal()
                ->title('Описание'),
            'keywords' => Label::make('domain.keywords')
                ->horizontal()
                ->title('Ключевые слова'),
            'ip' => Label::make('domain.ip')
                ->horizontal()
                ->title('IP'),
            'country' => Label::make('domain.country')
                ->horizontal()
                ->title('Страна'),
            'city' => Label::make('domain.city')
                ->horizontal()
                ->title('Город'),
            'hosting' => Label::make('domain.hosting')
                ->horizontal()
                ->title('Хостинг'),
            'has_ssl' => Label::make('domain.has_ssl')
                ->horizontal()
                ->title('Есть SSL'),
            'has_https_redirect' => Label::make('domain.has_https_redirect')
                ->horizontal()
                ->title('Есть редирект на https'),
            'has_catalog' => Label::make('domain.has_catalog')
                ->horizontal()
                ->title('Есть каталог'),
            'has_basket' => Label::make('domain.has_basket')
                ->horizontal()
                ->title('Есть корзина'),
            'updated_at' => Label::make('domain.updated_at')
                ->horizontal()
                ->title('Последнее обновление'),
        ];
    }
}
