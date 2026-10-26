<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Categorie;
use App\Models\Depense;
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
        $depense = Depense::factory()->create(['user_id' => $user->id, 'categorie_id' => $categorie->id]);

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
        $depense = Depense::factory()->create([
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
    public function test_inscription_valide_cree_un_compte(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Nouveau Compte',
            'email' => 'nouveau@example.test',
            'password' => 'un-mot-de-passe-solide-12',
            'password_confirmation' => 'un-mot-de-passe-solide-12',
        ])->assertRedirect(route('register'));

        $this->assertDatabaseHas('users', [
            'name' => 'Nouveau Compte',
            'email' => 'nouveau@example.test',
        ]);
    }

    public function test_inscription_rejette_les_champs_invalides(): void
    {
        // nom vide
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => '',
                'email' => 'a@example.test',
                'password' => 'un-mot-de-passe-solide-12',
                'password_confirmation' => 'un-mot-de-passe-solide-12',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('name');

        // nom trop long
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => str_repeat('N', 101),
                'email' => 'b@example.test',
                'password' => 'un-mot-de-passe-solide-12',
                'password_confirmation' => 'un-mot-de-passe-solide-12',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('name');

        // email invalide
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Test',
                'email' => 'pas-un-email',
                'password' => 'un-mot-de-passe-solide-12',
                'password_confirmation' => 'un-mot-de-passe-solide-12',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('email');

        // email dupliqué
        User::factory()->create(['email' => 'dup@example.test']);
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Test',
                'email' => 'dup@example.test',
                'password' => 'un-mot-de-passe-solide-12',
                'password_confirmation' => 'un-mot-de-passe-solide-12',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('email');

        // mot de passe trop court
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Test',
                'email' => 'c@example.test',
                'password' => 'court',
                'password_confirmation' => 'court',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('password');

        // confirmation ne correspond pas
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Test',
                'email' => 'd@example.test',
                'password' => 'un-mot-de-passe-solide-12',
                'password_confirmation' => 'autre-chose-123456',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('password');
    }

    public function test_connexion_valide_authentifie_l_utilisateur(): void
    {
        $user = User::factory()->create(['password' => bcrypt('mot-de-passe-valide-12')]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'mot-de-passe-valide-12',
        ])->assertRedirect(route('categories.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_connexion_rejette_les_identifiants_invalides(): void
    {
        $user = User::factory()->create(['password' => bcrypt('mot-de-passe-valide-12')]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'mauvais-mot-de-passe',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email')
            ->assertSessionHasInput('email', $user->email);

        $this->assertGuest();
    }

    public function test_connexion_valide_requiert_email_et_mot_de_passe(): void
    {
        $this->from(route('login'))
            ->post(route('login.store'), [])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function test_deconnexion_deconnecte_l_utilisateur(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));
        $this->assertGuest();
    }


    public function test_page_accueil_retourne_succes(): void
    {
        $this->get('/')
            ->assertOk();
    }

    public function test_page_inscription_affiche_formulaire(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('name="_token"', false);
    }

    public function test_page_connexion_affiche_formulaire(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('name="_token"', false);
    }

    public function test_deconnexion_par_visiteur_redirige_vers_connexion(): void
    {
        $this->post(route('logout'))
            ->assertRedirect(route('login'));
    }

    public function test_inscription_rejette_email_trop_long(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Test',
                'email' => str_repeat('a', 256) . '@example.test',
                'password' => 'un-mot-de-passe-solide-12',
                'password_confirmation' => 'un-mot-de-passe-solide-12',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('email');
    }

    public function test_inscription_rejette_nom_trop_long(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => str_repeat('N', 101),
                'email' => 'long@example.test',
                'password' => 'un-mot-de-passe-solide-12',
                'password_confirmation' => 'un-mot-de-passe-solide-12',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('name');
    }
}
