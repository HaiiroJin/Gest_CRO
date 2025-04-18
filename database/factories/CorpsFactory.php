<?php

namespace Database\Factories;

use App\Models\Corps;
use Illuminate\Database\Eloquent\Factories\Factory;

class CorpsFactory extends Factory
{
    protected $model = Corps::class;

    public function definition(): array
    {
        return [
            'libelle' => $this->faker->word,
            'libelle_ar' => $this->faker->word,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
