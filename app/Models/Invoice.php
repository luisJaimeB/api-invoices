<?php

namespace App\Models;

use App\Enums\Currency;
use App\Enums\DocumentType;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'debtor_document',
        'debtor_document_type',
        'debtor_name',
        'debtor_surname',
        'debtor_email',
        'payment_reference',
        'payment_description',
        'payment_currency',
        'payment_total',
        'payment_allow_partial',
        'payment_subscribe',
        'alt_reference',
        'created_at',
        'expiration_date',
    ];

    protected $casts = [
        'status' => Status::class,
        'debtor_document_type' => DocumentType::class,
        'payment_currency' => Currency::class,
        'payment_allow_partial' => 'boolean',
        'payment_subscribe' => 'boolean',
        'created_at' => 'datetime',
        'expiration_date' => 'datetime',
    ];
}
