@extends('layouts.app')

@section('titre', 'Créer une dépense')

@section('contenu')
    <div class="card" style="max-width:600px;margin:40px auto;">
        <h1>Créer une dépense</h1>
        <form action="{{ route('depenses.store') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="libelle">Libellé</label>
                <input type="text" name="libelle" id="libelle" value="{{ old('libelle') }}">
                @error('libelle')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="montant">Montant (FCFA)</label>
                    <input type="number" name="montant" id="montant" min="1" step="1" value="{{ old('montant') }}">
                    @error('montant')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="date_depenses">Date de la dépense</label>
                    <input type="date" name="date_depenses" id="date_depenses" value="{{ old('date_depenses') }}">
                    @error('date_depenses')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" maxlength="1000" cols="30" rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="categorie_id">Catégorie (obligatoire)</label>
                <select name="categorie_id" id="categorie_id" required>
                    <option value="">Choisir une catégorie</option>
                    @foreach ($cat as $cat)
                        <option value="{{ $cat->id }}" @selected(old('categorie_id') == $cat->id)>
                            {{ $cat->nom }}
                        </option>
                    @endforeach
                </select>
                @error('categorie_id')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit">Créer la dépense</button>
        </form>
    </div>
@endsection