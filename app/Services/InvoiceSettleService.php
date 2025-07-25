<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\Invoice;

class InvoiceSettleService
{

    public function searchByIdAndReference(array $data): array
    {
        $invoice = Invoice::where('id', $data['id'])
        ->where('payment_reference', $data['reference'])
        ->first();

        if (!$invoice) {
            return [
                'found' => false,
                'reason' => 'NF',
                'message' => 'No existe la factura con ese identificador',
                'invoice' => null,
            ];
        }

        if ($invoice->status->value === Status::PAID->value) {
            return [
                'found' => false,
                'reason' => 'AP',
                'message' => 'Ya se encuentra pagada',
                'invoice' => null,
            ];
        }

        return [
            'found' => true,
            'invoice' => $invoice,
        ];
    }

    public function settleInvoice($invoice): array
    {
        $invoice->status = Status::PAID;
        $invoice->setted_at = now();
        $invoice->receipt_number = $this->generateReceiptNumber();
        $invoice->save();

        return [
            'receipt' => $invoice->receipt_number,
            'message' => 'Factura liquidada correctamente',
        ];
    }

    private function generateReceiptNumber(): int
    {
        // Obtiene el último número de receipt registrado y suma 1
        $lastReceipt = Invoice::max('receipt_number');
        return $lastReceipt ? $lastReceipt + 1 : 100000; // Empieza en 100000 si no hay ninguno
    }
}
