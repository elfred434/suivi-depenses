@extends('layouts.app')

@section('titre', 'Mise à jour de la catégorie')

@section('contenu')
    <div class="card" style="max-width:560px;margin:40px auto;">
        <h1>Mise à jour de la catégorie</h1>
        <form action="{{ route('categories.update', ['cat' => $cat->id ?? '']) }}" method="post">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" value="{{ old('nom', $cat->nom ?? '') }}">
                @error('nom')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description">{{ old('description', $cat->description ?? '') }}</textarea>
                @error('description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit">Mettre à jour</button>
        </form>
    </div>
@endsection