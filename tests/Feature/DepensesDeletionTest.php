<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Depenses;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepensesDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_proprietaire_peut_supprimer_une_depense_depuis_sa_page_detail(): void
    {
        $user = User::factory()->create();
        $depense = $this->createExpense($user);

        $this->actingAs($user)
            ->get(route('depenses.show', ['dep' => $depense->id]))
            ->assertOk()
            ->assertSee(route('depenses.delete', ['dep' => $depense->id]))
            ->assertSee('method="post"', false)
            ->assertSee('name="_method" value="delete"', false);

        $this->delete(route('depenses.delete', ['dep' => $depense->id]))
            ->assertRedirect(route('depenses.index'))
            ->assertSessionHas('succes');

        $this->assertDatabaseMissing('depenses', ['id' => $depense->id]);
    }

    public function test_utilisateur_ne_peut_pas_supprimer_la_depense_d_un_autre(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $depense = $this->createExpense($owner);

        $this->actingAs($otherUser)
            ->delete(route('depenses.delete', ['dep' => $depense->id]))
            ->assertForbidden();

        $this->assertDatabaseHas('depenses', ['id' => $depense->id]);
    }

    public function test_visiteur_est_redirige_vers_connexion_lors_de_la_suppression_d_une_depense(): void
    {
        $user = User::factory()->create();
        $depense = $this->createExpense($user);

        $this->delete(route('depenses.delete', ['dep' => $depense->id]))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('depenses', ['id' => $depense->id]);
    }

    public function test_suppression_depense_inexistante_retourne_404(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->delete(route('depenses.delete', ['dep' => 99999]))
            ->assertNotFound();
    }

    private function createExpense(User $user): Depenses
    {
        $categorie = new Categorie();
        $categorie->nom = 'Alimentation';
        $categorie->user()->associate($user);
        $categorie->save();

        $depense = new Depenses();
        $depense->libelle = 'Courses';
        $depense->montant = 42;
        $depense->date_depenses = '2026-10-25';
        $depense->categorie()->associate($categorie);
        $user->depenses()->save($depense);

        return $depense;
    }
}
