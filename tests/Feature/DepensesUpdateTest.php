<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Depense;
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
        $depense = Depense::factory()->create([
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
        $depense = Depense::factory()->create([
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
        $depense = Depense::factory()->create([
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

    public function test_modification_depense_rejette_libelle_invalide(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $depense = Depense::factory()->create([
            'user_id' => $user->id,
            'categorie_id' => $categorie->id,
        ]);

        $this->actingAs($user)
            ->from(route('depenses.edit', ['dep' => $depense->id]))
            ->patch(route('depenses.update', ['dep' => $depense->id]), [
                'libelle' => 'AB',
                'montant' => 10,
                'date_depenses' => '2026-01-01',
                'categorie_id' => $categorie->id,
            ])
            ->assertRedirect(route('depenses.edit', ['dep' => $depense->id]))
            ->assertSessionHasErrors('libelle');
    }

    public function test_modification_depense_rejette_date_invalide(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $depense = Depense::factory()->create([
            'user_id' => $user->id,
            'categorie_id' => $categorie->id,
        ]);

        $this->actingAs($user)
            ->from(route('depenses.edit', ['dep' => $depense->id]))
            ->patch(route('depenses.update', ['dep' => $depense->id]), [
                'libelle' => 'Libelle valide',
                'montant' => 10,
                'date_depenses' => 'pas-une-date',
                'categorie_id' => $categorie->id,
            ])
            ->assertRedirect(route('depenses.edit', ['dep' => $depense->id]))
            ->assertSessionHasErrors('date_depenses');
    }

    public function test_modification_depense_rejette_description_trop_longue(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $depense = Depense::factory()->create([
            'user_id' => $user->id,
            'categorie_id' => $categorie->id,
        ]);

        $this->actingAs($user)
            ->from(route('depenses.edit', ['dep' => $depense->id]))
            ->patch(route('depenses.update', ['dep' => $depense->id]), [
                'libelle' => 'Libelle valide',
                'montant' => 10,
                'date_depenses' => '2026-01-01',
                'description' => str_repeat('D', 1001),
                'categorie_id' => $categorie->id,
            ])
            ->assertRedirect(route('depenses.edit', ['dep' => $depense->id]))
            ->assertSessionHasErrors('description');
    }

    public function test_modification_depense_rejette_categorie_inexistante(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $depense = Depense::factory()->create([
            'user_id' => $user->id,
            'categorie_id' => $categorie->id,
        ]);

        $this->actingAs($user)
            ->from(route('depenses.edit', ['dep' => $depense->id]))
            ->patch(route('depenses.update', ['dep' => $depense->id]), [
                'libelle' => 'Libelle valide',
                'montant' => 10,
                'date_depenses' => '2026-01-01',
                'categorie_id' => 99999,
            ])
            ->assertRedirect(route('depenses.edit', ['dep' => $depense->id]))
            ->assertSessionHasErrors('categorie_id');
    }

    public function test_modification_depense_rejette_categorie_d_un_autre_utilisateur(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $otherCategorie = Categorie::factory()->create(['user_id' => $other->id]);
        $depense = Depense::factory()->create([
            'user_id' => $user->id,
            'categorie_id' => $categorie->id,
            'libelle' => 'Original',
        ]);

        $this->actingAs($user)
            ->from(route('depenses.edit', ['dep' => $depense->id]))
            ->patch(route('depenses.update', ['dep' => $depense->id]), [
                'libelle' => 'Tentative',
                'montant' => 10,
                'date_depenses' => '2026-01-01',
                'categorie_id' => $otherCategorie->id,
            ])
            ->assertRedirect(route('depenses.edit', ['dep' => $depense->id]))
            ->assertSessionHasErrors('categorie_id');

        $this->assertDatabaseHas('depenses', [
            'id' => $depense->id,
            'libelle' => 'Original',
            'categorie_id' => $categorie->id,
        ]);
    }

    public function test_modification_depense_change_de_categorie_valide(): void
    {
        $user = User::factory()->create();
        $cat1 = Categorie::factory()->create(['user_id' => $user->id]);
        $cat2 = Categorie::factory()->create(['user_id' => $user->id]);
        $depense = Depense::factory()->create([
            'user_id' => $user->id,
            'categorie_id' => $cat1->id,
        ]);

        $this->actingAs($user)
            ->patch(route('depenses.update', ['dep' => $depense->id]), [
                'libelle' => 'Modifie',
                'montant' => 50,
                'date_depenses' => '2026-02-02',
                'categorie_id' => $cat2->id,
            ])
            ->assertRedirect(route('depenses.show', ['dep' => $depense->id]))
            ->assertSessionHas('succes');

        $this->assertDatabaseHas('depenses', [
            'id' => $depense->id,
            'categorie_id' => $cat2->id,
        ]);
    }

    public function test_modification_depense_rejette_champs_requis_manquants(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $depense = Depense::factory()->create([
            'user_id' => $user->id,
            'categorie_id' => $categorie->id,
        ]);

        $this->actingAs($user)
            ->from(route('depenses.edit', ['dep' => $depense->id]))
            ->patch(route('depenses.update', ['dep' => $depense->id]), [])
            ->assertRedirect(route('depenses.edit', ['dep' => $depense->id]))
            ->assertSessionHasErrors(['libelle', 'montant', 'date_depenses', 'categorie_id']);
    }

    public function test_modification_depense_conserve_proprietaire(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $depense = Depense::factory()->create([
            'user_id' => $user->id,
            'categorie_id' => $categorie->id,
        ]);

        $this->actingAs($user)
            ->patch(route('depenses.update', ['dep' => $depense->id]), [
                'libelle' => 'Modifie',
                'montant' => 10,
                'date_depenses' => '2026-01-01',
                'categorie_id' => $categorie->id,
                'user_id' => $other->id,
            ])
            ->assertRedirect(route('depenses.show', ['dep' => $depense->id]));

        $this->assertDatabaseHas('depenses', [
            'id' => $depense->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_affichage_depense_affiche_ses_details(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $depense = Depense::factory()->create([
            'user_id' => $user->id,
            'categorie_id' => $categorie->id,
            'libelle' => 'Depense detail',
            'montant' => 123,
        ]);

        $this->actingAs($user)
            ->get(route('depenses.show', ['dep' => $depense->id]))
            ->assertOk()
            ->assertSee('Depense detail')
            ->assertSee('123');
    }

    public function test_affichage_depense_inexistante_retourne_404(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('depenses.show', ['dep' => 99999]))
            ->assertNotFound();
    }

    public function test_edition_depense_inexistante_retourne_404(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('depenses.edit', ['dep' => 99999]))
            ->assertNotFound();
    }

    public function test_modification_depense_inexistante_retourne_404(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)
            ->patch(route('depenses.update', ['dep' => 99999]), [
                'libelle' => 'Test',
                'montant' => 10,
                'date_depenses' => '2026-01-01',
                'categorie_id' => $categorie->id,
            ])
            ->assertNotFound();
    }

    public function test_champ_date_affiche_date_sans_heure(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $depense = Depense::factory()->create([
            'user_id' => $user->id,
            'categorie_id' => $categorie->id,
            'date_depenses' => '2026-01-15 14:30:00',
        ]);

        $this->actingAs($user)
            ->get(route('depenses.edit', ['dep' => $depense->id]))
            ->assertOk()
            ->assertSee('value="2026-01-15"', false);
    }
}
