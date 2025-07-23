<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\InvoiceSearchRequest;

class InvoiceController extends Controller
{
    private AuthService $authService;
    private InvoiceService $invoiceService;

    public function __construct(AuthService $authService, InvoiceService $invoiceService)
    {
        $this->authService = $authService;
        $this->invoiceService = $invoiceService;
    }

    public function search(InvoiceSearchRequest $request): JsonResponse
    {
        $auth = $request->input('auth');
        $searchValue = $request->input('searchValue');

        if (!$this->authService->validateCredentials($auth)) {
            return $this->authFailed();
        }

        $invoices = $this->invoiceService->searchByDebtorDocument($searchValue);

        $data = $invoices->map(fn($invoice) => $this->invoiceService->transform($invoice));

        return response()->json([
            'status' => [
                'status' => 'OK',
                'reason' => '00',
                'message' => 'La petición se ha procesado correctamente',
                'date' => now()->toIso8601String(),
            ],
            'data' => $data,
        ]);
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
