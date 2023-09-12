<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditDomainRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'exists:\App\Models\Domain,id'],
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
            'phones' => ['array'],
            'phones.*' => ['string'],
            'emails' => ['array'],
            'emails.*' => ['string', 'email:rfc'],
            'inn' => ['array'],
            'inn.*' => ['unique:\App\Models\Company,inn']
        ];
    }
}
