<?php

namespace App\Http\Requests;

use App\Models\WebsiteType;

class StoreDomainRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'domain' => ['required', 'unique:\App\Models\Domain,domain'],
            'real_domain' => ['string'],
            'status' => ['integer'],
            'cms' => ['string'],
            'title' => ['string'],
            'description' => ['string'],
            'keywords' => ['string'],
            'ip' => ['string'],
            'country' => ['string'],
            'city' => ['string'],
            'hosting' => ['string'],
            'has_ssl' => ['bool'],
            'has_https_redirect' => ['bool'],
            'has_catalog' => ['bool'],
            'has_basket' => ['bool'],
            'type_id' => ['int', 'exists:' . WebsiteType::class . ',id'],
            'auto_type_id' => ['int', 'exists:' . WebsiteType::class . ',id'],
            'phones' => ['array'],
            'phones.*' => ['string'],
            'emails' => ['array'],
            'emails.*' => ['string', 'email:rfc'],
            'inn' => ['array'],
            'inn.*' => ['unique:\App\Models\Company,inn']
        ];
    }
}
