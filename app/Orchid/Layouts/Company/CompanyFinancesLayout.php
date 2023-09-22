<?php

namespace App\Orchid\Layouts\Company;

use App\Models\Company;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Repository;

class CompanyFinancesLayout extends Table
{
    private Company $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
        $this->target = 'company_' . $this->company->inn;
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
