<?php

namespace App\Orchid\Layouts\Company;

use App\Models\Company;
use App\Models\Domain;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Layouts\Rows;

class CompanyLayout extends Rows
{
//    protected $title = 'Компания';

    private Company $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    protected function fields(): iterable
    {
        $company = $this->company;
        return [
            Label::make('inn')->title('ИНН')->value($company->inn),
            Label::make('name')->title('Наименование')->value($company->name),
            Label::make('type')->title('Тип компании')->value($company->type),
            Label::make('segment')->title('Сегмент')->value($company->segment),
            Label::make('region')->title('Регион')->value($company->region),
            Label::make('city')->title('Город')->value($company->city),
            Label::make('address')->title('Адрес')->value($company->address),
            Label::make('post_index')->title('Почтовый индекс')->value($company->post_index),
            Label::make('registration_date')->title('Дата регистрации')->value($company->registration_date),
            Label::make('boss_name')->title('ФИО руководителя')->value($company->boss_name),
            Label::make('boss_post')->title('Должность руководителя')->value($company->boss_post),
            Label::make('authorized_capital_type')->title('Тип капитала')->value($company->authorized_capital_type),
            Label::make('registry_date')->title('Дата внесения в реестр')->value($company->registry_date),
            Label::make('registry_date')->title('Категория')->value($company->registry_date),
            Label::make('employees_count')->title('Количество сотрудников')->value($company->employees_count),
            Label::make('main_activity')->title('Основной тип деятельности')->value($company->main_activity),
            Label::make('last_finance_year')->title('Последний год финансовой отчётности')->value($company->last_finance_year),
            Label::make('updated_at')->title('Дата последнего обновления')->value($company->updated_at)
        ];
    }
}
