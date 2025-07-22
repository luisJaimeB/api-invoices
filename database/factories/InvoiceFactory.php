<?php

namespace Database\Factories;

use App\Enums\Currency;
use App\Enums\DocumentType;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => fake()->randomElement(Status::cases())->value,

            'debtor_document' => $this->faker->numerify('10400####'),
            'debtor_document_type' => fake()->randomElement(DocumentType::cases())->value,
            'debtor_name' => fake()->firstName(),
            'debtor_surname' => fake()->lastName(),
            'debtor_email' => fake()->unique()->safeEmail(),

            'payment_reference' => strtoupper(Str::random(8)),
            'payment_description' => fake()->sentence(),
            'payment_currency' => Currency::COP->value,
            'payment_total' => fake()->randomFloat(2, 10000, 500000),
            'payment_allow_partial' => fake()->boolean(),
            'payment_subscribe' => fake()->boolean(),

            'alt_reference' => fake()->boolean(30) ? strtoupper(Str::random(6)) : null,
            'expiration_date' => fake()->dateTimeBetween('now', '+30 days'),
        ];
    }
}
