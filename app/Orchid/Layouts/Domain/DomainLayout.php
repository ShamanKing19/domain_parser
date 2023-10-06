<?php

namespace App\Orchid\Layouts\Domain;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class DomainLayout extends Rows
{
    protected function fields(): iterable
    {
        return [
            Label::make('domain.id')
                ->horizontal()
                ->title('id'),

            Input::make('domain.domain')
                ->horizontal()
                ->title('Домен'),

            Label::make('domain.real_domain')
                ->horizontal()
                ->title('Реальная ссылка'),

            Label::make('domain.status')
                ->horizontal()
                ->title('HTTP Статус'),

            Label::make('domain.cms')
                ->horizontal()
                ->title('CMS / Framework'),

            Select::make('domain.processing_status_id')
                ->fromModel(\App\Models\ProcessingStatus::class, 'name')
                ->empty('Не обработан')
                ->horizontal()
                ->title('Статус обработки'),

            Select::make('domain.type_id')
                ->fromModel(\App\Models\WebsiteType::class, 'name')
                ->empty('Не определён')
                ->horizontal()
                ->title('Тип сайта'),

            Label::make('domain.title')
                ->horizontal()
                ->title('Заголовок'),

            Label::make('domain.description')
                ->horizontal()
                ->title('Описание'),

            Label::make('domain.keywords')
                ->horizontal()
                ->title('Ключевые слова'),

            Label::make('domain.ip')
                ->horizontal()
                ->title('IP'),

            Label::make('domain.country')
                ->horizontal()
                ->title('Страна'),

            Label::make('domain.city')
                ->horizontal()
                ->title('Город'),

            Label::make('domain.hosting')
                ->horizontal()
                ->title('Хостинг'),

            Label::make('domain.has_ssl')
                ->horizontal()
                ->title('Есть SSL'),

            Label::make('domain.has_https_redirect')
                ->horizontal()
                ->title('Есть редирект на https'),

            Label::make('domain.has_catalog')
                ->horizontal()
                ->title('Есть каталог'),

            Label::make('domain.has_basket')
                ->horizontal()
                ->title('Есть корзина'),

            Label::make('domain.updated_at')
                ->horizontal()
                ->title('Последнее обновление'),
        ];
    }
}
