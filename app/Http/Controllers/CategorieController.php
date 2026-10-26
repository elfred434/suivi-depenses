<?php

namespace App\Http\Controllers;
use App\Models\Categorie;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCategorieRequest;
use App\Http\Requests\UpdateCategorieRequest;
use Illuminate\Support\Facades\Gate;

class CategorieController extends Controller
{
    public function create(){
        return view('categorie.creer');
    }

    public function store(StoreCategorieRequest $request){
        $donnees = $request->validated();

        $cat = new Categorie();
        $cat->nom = $donnees['nom'];
        $cat->description = $donnees['description'] ?? null;
        $request->user()->categorie()->save($cat);
        return redirect()
        ->route('categories.index')
        ->with('succes', 'Categorie créé');

    }
    public function index(Request $request){
        $requete = $request->user()->categorie();
        $categorie = $requete->orderByDesc('id')
        ->paginate(10);

        return view('categorie.index', [
            'categorie' => $categorie,
        ]);
    }
    public function show(Categorie $cat ){
        Gate::authorize('view', $cat);

        $depenses = $cat->depenses()
            ->orderByDesc('date_depenses')
            ->orderByDesc('id')
            ->paginate(15);

        return view('categorie.show', [
            'cat' => $cat,
            'depenses' => $depenses
        ]);
    }
    public function edit(Categorie $cat){
        Gate::authorize('update', $cat);

        return view('categorie.edit', [
            'cat' => $cat
        ]);
    }

    public function update(UpdateCategorieRequest $request, Categorie $cat ){
        $donnees = $request->validated();

        $cat->nom = $donnees['nom'];
        $cat->description = $donnees['description'] ?? null;
        $cat->save();

        return redirect()
        ->route('categorie.show', [
            'cat' => $cat->id
        ])
        ->with('succes', 'La Catégorie a été modifié');

    }

    public function delete(Categorie $cat){
        if ($cat->user_id !== request()->user()->id) {
            abort(403);
        }

        if ($cat->depenses()->exists()) {
            return back()->withErrors([
                'categorie' => 'Cette catégorie ne peut pas être supprimée tant qu’elle contient des dépenses.',
            ]);
        }

        Gate::authorize('delete', $cat);
        $cat->delete();

        return redirect()
        ->route('categories.index')
        ->with('succes', 'la catégorie a été supprimé');
    }
    

}
