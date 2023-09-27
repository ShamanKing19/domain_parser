<?php

namespace App\Orchid\Screens;

use App\Models\WebsiteType;
use App\Orchid\Layouts\WebsiteType\WebsiteTypeListLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class WebsiteTypeListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'types' => WebsiteType::filters()->defaultSort('id')->paginate(20)
        ];
    }

    public function name(): ?string
    {
        return 'Типы сайтов';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить')
                ->icon('plus-circle')
                ->route('platform.website.types.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            WebsiteTypeListLayout::class
        ];
    }
}
