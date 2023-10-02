<?php

namespace App\Orchid\Screens\ProcessingStatus;

use App\Orchid\Layouts\ProcessingStatus\ProcessingStatusListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class ProcessingStatusListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'statuses' => \App\Models\ProcessingStatus::filters()->defaultSort('id')->paginate(20)
        ];
    }

    public function name(): ?string
    {
        return 'Статусы обработки доменов';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить')
                ->icon('plus-circle')
                ->route('platform.processing-statuses.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            ProcessingStatusListLayout::class
        ];
    }
}
