<?php

namespace Database\Factories;

use App\Models\Fonctionnaire;
use App\Models\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;

class FonctionnaireFactory extends Factory
{
    protected $model = Fonctionnaire::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'nom_ar' => $this->faker->lastName,
            'prenom_ar' => $this->faker->firstName,
            'cin' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{6}'),
            'date_naissance' => $this->faker->date(),
            'date_recrutement' => $this->faker->date(),
            'solde_année_act' => 22,
            'solde_année_prec' => 0,
            'grade_id' => Grade::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
