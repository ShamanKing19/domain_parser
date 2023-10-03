<?php

namespace App\Orchid\Layouts\Domain;

use App\Orchid\Fields\MultiInput;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class ImportDomainsLayout extends Rows
{
    protected function fields(): iterable
    {
        return [
            Input::make('file')
                ->type('file')
                ->accept('.txt,.csv,.xls,.xlsx')
                ->title('Файл со списком доменов'),

            MultiInput::make('domain_list')->title('Домены')
        ];
    }
}
