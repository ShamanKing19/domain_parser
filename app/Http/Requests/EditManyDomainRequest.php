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
            'domains.*.inn' => ['array', 'nullable'],
            'domains.*.inn.*' => ['string']
        ];
    }
}
