<?php

namespace Database\Factories;

use App\Models\AttestationTravail;
use App\Models\Fonctionnaire;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttestationTravailFactory extends Factory
{
    protected $model = AttestationTravail::class;

    public function definition(): array
    {
        return [
            'fonctionnaire_id' => Fonctionnaire::factory(),
            'date_demande' => now(),
            'langue' => $this->faker->randomElement(['fr', 'ar']),
            'status' => $this->faker->randomElement(['en cours', 'signé', 'rejeté']),
            'raison_rejection' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'en cours',
            ];
        });
    }

    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'signé',
            ];
        });
    }

    public function rejected()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejeté',
                'raison_rejection' => $this->faker->sentence,
            ];
        });
    }
}
