<?php

namespace App\Orchid\Layouts\ProcessingStatus;

use App\Models\ProcessingStatus;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ProcessingStatusListLayout extends Table
{
    protected $target = 'statuses';

    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID')->render(function (ProcessingStatus $type) {
                return Link::make($type->id)->route('platform.processing-statuses.detail', $type->id);
            })->sort(),
            TD::make('name', 'Статус')->render(function (ProcessingStatus $type) {
                return Link::make($type->name)->route('platform.processing-statuses.detail', $type->id);
            })->sort(),
        ];
    }
}
