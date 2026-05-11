<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'title'               => ['sometimes', 'string', 'max:255'],
            'description'         => ['sometimes', 'string'],
            'category_id'         => ['sometimes', 'string'],
            'ministry'            => ['nullable', 'string', 'max:255'],
            'benefits'            => ['sometimes', 'array'],
            'benefits.*'          => ['string'],
            'eligibility_rules'   => ['sometimes', 'array'],
            'eligibility_rules.*.field'    => ['required_with:eligibility_rules', 'string'],
            'eligibility_rules.*.operator' => ['required_with:eligibility_rules', 'in:>=,<=,==,!=,in,not_in'],
            'eligibility_rules.*.value'    => ['required_with:eligibility_rules'],
            'eligibility_rules.*.label'    => ['nullable', 'string'],
            'required_documents'  => ['nullable', 'array'],
            'required_documents.*' => ['string'],
            'application_url'     => ['nullable', 'url'],
            'tags'                => ['nullable', 'array'],
            'tags.*'              => ['string'],
            'is_active'           => ['boolean'],
        ];
    }
}
