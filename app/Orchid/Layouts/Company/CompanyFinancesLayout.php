<?php

namespace App\Orchid\Layouts\Company;

use App\Models\Company;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class CompanyFinancesLayout extends Table
{
    public function __construct(Company $company)
    {
        $this->target = 'company_' . $company->inn;
    }

    protected function columns(): iterable
    {
        return [
            TD::make('year', 'Год'),
            TD::make('income', 'Выручка'),
            TD::make('outcome', 'Расходы'),
            TD::make('profit', 'Чистая прибыль')
        ];
    }
}
