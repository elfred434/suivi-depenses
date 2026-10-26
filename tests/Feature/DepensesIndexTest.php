<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Depense;
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

    public function test_index_vide_affiche_message(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('depenses.index'))
            ->assertOk()
            ->assertSee('Aucune dépense trouvée.');
    }

    public function test_filtre_par_libelle_seul(): void
    {
        $user = User::factory()->create();
        $categorie = $this->createCategory($user, 'Alimentation');
        $this->createExpense($user, $categorie, 'Courses', '2026-01-02');
        $this->createExpense($user, $categorie, 'Transport', '2026-01-02');

        $this->actingAs($user)
            ->get(route('depenses.index', ['libelle' => 'Courses']))
            ->assertOk()
            ->assertSee('Courses')
            ->assertDontSee('Transport');
    }

    public function test_filtre_par_categorie_seul(): void
    {
        $user = User::factory()->create();
        $cat1 = $this->createCategory($user, 'Alimentation');
        $cat2 = $this->createCategory($user, 'Transport');
        $this->createExpense($user, $cat1, 'Depense A', '2026-01-02');
        $this->createExpense($user, $cat2, 'Depense B', '2026-01-02');

        $this->actingAs($user)
            ->get(route('depenses.index', ['categorie_id' => $cat1->id]))
            ->assertOk()
            ->assertSee('Depense A')
            ->assertDontSee('Depense B');
    }

    public function test_filtre_par_date_debut_seul(): void
    {
        $user = User::factory()->create();
        $categorie = $this->createCategory($user, 'Alimentation');
        $this->createExpense($user, $categorie, 'Ancienne', '2026-01-01');
        $this->createExpense($user, $categorie, 'Recente', '2026-01-03');

        $this->actingAs($user)
            ->get(route('depenses.index', ['date_debut' => '2026-01-02']))
            ->assertOk()
            ->assertSee('Recente')
            ->assertDontSee('Ancienne');
    }

    public function test_filtre_par_date_fin_seul(): void
    {
        $user = User::factory()->create();
        $categorie = $this->createCategory($user, 'Alimentation');
        $this->createExpense($user, $categorie, 'Ancienne', '2026-01-01');
        $this->createExpense($user, $categorie, 'Recente', '2026-01-03');

        $this->actingAs($user)
            ->get(route('depenses.index', ['date_fin' => '2026-01-02']))
            ->assertOk()
            ->assertSee('Ancienne')
            ->assertDontSee('Recente');
    }

    public function test_total_avec_zero_depense(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('depenses.index'))
            ->assertOk()
            ->assertSee('Montant total correspondant aux filtres : 0 FCFA');
    }

    public function test_total_avec_filtre_par_categorie(): void
    {
        $user = User::factory()->create();
        $cat1 = $this->createCategory($user, 'Alimentation');
        $cat2 = $this->createCategory($user, 'Transport');
        $d1 = $this->createExpense($user, $cat1, 'Depense A', '2026-01-02');
        $d1->montant = 100;
        $d1->save();
        $d2 = $this->createExpense($user, $cat2, 'Depense B', '2026-01-02');
        $d2->montant = 200;
        $d2->save();

        $this->actingAs($user)
            ->get(route('depenses.index', ['categorie_id' => $cat1->id]))
            ->assertOk()
            ->assertSee('Montant total correspondant aux filtres : 100 FCFA');
    }

    public function test_index_rejette_categorie_d_un_autre_utilisateur(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $otherCat = $this->createCategory($other, 'Privee');

        $this->actingAs($user)
            ->from(route('depenses.index'))
            ->get(route('depenses.index', ['categorie_id' => $otherCat->id]))
            ->assertRedirect(route('depenses.index'))
            ->assertSessionHasErrors('categorie_id');
    }

    public function test_index_rejette_date_debut_invalide(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('depenses.index'))
            ->get(route('depenses.index', ['date_debut' => 'pas-une-date']))
            ->assertRedirect(route('depenses.index'))
            ->assertSessionHasErrors('date_debut');
    }

    public function test_index_rejette_date_fin_invalide(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('depenses.index'))
            ->get(route('depenses.index', ['date_fin' => 'pas-une-date']))
            ->assertRedirect(route('depenses.index'))
            ->assertSessionHasErrors('date_fin');
    }

    public function test_index_conserve_filtres_sur_page_2(): void
    {
        $user = User::factory()->create();
        $categorie = $this->createCategory($user, 'Alimentation');

        for ($i = 1; $i <= 12; $i++) {
            $this->createExpense($user, $categorie, 'Achat ' . $i, '2026-01-02');
        }

        $response = $this->actingAs($user)->get(route('depenses.index', [
            'libelle' => 'Achat',
            'categorie_id' => $categorie->id,
            'date_debut' => '2026-01-01',
            'date_fin' => '2026-01-03',
            'page' => 2,
        ]));

        $response->assertOk()
            ->assertSee('Achat 2')
            ->assertSee('Achat 1');
    }

    private function createCategory(User $user, string $nom): Categorie
    {
        $categorie = new Categorie();
        $categorie->nom = $nom;
        $categorie->user()->associate($user);
        $categorie->save();

        return $categorie;
    }

    private function createExpense(User $user, Categorie $categorie, string $libelle, string $date): Depense
    {
        $depense = new Depense();
        $depense->libelle = $libelle;
        $depense->montant = 100;
        $depense->date_depenses = $date;
        $depense->categorie_id = $categorie->id;
        $user->depenses()->save($depense);

        return $depense;
    }
}
