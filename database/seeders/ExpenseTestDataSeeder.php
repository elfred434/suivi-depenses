<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Depenses;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use LogicException;

class ExpenseTestDataSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new LogicException('The demonstration data seeder must not run in production.');
        }

        $users = User::query()->where('name', 'like', 'pot%')->get();

        if ($users->count() > 1) {
            throw new LogicException(
                "Expected at most one user whose name starts with 'pot'; found {$users->count()}."
            );
        }

        $user = $users->first() ?? User::query()->where('email', 'demo@example.test')->first();

        if ($user === null) {
            $demoPassword = Str::random(40);
            $user = User::factory()->create([
                'name' => 'Demo User',
                'email' => 'demo@example.test',
                'password' => $demoPassword,
            ]);
            $this->command?->info("Demo login: demo@example.test / {$demoPassword}");
        }

        $categories = [];

        foreach (['Alimentaire', 'Transport', 'Maison'] as $nom) {
            $categories[$nom] = Categorie::query()
                ->where('user_id', $user->id)
                ->where('nom', $nom)
                ->first()
                ?? Categorie::factory()->create([
                    'user_id' => $user->id,
                    'nom' => $nom,
                    'description' => null,
                ]);
        }

        $depenses = [
            ['Alimentaire', 'Courses marché', 48, '2026-01-03', 'Achats au marché'],
            ['Alimentaire', 'Pain boulangerie', 6, '2026-01-05', 'Pain et viennoiseries'],
            ['Alimentaire', 'Fruits et légumes', 23, '2026-01-08', 'Produits frais'],
            ['Alimentaire', 'Repas restaurant', 32, '2026-01-12', 'Déjeuner'],
            ['Alimentaire', 'Courses supermarché', 76, '2026-01-15', 'Courses de la semaine'],
            ['Transport', 'Essence voiture', 65, '2026-01-04', 'Plein de carburant'],
            ['Transport', 'Ticket métro', 4, '2026-01-07', 'Déplacement en ville'],
            ['Transport', 'Stationnement', 8, '2026-01-11', 'Parking du centre-ville'],
            ['Transport', 'Révision vélo', 42, '2026-01-17', 'Entretien du vélo'],
            ['Maison', 'Facture internet', 35, '2026-01-02', 'Abonnement mensuel'],
            ['Maison', 'Électricité maison', 91, '2026-01-10', 'Facture mensuelle'],
            ['Maison', 'Produits nettoyage', 18, '2026-01-14', 'Produits ménagers'],
        ];

        foreach ($depenses as [$nomCategorie, $libelle, $montant, $date, $description]) {
            $categorie = $categories[$nomCategorie];
            $depenseExists = Depenses::query()
                ->where('user_id', $user->id)
                ->where('categorie_id', $categorie->id)
                ->where('libelle', $libelle)
                ->exists();

            if (! $depenseExists) {
                Depenses::factory()->create([
                    'user_id' => $user->id,
                    'categorie_id' => $categorie->id,
                    'libelle' => $libelle,
                    'montant' => $montant,
                    'date_depenses' => $date,
                    'description' => $description,
                ]);
            }
        }
    }
}
