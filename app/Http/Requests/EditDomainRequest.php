<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditDomainRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'id' => ['int'],
            'domain' => ['string'],
            'real_domain' => ['string', 'nullable'],
            'status' => ['integer', 'nullable'],
            'cms' => ['string', 'nullable'],
            'title' => ['string', 'nullable'],
            'description' => ['string', 'nullable'],
            'keywords' => ['string', 'nullable'],
            'ip' => ['string', 'nullable'],
            'country' => ['string', 'nullable'],
            'city' => ['string', 'nullable'],
            'hosting' => ['string', 'nullable'],
            'has_ssl' => ['bool', 'nullable'],
            'has_https_redirect' => ['bool', 'nullable'],
            'has_catalog' => ['bool', 'nullable'],
            'has_basket' => ['bool', 'nullable'],
            'type_id' => ['int', 'exists:'.\App\Models\WebsiteType::class.',id'],
            'auto_type_id' => ['int', 'exists:'.\App\Models\WebsiteType::class.',id'],
            'phones' => ['array', 'nullable'],
            'phones.*' => ['string', 'nullable'],
            'emails' => ['array', 'nullable'],
            'emails.*' => ['string', 'email:rfc', 'nullable'],

            'companies' => ['array', 'nullable'],
            'companies.*.inn' => ['string'],
            'companies.*.name' => ['string'],
            'companies.*.type' => ['string'],
            'companies.*.region' => ['string'],
            'companies.*.city' => ['string'],
            'companies.*.address' => ['string'],
            'companies.*.post_index' => ['string'],
            'companies.*.registration_date' => ['date_format:Y-m-d'],
            'companies.*.boss_name' => ['string'],
            'companies.*.boss_post' => ['string'],
            'companies.*.authorized_capital_type' => ['string'],
            'companies.*.authorized_capital_amount' => ['int'],
            'companies.*.registry_date' => ['date_format:Y-m-d'],
            'companies.*.registry_category' => ['int'],
            'companies.*.employees_count' => ['int'],
            'companies.*.main_activity' => ['string'],
            'companies.*.last_finance_year' => ['int'],

            'companies.*.finances.*.year' => ['int'],
            'companies.*.finances.*.income' => ['numeric'],
            'companies.*.finances.*.outcome' => ['numeric'],
            'companies.*.finances.*.profit' => ['numeric'],
        ];
    }
}
