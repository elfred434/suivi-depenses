@extends('layouts.app')

@section('titre', 'Modifier la catégorie ' . $cat->nom)

@section('contenu')
    <div class="card" style="max-width:560px;margin:40px auto;">
        <h1>Modifier la catégorie {{ $cat->nom }}</h1>
        <form action="{{ route('categories.update', ['cat' => $cat->id]) }}" method="post">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" minlength="2" maxlength="50" value="{{ old('nom', $cat->nom) }}">
                @error('nom')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" maxlength="1000" cols="30" rows="5">{{ old('description', $cat->description) }}</textarea>
                @error('description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit">Enregistrer les modifications</button>
        </form>
        <p style="margin-top:16px;"><a href="{{ route('categorie.show', ['cat' => $cat->id]) }}">Revenir au détail</a></p>
    </div>
@endsection