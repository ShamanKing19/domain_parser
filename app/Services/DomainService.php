<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Company\FinanceYear;
use App\Models\Domain;
use Illuminate\Support\Facades\Date;

class DomainService
{

    /**
     * Создание записи
     *
     * @param array $fields
     *
     * @return Domain|false
     */
    public function create(array $fields)
    {
        $fields = $this->truncateStrings($fields);
        $domain = \App\Models\Domain::create($fields);
        if(is_null($domain)) {
            return false;
        }

        if(isset($fields['phones'])) {
            $this->attachPhones($domain, $fields['phones']);
        }

        if(isset($fields['emails'])) {
            $this->attachEmails($domain, $fields['emails']);
        }

        if(isset($fields['inn'])) {
            $this->attachCompanies($domain, $fields['inn']);
        }

        return $domain;
    }

    /**
     * Создание сразу нескольких записей
     *
     * @param array $fieldsList
     *
     * @return bool
     */
    public function createMany(array $fieldsList)
    {
        $now = Date::now();
        foreach($fieldsList as $key => $fields) {
            $fieldsList[$key] = $this->truncateStrings($fields);
            $fieldsList[$key]['updated_at'] = $now;
        }

        return \App\Models\Domain::insert($fieldsList);
    }

    /**
     * Обновление записи с доменом
     *
     * @param int $id
     * @param array $fields
     *
     * @return Domain|false
     */
    public function update(int $id, array $fields)
    {
        $domain = Domain::find($id);
        if(is_null($domain)) {
            return false;
        }

        $fields['updated_at'] = Date::now();
        $fields = $this->truncateStrings($fields);

        if(!empty($fields['phones'])) {
            $domain->phones()->delete();
            $this->attachPhones($domain, $fields['phones']);
        }

        if(!empty($fields['emails'])) {
            $domain->emails()->delete();
            $this->attachEmails($domain, $fields['emails']);
        }

        if(!empty($fields['companies'])) {
            $domain->companies()->delete();
            $this->attachCompanies($domain, $fields['companies']);
        }

        $domain->update($fields);

        return $domain;
    }


    /**
     * Прикрепление номеров телефонов
     *
     * @param Domain $domain
     * @param array $phones
     *
     * @return void
     */
    public function attachPhones(Domain $domain, array $phones) : void
    {
        $phoneRows = collect($phones)->map(function($item) {
            return ['phone' => \App\Helpers::cleanPhoneString($item)];
        })->unique();

        $domain->phones()->createMany($phoneRows);
    }

    /**
     * Прикрепление email'ов
     *
     * @param Domain $domain
     * @param array $emails
     *
     * @return void
     */
    public function attachEmails(Domain $domain, array $emails) : void
    {
        $emailList = collect(array_unique($emails))->map(fn($item) => ['email' => $item]);
        $domain->emails()->createMany($emailList);
    }

    /**
     * Прикрепление ИНН'ов
     *
     * @param Domain $domain
     * @param array $companyList
     *
     * @return void
     */
    public function attachCompanies(Domain $domain, array $companyList) : void
    {
        foreach($companyList as $companyFields) {
            $inn = $companyFields['inn'];
            $companyRow = Company::firstOrCreate(['inn' => $inn]);
            if($companyRow->wasRecentlyCreated) {
                $newCompanyIdList = [];
            } else {
                $companyFields['updated_at'] = Date::now();
                $companyRow->update($companyFields);
            }

            if(!empty($companyFields['finances'])) {
                $this->attachFinancesToCompany($companyRow, $companyFields['finances']);
            }
        }

        if(isset($newCompanyIdList)) {
            $domain->companies()->attach($newCompanyIdList);
        }
    }

    /**
     * Прикрепление периодов отчётности к компании
     * TODO: Рефакторнуть
     *
     * @param Company $company
     * @param array $financesFields
     *
     * @return void
     */
    public function attachFinancesToCompany(Company $company, array $financesFields) : void
    {
        foreach($financesFields as $financeYear) {
            $year = $company->financeYears()->where([
                'year' => $financeYear['year']
            ]);

            if($year->get()->isNotEmpty()) {
                $year->update($financeYear);
            } else {
                $financeYear['inn_id'] = $company->id;
                FinanceYear::create($financeYear);
            }
        }
    }

    /**
     * Обрезание строк для
     *
     * @param array $fields
     *
     * @return array
     */
    public function truncateStrings(array $fields) : array
    {
        $stringFields = ['title', 'keywords'];
        foreach($stringFields as $field) {
            if(!empty($fields[$field])) {
                $fields[$field] = \App\Helpers::truncate($fields[$field], 250);
            }
        }

        return $fields;
    }
}
