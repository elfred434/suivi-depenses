@extends('layouts.app')

@section('titre', 'Formulaire de création de dépense')

@section('contenu')
    <h1>Créer une dépense</h1>
    <form action="{{ route('depenses.store') }}" method="post">
        @csrf
        <div>
            <label for="libelle">Libellé</label>
            <input type="text" name="libelle" id="libelle"
            value="{{ old('libelle') }}">

            @error('libelle')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="montant">Montant (FCFA)</label>
            <input type="number" name="montant" id="montant" min="1" step="1"
            value="{{ old('montant') }}">

            @error('montant')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="date_depenses">Date de  la depenses</label>
            <input type="date" name="date_depenses" id="date_depenses"
            value="{{ old('date_depenses') }}">
            @error('date_depenses')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description">Description</label>
            <textarea name="description" id="description" maxlength="1000" cols="30" rows="10">{{ old('description') }}</textarea>

            @error('description')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="categorie_id">Catégorie (obligatoire)</label>
            <select name="categorie_id" id="categorie_id" required>
                <option value=""> Choisir une catégories</option>
                @foreach ($cat as $cat)
                    <option value="{{ $cat->id }}" @selected(old('categorie_id') == $cat->id)>
                        {{ $cat->nom }}
                    </option>
                @endforeach
            </select>

            @error('categorie_id')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">Créer la dépense</button>
    </form>
@endsection