<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\ExpenseTestDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTestDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seme_categories_et_depenses_pour_l_utilisateur_correspondant_de_maniere_idempotente(): void
    {
        $user = User::factory()->create(['name' => 'pot-test']);

        $this->seed(ExpenseTestDataSeeder::class);
        $this->seed(ExpenseTestDataSeeder::class);

        $this->assertDatabaseCount('categories', 3);
        $this->assertDatabaseCount('depenses', 12);
        $this->assertDatabaseMissing('categories', ['user_id' => null]);
        $this->assertDatabaseMissing('depenses', ['user_id' => null]);

        $this->assertSame(
            3,
            $user->categorie()->count(),
        );
        $this->assertSame(
            12,
            $user->depenses()->count(),
        );
        $this->assertDatabaseMissing('depenses', [
            'user_id' => $user->id,
            'categorie_id' => null,
        ]);
    }

    public function test_cree_un_compte_demo_si_aucun_utilisateur_correspondant(): void
    {
        $this->seed(ExpenseTestDataSeeder::class);
        $this->seed(ExpenseTestDataSeeder::class);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('categories', 3);
        $this->assertDatabaseCount('depenses', 12);
        $this->assertDatabaseHas('users', [
            'email' => 'demo@example.test',
            'name' => 'Demo User',
        ]);

        $demoUser = User::query()->where('email', 'demo@example.test')->firstOrFail();
        $this->assertSame(12, $demoUser->depenses()->count());
        $this->assertSame(
            0,
            $demoUser->depenses()
                ->join('categories', 'depenses.categorie_id', '=', 'categories.id')
                ->whereColumn('depenses.user_id', '!=', 'categories.user_id')
                ->count(),
        );
    }

    public function test_la_factory_depense_utilise_le_proprietaire_de_la_categorie_si_pas_d_utilisateur(): void
    {
        $depense = \App\Models\Depenses::factory()->create();

        $this->assertSame($depense->categorie->user_id, $depense->user_id);
    }
}
