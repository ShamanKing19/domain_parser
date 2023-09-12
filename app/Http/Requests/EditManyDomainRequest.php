<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditManyDomainRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'domains' => ['required', 'array'],
            'domains.*.id' => ['required', 'exists:\App\Models\Domain,id'],
            'domains.*.real_domain' => ['string'],
            'domains.*.status' => ['integer'],
            'domains.*.cms' => ['string'],
            'domains.*.title' => ['string'],
            'domains.*.description' => ['string'],
            'domains.*.keywords' => ['string'],
            'domains.*.ip' => ['string'],
            'domains.*.country' => ['string'],
            'domains.*.city' => ['string'],
            'domains.*.hosting' => ['string'],
            'domains.*.has_ssl' => ['bool'],
            'domains.*.has_https_redirect' => ['bool'],
            'domains.*.has_catalog' => ['bool'],
            'domains.*.has_basket' => ['bool'],
            'domains.*.phones' => ['array'],
            'domains.*.phones.*' => ['string'],
            'domains.*.emails' => ['array'],
            'domains.*.emails.*' => ['string', 'email:rfc'],
            'domains.*.inn' => ['array'],
            'domains.*.inn.*' => ['unique:\App\Models\Company,inn']
        ];
    }
}
