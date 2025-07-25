<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'auth' => ['required', 'array'],
            'debtor_document' => ['required', 'string'],
            'debtor_document_type' => ['required', 'string'],
            'debtor_name' => ['required', 'string'],
            'debtor_surname' => ['required', 'string'],
            'debtor_email' => ['required', 'string', 'email'],

            'payment_reference' => ['required', 'string'],
            'payment_description' => ['required', 'string'],
            'payment_currency' => ['required', 'string'],
            'payment_total' => ['required', 'numeric', 'min:0.01'],
            'payment_allow_partial' => ['required', 'boolean'],
            'payment_subscribe' => ['required', 'boolean'],

            'alt_reference' => ['nullable', 'string'],
            'expiration_date' => ['required', 'date_format:Y-m-d\TH:i:sP'],
        ];
    }
}
