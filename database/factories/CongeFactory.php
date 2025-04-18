<?php

namespace Database\Factories;

use App\Models\Conge;
use App\Models\Fonctionnaire;
use Illuminate\Database\Eloquent\Factories\Factory;

class CongeFactory extends Factory
{
    protected $model = Conge::class;

    public function definition(): array
    {
        return [
            'fonctionnaire_id' => Fonctionnaire::factory(),
            'type' => $this->faker->randomElement(['annuel', 'exceptionnel']),
            'date_debut' => $this->faker->dateTimeBetween('now', '+30 days'),
            'date_retour' => $this->faker->dateTimeBetween('+31 days', '+60 days'),
            'nombre_jours' => $this->faker->numberBetween(1, 30),
            'status' => $this->faker->randomElement(['en_attente', 'signée', 'rejetée']),
            'date_demande' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'en_attente',
            ];
        });
    }

    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'signée',
            ];
        });
    }

    public function rejected()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejetée',
            ];
        });
    }
}
