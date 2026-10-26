<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Depenses;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepensesCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_utilisateur_authentifie_peut_creer_une_depense_pour_sa_categorie(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $categorie = new Categorie();
        $categorie->nom = 'Alimentation';
        $categorie->user()->associate($user);
        $categorie->save();

        $response = $this->actingAs($user)->post(route('depenses.store'), [
            'libelle' => 'Courses',
            'montant' => 42,
            'date_depenses' => '2026-10-25',
            'description' => 'Courses de la semaine',
            'categorie_id' => $categorie->id,
            'user_id' => $otherUser->id,
        ]);

        $response->assertRedirect(route('depenses.index'));
        $this->assertDatabaseHas('depenses', [
            'libelle' => 'Courses',
            'montant' => 42,
            'date_depenses' => '2026-10-25',
            'description' => 'Courses de la semaine',
            'categorie_id' => $categorie->id,
            'user_id' => $user->id,
        ]);

        $this->get(route('depenses.index'))
            ->assertOk()
            ->assertSee('Courses');
    }

    public function test_utilisateur_ne_peut_pas_creer_une_depense_pour_la_categorie_d_un_autre(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $categorie = new Categorie();
        $categorie->nom = 'Catégorie privée';
        $categorie->user()->associate($otherUser);
        $categorie->save();

        $response = $this->actingAs($user)->from('/depenses/creer')->post(route('depenses.store'), [
            'libelle' => 'Courses',
            'montant' => 42,
            'date_depenses' => '2026-10-25',
            'categorie_id' => $categorie->id,
        ]);

        $response->assertRedirect('/depenses/creer');
        $response->assertSessionHasErrors('categorie_id');
        $this->assertDatabaseCount('depenses', 0);
    }

    public function test_depense_accepte_dates_futures_et_descriptions_jusqu_a_mille_caracteres(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('depenses.store'), [
                'libelle' => 'Dépense future',
                'montant' => '2500',
                'date_depenses' => '2099-12-31',
                'description' => str_repeat('D', 1000),
                'categorie_id' => $categorie->id,
            ])
            ->assertRedirect(route('depenses.index'));

        $this->assertDatabaseHas('depenses', [
            'libelle' => 'Dépense future',
            'montant' => 2500,
            'date_depenses' => '2099-12-31',
            'user_id' => $user->id,
            'categorie_id' => $categorie->id,
        ]);
    }

    public function test_depense_rejette_montants_decimaux_et_descriptions_de_plus_de_mille_caracteres(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->from(route('depenses.create'))
            ->post(route('depenses.store'), [
                'libelle' => 'Dépense invalide',
                'montant' => '10.50',
                'date_depenses' => '2099-12-31',
                'description' => str_repeat('D', 1001),
                'categorie_id' => $categorie->id,
            ])
            ->assertRedirect(route('depenses.create'))
            ->assertSessionHasErrors(['montant', 'description'])
            ->assertSessionHasInput('libelle', 'Dépense invalide')
            ->assertSessionHasInput('categorie_id', $categorie->id);

        $this->assertDatabaseCount('depenses', 0);
    }
}
