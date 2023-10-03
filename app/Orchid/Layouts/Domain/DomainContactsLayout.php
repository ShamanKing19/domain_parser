<?php

namespace App\Orchid\Layouts\Domain;

use App\Models\Domain;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Layouts\Rows;

class DomainContactsLayout extends Rows
{
//    protected $title = 'Контакты';


    protected function fields(): iterable
    {
        /** @var Domain $domain */
        $domain = $this->query->get('domain');
        $emails = $domain->emails->pluck('email');
        $phones = $domain->phones->pluck('phone');


        $emailRows = $emails->map(fn($email) => Label::make('email')->value($email));
        $phoneRows = $phones->map(fn($phone) => Label::make('phone')->value($phone));

        return [
            Label::make('emails-label')->canSee($emails->isNotEmpty())->title('Почты'),
            ...$emailRows,
            Label::make('phones-label')->canSee($phones->isNotEmpty())->title('Номера телефонов'),
            ...$phoneRows
        ];
    }
}
