<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\Corps;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        return [
            'libelle' => $this->faker->jobTitle,
            'libelle_ar' => $this->faker->jobTitle,
            'corps_id' => Corps::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
