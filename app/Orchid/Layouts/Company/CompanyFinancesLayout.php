<?php

namespace App\Orchid\Layouts\Company;

use App\Models\Domain;
use Orchid\Screen\Field;
use Orchid\Screen\Layouts\Rows;

class CompanyFinancesLayout extends Rows
{
    protected function fields(): iterable
    {
        /** @var Domain $domain */
        $domain = $this->query->get('domain');
        $companyList = $domain->companies->load('financeYears');
        // TODO: Доделать
        return [];
    }
}
