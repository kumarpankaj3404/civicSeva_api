<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'title'               => ['required', 'string', 'max:255'],
            'description'         => ['required', 'string'],
            'category_id'         => ['required', 'string'],
            'ministry'            => ['nullable', 'string', 'max:255'],
            'benefits'            => ['required', 'array'],
            'benefits.*'          => ['string'],
            'eligibility_rules'   => ['required', 'array'],
            'eligibility_rules.*.field'    => ['required', 'string'],
            'eligibility_rules.*.operator' => ['required', 'in:>=,<=,==,!=,in,not_in'],
            'eligibility_rules.*.value'    => ['required'],
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
