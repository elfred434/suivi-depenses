<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Depenses;
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
        Depenses::factory()->create([
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
}
