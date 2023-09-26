<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditManyDomainRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'domains' => ['required', 'array'],
            'domains.*.id' => ['int'],
            'domains.*.domain' => ['string'],
            'domains.*.real_domain' => ['string', 'nullable'],
            'domains.*.status' => ['integer', 'nullable'],
            'domains.*.cms' => ['string', 'nullable'],
            'domains.*.title' => ['string', 'nullable'],
            'domains.*.description' => ['string', 'nullable'],
            'domains.*.keywords' => ['string', 'nullable'],
            'domains.*.ip' => ['string', 'nullable'],
            'domains.*.country' => ['string', 'nullable'],
            'domains.*.city' => ['string', 'nullable'],
            'domains.*.hosting' => ['string', 'nullable'],
            'domains.*.has_ssl' => ['bool', 'nullable'],
            'domains.*.has_https_redirect' => ['bool', 'nullable'],
            'domains.*.has_catalog' => ['bool', 'nullable'],
            'domains.*.has_basket' => ['bool', 'nullable'],
            'domains.*.phones' => ['array', 'nullable'],
            'domains.*.phones.*' => ['string', 'nullable'],
            'domains.*.emails' => ['array', 'nullable'],
            'domains.*.emails.*' => ['string', 'email:rfc', 'nullable'],

            'domains.*.companies' => ['array', 'nullable'],
            'domains.*.companies.*.inn' => ['string'],
            'domains.*.companies.*.name' => ['string'],
            'domains.*.companies.*.type' => ['string'],
            'domains.*.companies.*.region' => ['string'],
            'domains.*.companies.*.city' => ['string'],
            'domains.*.companies.*.address' => ['string'],
            'domains.*.companies.*.post_index' => ['string'],
            'domains.*.companies.*.registration_date' => ['date_format:Y-m-d'],
            'domains.*.companies.*.boss_name' => ['string'],
            'domains.*.companies.*.boss_post' => ['string'],
            'domains.*.companies.*.authorized_capital_type' => ['string'],
            'domains.*.companies.*.authorized_capital_amount' => ['int'],
            'domains.*.companies.*.registry_date' => ['date_format:Y-m-d'],
            'domains.*.companies.*.registry_category' => ['int'],
            'domains.*.companies.*.employees_count' => ['int'],
            'domains.*.companies.*.main_activity' => ['string'],
            'domains.*.companies.*.last_finance_year' => ['int'],

            'domains.*.companies.*.finances.*.year' => ['int'],
            'domains.*.companies.*.finances.*.income' => ['numeric'],
            'domains.*.companies.*.finances.*.outcome' => ['numeric'],
            'domains.*.companies.*.finances.*.profit' => ['numeric'],
        ];
    }
}
