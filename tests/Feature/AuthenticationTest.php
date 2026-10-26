<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Categorie;
use App\Models\Depenses;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_utilisateur_peut_s_inscrire_se_connecter_et_se_deconnecter(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('name="_token"', false);

        $this->post(route('register.store'), [
            'name' => 'Compte Test',
            'email' => 'compte@example.test',
            'password' => 'un-mot-de-passe-solide',
            'password_confirmation' => 'un-mot-de-passe-solide',
        ])->assertRedirect(route('register'));

        $user = User::query()->where('email', 'compte@example.test')->firstOrFail();
        $this->assertGuest();

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('name="_token"', false);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'un-mot-de-passe-solide',
        ])->assertRedirect(route('categories.index'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))
            ->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_visiteur_ne_peut_pas_acceder_aux_routes_de_gestion(): void
    {
        $this->get(route('categories.index'))->assertRedirect(route('login'));
        $this->get(route('depenses.index'))->assertRedirect(route('login'));
        $this->get(route('categories.create'))->assertRedirect(route('login'));
        $this->get(route('depenses.create'))->assertRedirect(route('login'));
    }

    public function test_visiteur_ne_peut_pas_acceder_aux_routes_de_detail_et_mutation(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $depense = Depenses::factory()->create(['user_id' => $user->id, 'categorie_id' => $categorie->id]);

        $this->get(route('categories.edit', ['cat' => $categorie->id]))->assertRedirect(route('login'));
        $this->get(route('depenses.edit', ['dep' => $depense->id]))->assertRedirect(route('login'));
        $this->get(route('categorie.show', ['cat' => $categorie->id]))->assertRedirect(route('login'));
        $this->get(route('depenses.show', ['dep' => $depense->id]))->assertRedirect(route('login'));

        $this->post(route('categories.store'), ['nom' => 'Xyz'])->assertRedirect(route('login'));
        $this->post(route('depenses.store'), [])->assertRedirect(route('login'));
        $this->patch(route('categories.update', ['cat' => $categorie->id]), ['nom' => 'Xyz'])->assertRedirect(route('login'));
        $this->patch(route('depenses.update', ['dep' => $depense->id]), [])->assertRedirect(route('login'));
        $this->delete(route('categories.delete', ['cat' => $categorie->id]))->assertRedirect(route('login'));
        $this->delete(route('depenses.delete', ['dep' => $depense->id]))->assertRedirect(route('login'));
    }
    public function test_utilisateur_ne_peut_pas_modifier_la_depense_d_un_autre_meme_avec_requete_falsifiee(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $owner->id]);
        $depense = Depenses::factory()->create([
            'user_id' => $owner->id,
            'categorie_id' => $categorie->id,
        ]);
        $autreCategorie = Categorie::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($otherUser)
            ->patch(route('depenses.update', ['dep' => $depense->id]), [
                'libelle' => 'Tentative de modification',
                'montant' => 1,
                'date_depenses' => '2026-01-01',
                'categorie_id' => $autreCategorie->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('depenses', [
            'id' => $depense->id,
            'libelle' => $depense->libelle,
            'user_id' => $owner->id,
        ]);
    }
}
