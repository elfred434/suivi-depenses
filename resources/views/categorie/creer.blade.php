@extends('layouts.app')

@section('titre', 'Créer une catégorie')

@section('contenu')
    <div class="card" style="max-width:560px;margin:40px auto;">
        <h1>Créer une catégorie</h1>
        <form action="{{ route('categories.store') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="nom">Nom (obligatoire)</label>
                <input type="text" name="nom" id="nom" minlength="2" maxlength="50" value="{{ old('nom') }}" required>
                @error('nom')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="description">Description (facultatif)</label>
                <textarea name="description" id="description" maxlength="1000" cols="30" rows="5">{{ old('description') }}</textarea>
                @error('description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit">Créer</button>
        </form>
    </div>
@endsection