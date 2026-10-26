@extends('layouts.app')

@section('titre', 'Mise à jour de la catégorie')

@section('contenu')
    <style>
        .cat-update { max-width: 560px; margin: 40px auto; padding: 36px; background: #fff; border-radius: 18px; box-shadow: 0 12px 32px rgba(0,0,0,0.06); border-top: 6px solid #9f7aea; animation: slideInLeft 0.5s ease-out; }
        @keyframes slideInLeft { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
        .cat-update h1 { color: #2d3748; margin-bottom: 24px; }
        .cat-update form { display: flex; flex-direction: column; gap: 20px; }
        .cat-update label { font-weight: 600; color: #4a5568; display: block; margin-bottom: 6px; }
        .cat-update input, .cat-update textarea { width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; transition: border-color 0.2s, box-shadow 0.2s; box-sizing: border-box; font-family: inherit; }
        .cat-update input:focus, .cat-update textarea:focus { outline: none; border-color: #9f7aea; box-shadow: 0 0 0 3px rgba(159, 122, 234, 0.15); }
        .cat-update button { background: linear-gradient(135deg, #9f7aea, #805ad5); color: #fff; padding: 14px; border: none; border-radius: 10px; font-size: 1rem; font-weight: 700; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; }
        .cat-update button:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(159, 122, 234, 0.35); }
        .cat-update .error { color: #e53e3e; font-size: 0.85rem; margin-top: 4px; }
    </style>

    <div class="cat-update">
        <h1>Mise à jour de la catégorie</h1>
        <form action="{{ route('categories.update', ['cat' => $cat->id ?? '']) }}" method="post">
            @csrf
            @method('PATCH')
            <div>
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" value="{{ old('nom', $cat->nom ?? '') }}">
                @error('nom')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div>
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