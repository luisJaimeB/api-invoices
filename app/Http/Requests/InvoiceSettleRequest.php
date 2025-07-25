<?php

namespace App\Http\Requests;

use App\Rules\ValidSiteId;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class InvoiceSettleRequest extends FormRequest
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
            'id' => ['required', 'integer'],
            'reference' => ['required', 'string', 'exists:invoices,payment_reference'],
            'agreement' => ['required', 'string'],
            'authorization' => ['required', 'string'],
            'receipt' => ['required', 'string'],
            'method' => ['required', 'string'],
            'franchise' => ['required', 'string'],
            'franchiseName' => ['required', 'string'],
            'issuerName' => ['required', 'string'],
            'lastDigits' => ['nullable', 'string'],
            'provider' => ['required', 'string'],
            'internalReference' => ['required', 'integer'],
            'amount' => ['required', 'array'],
            'amount.currency' => ['required', 'string'],
            'amount.total' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date_format:Y-m-d\TH:i:sP'],
            'channel' => ['required', 'string'],
            'paymentMethod' => ['required', 'string'],
            'location' => ['required', 'string'],
            'requestId' => ['required', 'integer'],
            'locale' => ['required', 'string', 'in:es_CO,en'],
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
