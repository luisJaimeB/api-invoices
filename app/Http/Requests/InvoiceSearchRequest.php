<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceSearchRequest extends FormRequest
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
            'auth' => 'required|array',
            'auth.login' => 'required|string',
            'auth.tranKey' => 'required|string',
            'auth.nonce' => 'required|string',
            'auth.seed' => 'required|string',
            'searchValue' => 'required|string',
        ];
    }
}
