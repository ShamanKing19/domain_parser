<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditDomainRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'exists:\App\Models\Domain,id'],
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
            'phones' => ['array', 'nullable'],
            'phones.*' => ['string', 'nullable'],
            'emails' => ['array', 'nullable'],
            'emails.*' => ['string', 'email:rfc', 'nullable'],
            'inn' => ['array', 'nullable'],
            'inn.*' => ['unique:\App\Models\Company,inn', 'nullable']
        ];
    }
}
