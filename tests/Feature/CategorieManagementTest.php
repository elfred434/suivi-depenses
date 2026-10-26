<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Depense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorieManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_utilisateur_peut_creer_et_modifier_une_categorie_avec_nom_de_deux_a_cinquante_caracteres(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user)
            ->post(route('categories.store'), [
                'nom' => 'AB',
                'user_id' => $otherUser->id,
            ])
            ->assertRedirect(route('categories.index'));

        $categorie = Categorie::query()->firstOrFail();
        $this->assertSame($user->id, $categorie->user_id);

        $this->patch(route('categories.update', ['cat' => $categorie->id]), [
            'nom' => str_repeat('N', 50),
        ])->assertRedirect(route('categorie.show', ['cat' => $categorie->id]));

        $this->assertDatabaseHas('categories', [
            'id' => $categorie->id,
            'nom' => str_repeat('N', 50),
            'user_id' => $user->id,
        ]);
    }

    public function test_nom_de_categorie_hors_longueur_requise_est_rejete(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->from(route('categories.create'))
            ->post(route('categories.store'), ['nom' => 'A'])
            ->assertRedirect(route('categories.create'))
            ->assertSessionHasErrors('nom')
            ->assertSessionHasInput('nom', 'A');

        $this->from(route('categories.create'))
            ->post(route('categories.store'), ['nom' => str_repeat('N', 51)])
            ->assertRedirect(route('categories.create'))
            ->assertSessionHasErrors('nom');

        $this->assertDatabaseCount('categories', 0);
    }

    public function test_categorie_avec_depenses_ne_peut_pas_etre_supprimee_et_affiche_une_erreur_claire(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        Depense::factory()->create([
            'user_id' => $user->id,
            'categorie_id' => $categorie->id,
        ]);

        $this->actingAs($user)
            ->followingRedirects()
            ->from(route('categorie.show', ['cat' => $categorie->id]))
            ->delete(route('categories.delete', ['cat' => $categorie->id]))
            ->assertOk()
            ->assertSee('Cette catégorie ne peut pas être supprimée tant qu’elle contient des dépenses.');

        $this->assertDatabaseHas('categories', ['id' => $categorie->id]);
        $this->assertDatabaseCount('depenses', 1);
    }

    public function test_categorie_vide_peut_etre_supprimee_mais_celle_d_un_autre_utilisateur_non(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);

        $this->actingAs($otherUser)
            ->get(route('categorie.show', ['cat' => $categorie->id]))
            ->assertForbidden();
        $this->get(route('categories.edit', ['cat' => $categorie->id]))
            ->assertForbidden();

        $this->delete(route('categories.delete', ['cat' => $categorie->id]))
            ->assertForbidden();
        $this->patch(route('categories.update', ['cat' => $categorie->id]), [
            'nom' => 'Falsifiée',
        ])->assertForbidden();
        $this->assertDatabaseHas('categories', ['id' => $categorie->id]);

        $this->actingAs($user)
            ->delete(route('categories.delete', ['cat' => $categorie->id]))
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('succes');

        $this->assertDatabaseMissing('categories', ['id' => $categorie->id]);
    }

    public function test_liste_de_categories_vide_affiche_un_message_aide(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('categories.index'))
            ->assertOk()
            ->assertSee('Aucune catégorie pour le moment.');
    }

    public function test_creation_categorie_rejette_nom_invalide_et_description_trop_longue(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        foreach (['', 'A', str_repeat('N', 51)] as $nom) {
            $this->from(route('categories.create'))
                ->post(route('categories.store'), ['nom' => $nom])
                ->assertRedirect(route('categories.create'))
                ->assertSessionHasErrors('nom');
        }

        $this->from(route('categories.create'))
            ->post(route('categories.store'), [
                'nom' => 'Valide',
                'description' => str_repeat('D', 1001),
            ])
            ->assertRedirect(route('categories.create'))
            ->assertSessionHasErrors('description');

        $this->assertDatabaseCount('categories', 0);
    }

    public function test_modification_categorie_rejette_nom_invalide(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->from(route('categories.edit', ['cat' => $categorie->id]))
            ->patch(route('categories.update', ['cat' => $categorie->id]), ['nom' => 'A'])
            ->assertRedirect(route('categories.edit', ['cat' => $categorie->id]))
            ->assertSessionHasErrors('nom');

        $this->assertDatabaseHas('categories', [
            'id' => $categorie->id,
            'nom' => $categorie->nom,
        ]);
    }

    public function test_liste_categories_isolee_entre_utilisateurs(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Categorie::factory()->create(['user_id' => $user->id, 'nom' => 'Ma catégorie']);
        Categorie::factory()->create(['user_id' => $other->id, 'nom' => 'Catégorie privée']);

        $this->actingAs($user)
            ->get(route('categories.index'))
            ->assertSee('Ma catégorie')
            ->assertDontSee('Catégorie privée');
    }

    public function test_affichage_categorie_affiche_ses_depenses(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        Depense::factory()->create([
            'user_id' => $user->id,
            'categorie_id' => $categorie->id,
            'libelle' => 'Dépense visible',
        ]);

        $this->actingAs($user)
            ->get(route('categorie.show', ['cat' => $categorie->id]))
            ->assertOk()
            ->assertSee('Dépense visible');
    }

    public function test_creation_categorie_avec_description_max_et_sans_description(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('categories.store'), [
            'nom' => 'Avec desc',
            'description' => str_repeat('D', 1000),
        ])->assertRedirect(route('categories.index'));

        $this->post(route('categories.store'), [
            'nom' => 'Sans desc',
        ])->assertRedirect(route('categories.index'));

        $this->assertDatabaseCount('categories', 2);
    }

    public function test_modification_categorie_conserve_proprietaire(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->patch(route('categories.update', ['cat' => $categorie->id]), [
                'nom' => 'Nouveau nom',
                'user_id' => $other->id,
            ])
            ->assertRedirect(route('categorie.show', ['cat' => $categorie->id]));

        $this->assertDatabaseHas('categories', [
            'id' => $categorie->id,
            'nom' => 'Nouveau nom',
            'user_id' => $user->id,
        ]);
    }

    public function test_liste_categories_paginee(): void
    {
        $user = User::factory()->create();
        for ($i = 1; $i <= 12; $i++) {
            Categorie::factory()->create(['user_id' => $user->id, 'nom' => 'Cat ' . $i]);
        }

        $this->actingAs($user)
            ->get(route('categories.index'))
            ->assertOk()
            ->assertViewHas('categorie', fn ($c) => $c->count() === 10);
    }

    public function test_modification_categorie_avec_description_trop_longue_est_rejetee(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->from(route('categories.edit', ['cat' => $categorie->id]))
            ->patch(route('categories.update', ['cat' => $categorie->id]), [
                'nom' => 'Valide',
                'description' => str_repeat('D', 1001),
            ])
            ->assertRedirect(route('categories.edit', ['cat' => $categorie->id]))
            ->assertSessionHasErrors('description');

        $this->assertDatabaseHas('categories', [
            'id' => $categorie->id,
            'description' => $categorie->description,
        ]);
    }

    public function test_affichage_categorie_inexistante_retourne_404(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('categorie.show', ['cat' => 99999]))
            ->assertNotFound();
    }

    public function test_edition_categorie_inexistante_retourne_404(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('categories.edit', ['cat' => 99999]))
            ->assertNotFound();
    }

    public function test_modification_categorie_inexistante_retourne_404(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->patch(route('categories.update', ['cat' => 99999]), [
                'nom' => 'Test',
            ])
            ->assertNotFound();
    }

    public function test_suppression_categorie_inexistante_retourne_404(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->delete(route('categories.delete', ['cat' => 99999]))
            ->assertNotFound();
    }
}
