<?php

namespace App\Http\Requests;

use App\Rules\ValidSiteId;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class InvoiceHoldRequest extends FormRequest
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
            'id' => ['required', 'exists:invoices,id'],
            'revoke' => ['required', 'boolean'],
            'reference' => ['required', 'exists:invoices,payment_reference'],
            'siteId' => ['required', new ValidSiteId()],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => [
                'status' => 'FAILED',
                'reason' => 422,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors(),
                'date' => now()->toIso8601String(),
            ]
        ], 422));
    }
}
