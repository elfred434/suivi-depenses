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

    public function test_creation_depense_rejette_libelle_invalide(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        foreach (['', 'AB', str_repeat('L', 101)] as $libelle) {
            $this->from(route('depenses.create'))
                ->post(route('depenses.store'), [
                    'libelle' => $libelle,
                    'montant' => 10,
                    'date_depenses' => '2026-01-01',
                    'categorie_id' => $categorie->id,
                ])
                ->assertRedirect(route('depenses.create'))
                ->assertSessionHasErrors('libelle');
        }

        $this->assertDatabaseCount('depenses', 0);
    }

    public function test_creation_depense_rejette_montant_invalide(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        foreach ([null, 0, -1, 'abc', '1.5'] as $montant) {
            $this->from(route('depenses.create'))
                ->post(route('depenses.store'), [
                    'libelle' => 'Libelle valide',
                    'montant' => $montant,
                    'date_depenses' => '2026-01-01',
                    'categorie_id' => $categorie->id,
                ])
                ->assertRedirect(route('depenses.create'))
                ->assertSessionHasErrors('montant');
        }

        $this->assertDatabaseCount('depenses', 0);
    }

    public function test_creation_depense_rejette_date_invalide(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $this->from(route('depenses.create'))
            ->post(route('depenses.store'), [
                'libelle' => 'Libelle valide',
                'montant' => 10,
                'date_depenses' => 'pas-une-date',
                'categorie_id' => $categorie->id,
            ])
            ->assertRedirect(route('depenses.create'))
            ->assertSessionHasErrors('date_depenses');

        $this->assertDatabaseCount('depenses', 0);
    }

    public function test_creation_depense_rejette_categorie_inexistante(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->from(route('depenses.create'))
            ->post(route('depenses.store'), [
                'libelle' => 'Libelle valide',
                'montant' => 10,
                'date_depenses' => '2026-01-01',
                'categorie_id' => 99999,
            ])
            ->assertRedirect(route('depenses.create'))
            ->assertSessionHasErrors('categorie_id');

        $this->assertDatabaseCount('depenses', 0);
    }

    public function test_creation_depense_rejette_champs_requis_manquants(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->from(route('depenses.create'))
            ->post(route('depenses.store'), [])
            ->assertRedirect(route('depenses.create'))
            ->assertSessionHasErrors(['libelle', 'montant', 'date_depenses', 'categorie_id']);

        $this->assertDatabaseCount('depenses', 0);
    }

    public function test_creation_depense_accepte_libelle_et_montant_limites(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $this->post(route('depenses.store'), [
            'libelle' => str_repeat('L', 3),
            'montant' => 1,
            'date_depenses' => '2026-01-01',
            'categorie_id' => $categorie->id,
        ])->assertRedirect(route('depenses.index'));

        $this->post(route('depenses.store'), [
            'libelle' => str_repeat('L', 100),
            'montant' => 500000,
            'date_depenses' => '2026-01-01',
            'categorie_id' => $categorie->id,
        ])->assertRedirect(route('depenses.index'));

        $this->assertDatabaseCount('depenses', 2);
    }

    public function test_creation_depense_sans_description(): void
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $this->post(route('depenses.store'), [
            'libelle' => 'Sans desc',
            'montant' => 10,
            'date_depenses' => '2026-01-01',
            'categorie_id' => $categorie->id,
        ])->assertRedirect(route('depenses.index'));

        $this->assertDatabaseHas('depenses', [
            'libelle' => 'Sans desc',
            'description' => null,
        ]);
    }

    public function test_creation_depense_conserve_proprietaire_malgre_user_id_falsifie(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $categorie = Categorie::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $this->post(route('depenses.store'), [
            'libelle' => 'Test proprietaire',
            'montant' => 10,
            'date_depenses' => '2026-01-01',
            'categorie_id' => $categorie->id,
            'user_id' => $other->id,
        ])->assertRedirect(route('depenses.index'));

        $this->assertDatabaseHas('depenses', [
            'libelle' => 'Test proprietaire',
            'user_id' => $user->id,
        ]);
    }
}
