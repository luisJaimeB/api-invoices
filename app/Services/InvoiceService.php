<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Collection;

class InvoiceService
{
    public function searchByDebtorDocument(string $document): Collection
    {
        return Invoice::where('debtor_document', $document)->get();
    }

    public function transform(Invoice $invoice): array
    {
        return [
            'id' => $invoice->id,
            'status' => $invoice->status->value,
            'debtor' =>[
                'document' => $invoice->debtor_document,
                'document_type' => $invoice->debtor_document_type->value,
                'name' => $invoice->debtor_name,
                'surname' => $invoice->debtor_surname,
                'email' => $invoice->debtor_email,
            ],
            'payment' => [
                'reference' => $invoice->payment_reference,
                'description' => $invoice->payment_description,
                'amount' => [
                    'currency' => $invoice->payment_currency->value,
                    'total' => $invoice->payment_total,
                ],
                'allow_partial' => $invoice->payment_allow_partial,
                'subscribe' => $invoice->payment_subscribe,
            ],
            'altReference' => $invoice->alt_reference,
            'createdAt' => $invoice->created_at->toIso8601String(),
            'expirationDate' => $invoice->expiration_date ? $invoice->expiration_date->toIso8601String() : null,
        ];
    }
}
