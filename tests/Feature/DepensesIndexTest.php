<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Depenses;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepensesIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_depenses_peuvent_etre_filtrees_ensemble_avec_bornes_de_dates_inclusives(): void
    {
        $user = User::factory()->create();
        $categorie = $this->createCategory($user, 'Alimentation');
        $autreCategorie = $this->createCategory($user, 'Transport');

        $this->createExpense($user, $categorie, 'Courses du lundi', '2026-01-02 12:00:00');
        $this->createExpense($user, $categorie, 'Courses du mardi', '2026-01-03 12:00:00');
        $this->createExpense($user, $categorie, 'Courses trop tôt', '2026-01-01 12:00:00');
        $this->createExpense($user, $categorie, 'Courses trop tard', '2026-01-04 12:00:00');
        $this->createExpense($user, $autreCategorie, 'Courses autre catégorie', '2026-01-02 12:00:00');
        $this->createExpense($user, $categorie, 'Autre libellé', '2026-01-02 12:00:00');

        $this->actingAs($user)
            ->get(route('depenses.index', [
                'libelle' => 'Courses',
                'categorie_id' => $categorie->id,
                'date_debut' => '2026-01-02',
                'date_fin' => '2026-01-03',
            ]))
            ->assertOk()
            ->assertSee('Courses du lundi')
            ->assertSee('Courses du mardi')
            ->assertDontSee('Courses trop tôt')
            ->assertDontSee('Courses trop tard')
            ->assertDontSee('Courses autre catégorie')
            ->assertDontSee('Autre libellé')
            ->assertViewHas('total', 200);
    }

    public function test_depenses_triees_par_date_puis_id_et_paginées_avec_filtres_conserves(): void
    {
        $user = User::factory()->create();
        $categorie = $this->createCategory($user, 'Alimentation');

        for ($number = 1; $number <= 12; $number++) {
            $this->createExpense(
                $user,
                $categorie,
                'Achat ' . $number,
                $number <= 2 ? '2026-01-03 12:00:00' : '2026-01-02 12:00:00',
            );
        }

        $response = $this->actingAs($user)->get(route('depenses.index', [
            'libelle' => 'Achat',
            'categorie_id' => $categorie->id,
            'date_debut' => '2026-01-01',
            'date_fin' => '2026-01-03',
        ]));

        $response->assertOk()
            ->assertViewHas('depenses', function ($depenses): bool {
                $this->assertCount(10, $depenses->items());
                $this->assertSame(
                    ['Achat 2', 'Achat 1', 'Achat 12', 'Achat 11', 'Achat 10', 'Achat 9', 'Achat 8', 'Achat 7', 'Achat 6', 'Achat 5'],
                    $depenses->getCollection()->pluck('libelle')->all(),
                );
                $this->assertStringContainsString('libelle=Achat', $depenses->url(2));
                $this->assertStringContainsString('categorie_id=' . $depenses->first()->categorie_id, $depenses->url(2));
                $this->assertStringContainsString('date_debut=2026-01-01', $depenses->url(2));
                $this->assertStringContainsString('date_fin=2026-01-03', $depenses->url(2));

                return true;
            })
            ->assertViewHas('total', 1200);

        $this->get(route('depenses.index', [
            'libelle' => 'Achat',
            'categorie_id' => $categorie->id,
            'date_debut' => '2026-01-01',
            'date_fin' => '2026-01-03',
            'page' => 2,
        ]))
            ->assertOk()
            ->assertSee('Achat 4')
            ->assertSee('Achat 3')
            ->assertDontSee('Achat 12');
    }

    public function test_index_affiche_le_total_filtre_incluant_les_depenses_des_autres_pages(): void
    {
        $user = User::factory()->create();
        $categorie = $this->createCategory($user, 'Alimentation');

        for ($number = 1; $number <= 12; $number++) {
            $depense = $this->createExpense($user, $categorie, 'Repas ' . $number, '2026-01-02');
            $depense->montant = $number * 100;
            $depense->save();
        }

        $this->actingAs($user)
            ->get(route('depenses.index', ['libelle' => 'Repas']))
            ->assertOk()
            ->assertSee('Montant total correspondant aux filtres : 7 800 FCFA')
            ->assertViewHas('depenses', fn ($depenses) => $depenses->count() === 10);
    }

    public function test_index_rejette_une_date_de_debut_apres_la_date_de_fin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->followingRedirects()
            ->from(route('depenses.index'))
            ->get(route('depenses.index', [
                'date_debut' => '2026-02-02',
                'date_fin' => '2026-02-01',
            ]))
            ->assertOk()
            ->assertSee('La date de fin doit être postérieure ou égale à la date de début.')
            ->assertSee('value="2026-02-02"', false)
            ->assertSee('value="2026-02-01"', false);
    }

    public function test_utilisateur_ne_voit_que_ses_propres_depenses_et_categories_dans_l_index(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $categorie = $this->createCategory($user, 'À moi');
        $autreCategorie = $this->createCategory($otherUser, 'Privée');

        $this->createExpense($user, $categorie, 'Ma dépense', '2026-01-02 12:00:00');
        $this->createExpense($otherUser, $autreCategorie, 'Dépense privée', '2026-01-02 12:00:00');

        $this->actingAs($user)
            ->get(route('depenses.index'))
            ->assertOk()
            ->assertSee('Ma dépense')
            ->assertDontSee('Dépense privée')
            ->assertSee('À moi')
            ->assertDontSee('Privée');
    }

    private function createCategory(User $user, string $nom): Categorie
    {
        $categorie = new Categorie();
        $categorie->nom = $nom;
        $categorie->user()->associate($user);
        $categorie->save();

        return $categorie;
    }

    private function createExpense(User $user, Categorie $categorie, string $libelle, string $date): Depenses
    {
        $depense = new Depenses();
        $depense->libelle = $libelle;
        $depense->montant = 100;
        $depense->date_depenses = $date;
        $depense->categorie_id = $categorie->id;
        $user->depenses()->save($depense);

        return $depense;
    }
}
