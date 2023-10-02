<?php
namespace App\Orchid\Layouts\WebsiteType;

use App\Models\WebsiteType;
use App\Orchid\Fields\MultiInput;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Layouts\Rows;

class WebsiteTypeLayout extends Rows
{
    protected function fields(): iterable
    {
        /** @var WebsiteType $type */
        $type = $this->query->get('type');
        $keywords = $type->keywords()->get()->map(function($item, $key) {
            return [
                'id' => $key,
                'value' => $item->word
            ];
        });

        return [
            Label::make('id')->value($type->id)->title('id')->horizontal(),
            Input::make('name')->value($type->name)->title('Тип')->horizontal(),
            MultiInput::make('keywords')->title('Ключевые слова')
                ->columns($keywords)
                ->horizontal(),
        ];
    }
}
