<?php

namespace App\Orchid\Screens;

use App\Models\Domain;
use App\Orchid\Layouts\Company\CompanyFinancesLayout;
use App\Orchid\Layouts\Company\CompanyLayout;
use App\Orchid\Layouts\Domain\DomainContactsLayout;
use App\Orchid\Layouts\Domain\DomainLayout;
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
                ->icon('bs.arrow-through-heart')
                ->method('update'),

            // TODO: Implement
//            Button::make('Удалить')
//                ->icon('trash')
//                ->method('remove')
//                ->confirm('Запись удалится из базы данных'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::tabs([
                'Общая информация' => new DomainLayout(),
                'Контакты' => new DomainContactsLayout(),
                'Информация о компании' => [
                    new CompanyLayout(),
                    new CompanyFinancesLayout()
                ],
            ])
        ];
    }
}
