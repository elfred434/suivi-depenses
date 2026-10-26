<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Depenses;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepensesUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_proprietaire_peut_voir_et_modifier_sa_depense(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $depense = Depenses::factory()->create([
            'user_id' => $user->id,
            'categorie_id' => $categorie->id,
            'libelle' => 'Ancien libellé',
        ]);

        $this->actingAs($user)
            ->get(route('depenses.show', ['dep' => $depense->id]))
            ->assertOk()
            ->assertSee('Ancien libellé')
            ->assertSee('FCFA');

        $this->patch(route('depenses.update', ['dep' => $depense->id]), [
            'libelle' => 'Libellé modifié',
            'montant' => '2300',
            'date_depenses' => '2099-06-15',
            'description' => 'Description actualisée',
            'categorie_id' => $categorie->id,
        ])
            ->assertRedirect(route('depenses.show', ['dep' => $depense->id]))
            ->assertSessionHas('succes');

        $this->assertDatabaseHas('depenses', [
            'id' => $depense->id,
            'libelle' => 'Libellé modifié',
            'montant' => 2300,
            'date_depenses' => '2099-06-15',
            'description' => 'Description actualisée',
            'user_id' => $user->id,
            'categorie_id' => $categorie->id,
        ]);
    }

    public function test_utilisateur_ne_peut_pas_voir_les_details_ou_le_formulaire_d_edition_de_la_depense_d_un_autre(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $owner->id]);
        $depense = Depenses::factory()->create([
            'user_id' => $owner->id,
            'categorie_id' => $categorie->id,
        ]);

        $this->actingAs($otherUser)
            ->get(route('depenses.show', ['dep' => $depense->id]))
            ->assertForbidden();
        $this->get(route('depenses.edit', ['dep' => $depense->id]))
            ->assertForbidden();
    }

    public function test_montants_invalides_sont_rejetes_a_la_modification(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $depense = Depenses::factory()->create([
            'user_id' => $user->id,
            'categorie_id' => $categorie->id,
            'montant' => 100,
        ]);

        foreach ([0, -5, '10.50', 'abc', null] as $montant) {
            $this->actingAs($user)
                ->from(route('depenses.edit', ['dep' => $depense->id]))
                ->patch(route('depenses.update', ['dep' => $depense->id]), [
                    'libelle' => 'Libellé valide',
                    'montant' => $montant,
                    'date_depenses' => '2026-01-01',
                    'categorie_id' => $categorie->id,
                ])
                ->assertRedirect(route('depenses.edit', ['dep' => $depense->id]))
                ->assertSessionHasErrors('montant');
        }

        $this->assertDatabaseHas('depenses', [
            'id' => $depense->id,
            'montant' => 100,
        ]);
    }
}
