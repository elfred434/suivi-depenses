<?php

namespace App\Http\Controllers;
use App\Models\Depense;
use Illuminate\Http\Request;
use App\Http\Requests\StoreDepensesRequest;
use App\Http\Requests\IndexDepensesRequest;
use App\Http\Requests\UpdateDepensesRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\Categorie;
class DepensesController extends Controller
{
    public function create(Request $request){
        $categories = $request->user()->categorie()->orderBy('nom')->get();
        return view('depenses.creer', [
            'cat' => $categories
        ]);
    }

    public function store(StoreDepensesRequest $request){
        $donnees = $request->validated();

        $dep = new Depense();
        $dep->libelle = $donnees['libelle'];
        $dep->montant = $donnees['montant'];
        $dep->date_depenses = $donnees['date_depenses'];
        $dep->description = $donnees['description'] ?? null;
        $dep->categorie_id = $donnees['categorie_id'];
        $request->user()->depenses()->save($dep);

        return redirect()
        ->route('depenses.index')
        ->with('succes', 'depenses créé');
    }

    public function index(IndexDepensesRequest $request){
        $donnees = $request->validated();

        $libelle = $donnees['libelle'] ?? null;
        $categorie_id = $donnees['categorie_id'] ?? null;
        $date_debut = $donnees['date_debut'] ?? null;
        $date_fin = $donnees['date_fin'] ?? null;
        $requete = $request->user()->depenses();

        //recherche dans le libelle
        if (!empty($libelle)) {
            $requete->where('libelle', 'like', '%' . $libelle . '%');
        }

        //filtre par catégorie
        if (!empty($categorie_id)) {
            $requete->where('categorie_id', $categorie_id);
        }

        //filtre par date de début et de fin
        if (!empty($date_debut)) {
            $requete->whereDate('date_depenses', '>=', $date_debut);
        }

        if (!empty($date_fin)) {
            $requete->whereDate('date_depenses', '<=', $date_fin);
        }

        $total = (clone $requete)->sum('montant');

        $depenses = $requete
        ->with('categorie')
        ->orderByDesc('date_depenses')
        ->orderByDesc('id')
        ->paginate(10)
        ->withQueryString();

        $categories = $request->user()->categorie()->orderBy('nom')->get();

        return view('depenses.index', compact('depenses', 'categories', 'total'));
    }
    public function show(Depense $dep){
        Gate::authorize('view', $dep);

        return view ('depenses.show', [
            'dep' => $dep
        ]);
    }
    public function edit(Depense $dep){
        Gate::authorize('update', $dep);
          $categories = auth()->user()->categorie()->orderBy('nom')->get();

    return view('depenses.edit', [
        'dep'        => $dep,         
        'categories' => $categories,  
    ]);
    }

    public function update(UpdateDepensesRequest $request, Depense $dep){
        $donnees = $request->validated();

        $dep->libelle = $donnees['libelle'];
        $dep->montant = $donnees['montant'];
        $dep->date_depenses = $donnees['date_depenses'];
        $dep->description = $donnees['description'] ?? null;
        $dep->categorie_id = $donnees['categorie_id'];
        $dep->save();

        return redirect()
        ->route('depenses.show', [
            'dep' => $dep->id
        ])
        ->with('succes', 'La dépenses a été mdofié');


    }

    public function delete(Depense $dep){
        Gate::authorize('delete', $dep);

        $dep->delete();
        return redirect()
        ->route('depenses.index')
        ->with('succes', 'La dépenses a été supprimé ');
    }
    
    
}
