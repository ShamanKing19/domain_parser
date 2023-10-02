<?php
namespace App\Orchid\Layouts\ProcessingStatus;

use App\Models\ProcessingStatus;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Layouts\Rows;

class ProcessingStatusLayout extends Rows
{
    protected function fields(): iterable
    {
        /** @var ProcessingStatus $status */
        $status = $this->query->get('status');

        return [
            Label::make('id')->value($status->id)->title('id')->horizontal(),
            Input::make('name')->value($status->name)->title('Статус')->horizontal(),
        ];
    }
}
