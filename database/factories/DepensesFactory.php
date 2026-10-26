<?php

namespace Database\Factories;

use App\Models\Categorie;
use App\Models\Depenses;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Depenses>
 */
class DepensesFactory extends Factory
{
    protected $model = Depenses::class;

    public function definition(): array
    {
        return [
            'libelle' => fake()->sentence(3),
            'montant' => fake()->numberBetween(1, 500000),
            'date_depenses' => fake()->date(),
            'description' => fake()->optional()->sentence(),
            'categorie_id' => Categorie::factory(),
            'user_id' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Depenses $depense): void {
            if ($depense->user_id === null) {
                $depense->user_id = $depense->categorie->user_id;
            }
        });
    }
}
