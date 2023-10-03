<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Company\FinanceYear;
use App\Models\Domain;
use App\Repositories\DomainRepository;
use Illuminate\Support\Facades\Date;

class DomainService
{
    private DomainRepository $repository;

    public function __construct(DomainRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Создание или обновление существующей записи
     *
     * @param array $fields
     *
     * @return Domain|null
     */
    public function createOrUpdate(array $fields) : Domain|null
    {
        if(!empty($fields['id'])) {
            return $this->update($fields['id'], $fields);
        }

        if(empty($fields['domain'])) {
            return null;
        }

        $domain = $this->repository->getByDomain($fields['domain']);
        return $domain ? $this->update($domain->id, $fields) : $this->create($fields);
    }

    /**
     * Создание записи
     *
     * @param array $fields
     *
     * @return Domain|false
     */
    public function create(array $fields)
    {
        $fields = $this->prepareFields($fields);
        $domain = Domain::create($fields);
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
            $fieldsList[$key] = $this->prepareFields($fields);
            $fieldsList[$key]['updated_at'] = $now;
        }

        return Domain::insert($fieldsList);
    }

    /**
     * Обновление записи с доменом
     *
     * @param int $id
     * @param array $fields
     *
     * @return Domain|null
     */
    public function update(int $id, array $fields)
    {
        $domain = Domain::find($id);
        if(is_null($domain)) {
            return null;
        }

        $fields = $this->prepareFields($fields);

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
        $phoneRows = collect($phones)->map(fn($item) => \App\Helpers::cleanPhoneString($item))->unique();
        foreach($phoneRows as $phone) {
            $domain->phones()->firstOrCreate(['phone' => $phone]);
        }
    }

    /**
     * Прикрепление email'ов
     *
     * @param Domain $domain
     * @param array $emailList
     *
     * @return void
     */
    public function attachEmails(Domain $domain, array $emailList) : void
    {
        foreach(array_unique($emailList) as $email) {
            $domain->emails()->firstOrCreate(['email' => $email]);
        }
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
        $newCompanyIdList = [];
        foreach($companyList as $companyFields) {
            $inn = $companyFields['inn'];
            $companyRow = $this->getCompany($inn);
            if($companyRow->wasRecentlyCreated) {
                $newCompanyIdList[] = $companyRow->id;
            }

            $companyFields['updated_at'] = Date::now();
            $companyRow->update($companyFields);

            if(!empty($companyFields['finances'])) {
                $this->attachFinancesToCompany($companyRow, $companyFields['finances']);
            }
        }

        if($newCompanyIdList) {
            $domain->companies()->syncWithoutDetaching($newCompanyIdList);
        }
    }

    /**
     * Создание или получение уже существующей компании
     *
     * @param string $inn
     *
     * @return Company
     */
    private function getCompany(string $inn) : Company
    {
        return Company::firstOrCreate(['inn' => $inn]);
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
     * Подготовка полей к сохранению
     *
     * @param array $fields
     *
     * @return array
     */
    private function prepareFields(array $fields) : array
    {
        $this->truncateStrings($fields);
        $fields['updated_at'] = Date::now();
        if($fields['domain']) {
            $urlInfo = parse_url($fields['domain']);
            $fields['domain'] = $urlInfo['host'] ?? $urlInfo['path'];
        }

        return $fields;
    }

    /**
     * Обрезание строк для
     *
     * @param array $fields
     *
     * @return array
     */
    private function truncateStrings(array $fields) : array
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
