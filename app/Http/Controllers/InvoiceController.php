<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function search(Request $request)
    {
        $auth = $request->input('auth');
        $searchValue = $request->input('searchValue');

        if (!$auth || !$searchValue) {
            return $this->authFailed();
        }

        $login = $auth['login'] ?? null;
        $tranKey = $auth['tranKey'] ?? null;
        $nonce = $auth['nonce'] ?? null;
        $seed = $auth['seed'] ?? null;

        if (!$login || !$tranKey || !$nonce || !$seed) {
            return $this->authFailed();
        }

        $secretKey = config('services.api_auth.secret');

        // Decodificar nonce (base64)
        $decodedNonce = base64_decode($nonce);

        $rawString = $decodedNonce . $seed . $secretKey;
        $hash = hash('sha256', $rawString, true);
        $expectedTranKey = base64_encode($hash);

        if ($login !== config('services.api_auth.login') || $tranKey !== $expectedTranKey) {
            return $this->authFailed();
        }

        Invoice::factory()->count(3)->create([
            'debtor_document' => $searchValue,
        ]);

        $invoices = Invoice::where('debtor_document', $searchValue)->get();
        if ($invoices->isEmpty()) {
            return response()->json([
            'status' => [
                'status' => 'OK',
                'reason' => '00',
                'message' => 'La petición se ha procesado correctamente',
                'date' => now()->toIso8601String(),
            ],
            'data' => [],
            ])->setStatusCode(200);
        };

        $data = $invoices->map(function ($invoice) {
            return [
                'id' => $invoice->id,
                'status' => $invoice->status->value,
                'debtor' => [
                    'document' => $invoice->debtor_document,
                    'documentType' => $invoice->debtor_document_type->value,
                    'name' => $invoice->debtor_name,
                    'surname' => $invoice->debtor_surname,
                    'email' => $invoice->debtor_email,
                ],
                'payment' => [
                    'reference' => $invoice->payment_reference,
                    'description' => $invoice->payment_description,
                    'amount' => [
                        'currency' => $invoice->payment_currency->value,
                        'total' => floatval($invoice->payment_total),
                    ],
                    'allowPartial' => $invoice->payment_allow_partial,
                    'subscribe' => $invoice->payment_subscribe,
                ],
                'paymentMethod' => ['visa', 'pse'], // puedes cambiar esto si es dinámico
                'altReference' => $invoice->alt_reference,
                'createdAt' => $invoice->created_at,
                'expirationDate' => $invoice->expiration_date,
            ];
        });

        return response()->json([
            'status' => [
                'status' => 'OK',
                'reason' => '00',
                'message' => 'La petición se ha procesado correctamente',
                'date' => now()->toIso8601String(),
            ],
            'data' => $data,
        ])->setStatusCode(200);
    }

    private function authFailed()
    {
        return response()->json([
            'status' => [
                'status' => 'FAILED',
                'reason' => 401,
                'message' => 'Autenticación fallida',
                'date' => now()->toIso8601String()
            ]
        ], 401);
    }
}
