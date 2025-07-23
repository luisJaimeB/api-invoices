<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Collection;

class InvoiceStatusService
{
    public function searchByIdAndReference(array $data): Collection
    {
        return Invoice::where('id', $data['id'])
            ->where('payment_reference', $data['reference'])
            ->get();
    }

    public function changeStatus($invoice, bool $revoke): array
    {
        $currentStatus = $invoice->status->value;

        if (!$revoke && $currentStatus === 'ACTIVE') {
            // revoke = false, estado ACTIVE → bloquear
            $invoice->status = 'HOLD';
            $invoice->save();
            $message = 'Factura bloqueada correctamente';
        } elseif ($revoke && $currentStatus === 'HOLD') {
            // revoke = true, estado HOLD → activar
            $invoice->status = 'ACTIVE';
            $invoice->save();
            $message = 'Factura activada correctamente';
        } else {
            $message = !$revoke
                ? 'Factura ya está bloqueada o no puede ser bloqueada'
                : 'Factura ya está activa o no puede ser activada';
        }

        return [
            'invoice' => $invoice,
            'message' => $message,
        ];
    }
}
