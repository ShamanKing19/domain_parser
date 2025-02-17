<?php

namespace App\Orchid\Layouts\WebsiteType;

use App\Models\WebsiteType;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class WebsiteTypeListLayout extends Table
{
    protected $target = 'types';

    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID')->render(function (WebsiteType $type) {
                return Link::make($type->id)->route('platform.website-types.detail', $type->id);
            })->sort(),
            TD::make('name', 'Тип')->render(function (WebsiteType $type) {
                return Link::make($type->name)->route('platform.website-types.detail', $type->id);
            })->sort(),
        ];
    }
}
