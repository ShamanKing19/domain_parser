<?php

namespace App\Orchid\Layouts\Company;

use App\Models\Company;
use Orchid\Screen\Layouts\Table;

class CompanyFinancesLayout extends Table
{
    private Company $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    protected function columns(): iterable
    {
        return [];
    }
}
