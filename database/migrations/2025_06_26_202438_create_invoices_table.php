<?php

use App\Enums\Currency;
use App\Enums\DocumentType;
use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('status')->default(Status::PENDING->value);

            $table->string('debtor_name');
            $table->string('debtor_surname');
            $table->string('debtor_email');
            $table->string('debtor_document');
            $table->string('debtor_document_type')
                ->default(DocumentType::CC->value);

            $table->string('payment_reference');
            $table->string('payment_description');
            $table->string('payment_currency')
                ->default(Currency::COP->value);
            $table->decimal('payment_total', 12, 2);
            $table->boolean('payment_allow_partial')->default(false);
            $table->boolean('payment_subscribe')->default(false);

            $table->string('alt_reference')->nullable();
            $table->timestamp('expiration_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
