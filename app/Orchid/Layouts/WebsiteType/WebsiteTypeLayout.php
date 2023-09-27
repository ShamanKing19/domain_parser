<?php

namespace App\Orchid\Layouts\WebsiteType;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Layouts\Rows;

class WebsiteTypeLayout extends Rows
{
    protected function fields(): iterable
    {
        $type = $this->query->get('type');
        return [
            Label::make('id')->value($type->id)->title('id')->horizontal(),
            Input::make('name')->value($type->name)->title('Тип')->horizontal(),
            // TODO: Выводить инпуты с ключевыми словами, + и -
        ];
    }
}
