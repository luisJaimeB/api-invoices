<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use App\Services\InvoiceCreateService;
use App\Services\InvoiceSettleService;
use App\Services\InvoiceStatusService;
use App\Http\Requests\InvoiceHoldRequest;
use App\Http\Requests\InvoiceCreateRequest;
use App\Http\Requests\InvoiceSearchRequest;
use App\Http\Requests\InvoiceSettleRequest;

class InvoiceController extends Controller
{
    private AuthService $authService;
    private InvoiceService $invoiceService;
    private InvoiceStatusService $invoiceStatusService;
    private InvoiceSettleService $invoiceSettleService;
    private InvoiceCreateService $invoiceCreateService;

    public function __construct(
        AuthService $authService,
        InvoiceService $invoiceService,
        InvoiceStatusService $invoiceStatusService,
        InvoiceSettleService $invoiceSettleService,
        InvoiceCreateService $invoiceCreateService
    ) {
        $this->authService = $authService;
        $this->invoiceService = $invoiceService;
        $this->invoiceStatusService = $invoiceStatusService;
        $this->invoiceSettleService = $invoiceSettleService;
        $this->invoiceCreateService = $invoiceCreateService;
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

    public function revoke(InvoiceHoldRequest $request): JsonResponse
    {
        $auth = $request->input('auth');

        if (!$this->authService->validateCredentials($auth)) {
            return $this->authFailed();
        }

        $revoke = $request->boolean('revoke');
        $id = $request->input('id');
        $reference = $request->input('reference');

        $invoice = $this->invoiceStatusService->searchByIdAndReference([
            'id' => $id,
            'reference' => $reference,
        ])->first();

        if (!$invoice) {
            return response()->json([
                'status' => [
                    'status' => 'FAILED',
                    'reason' => 404,
                    'message' => 'Factura no encontrada',
                    'date' => now()->toIso8601String(),
                ]
            ], 404);
        }

        $result = $this->invoiceStatusService->changeStatus($invoice, $revoke);

        return response()->json([
            'status' => [
                'status' => 'OK',
                'reason' => '00',
                'message' => $result['message'],
                'date' => now()->toIso8601String(),
            ],
            'data' => $this->invoiceService->transform($result['invoice']),
        ]);
    }

    public function settle(InvoiceSettleRequest $request): JsonResponse
    {
        $auth = $request->input('auth');

        if (!$this->authService->validateCredentials($auth)) {
            return $this->authFailed();
        }

        $result = $this->invoiceSettleService->searchByIdAndReference([
            'id' => $request->input('id'),
            'reference' => $request->input('reference'),
        ]);

        if (!$result['found']) {
            return response()->json([
                'status' => [
                    'status' => 'FAILED',
                    'reason' => $result['reason'],
                    'message' => $result['message'],
                    'date' => now()->toIso8601String(),
                ]
            ], 404);
        }

        $settleResult = $this->invoiceSettleService->settleInvoice($result['invoice'], $request->validated());

        return response()->json([
            'status' => [
                'status' => 'OK',
                'reason' => '00',
                'message' => 'la petición se ha procesado correctamente',
                'date' => now()->toIso8601String(),
            ],
            'receipt' => $settleResult['receipt'],
        ]);
    }

    public function store(InvoiceCreateRequest $request)
    {
        $invoice = $this->invoiceCreateService->create($request->validated());

        return response()->json([
            'status' => [
                'status' => 'OK',
                'reason' => '00',
                'message' => 'Factura creada correctamente',
                'date' => now()->toIso8601String(),
            ],
            'data' => $this->invoiceService->transform($invoice),
        ], 201);
    }

    private function authFailed(): JsonResponse
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
