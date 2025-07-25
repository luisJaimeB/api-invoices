<?php

namespace App\Services;

use App\Models\Invoice;

class InvoiceCreateService
{
    public function create(array $data): Invoice
    {
        $data['status'] = \App\Enums\Status::ACTIVE->value;
        return Invoice::create($data);
    }
}
