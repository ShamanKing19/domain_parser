<?php

namespace App\Orchid\Screens;

use App\Models\Domain;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class DomainScreen extends Screen
{
    public function query(Domain $domain): iterable
    {
        $this->name = $domain->domain;

        return [
            'domain' => $domain
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Назад')
                ->route('platform.domains.list')
                ->icon('arrow-left')
                ->class('btn btn-link mr-10'),

            // TODO: Запускать парсер и обновлять страницу
            Button::make('Обновить данные')
                ->icon('')
                ->method('update'),

            // Implement
            Button::make('Удалить')
                ->icon('trash')
                ->method('remove')
                ->confirm('Запись удалится из базы данных'),

            // Implement
            Button::make('Сохранить')
                ->icon('check')
                ->method('save'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::columns([
                new \App\Orchid\Layouts\DomainLayout()
                // TODO: Сделать layout для отображения инфы о компании
            ])
        ];
    }
}
