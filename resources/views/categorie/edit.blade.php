@extends('layouts.app')

@section('titre', 'Modifier la catégorie {{ $cat->nom }}')

@section('contenu')
    <style>
        .cat-edit {
            max-width: 560px;
            margin: 40px auto;
            padding: 36px;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 12px 32px rgba(0,0,0,0.06);
            border-left: 6px solid #ed8936;
            animation: slideInRight 0.5s ease-out;
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .cat-edit h1 { color: #2d3748; margin-bottom: 24px; }
        .cat-edit form { display: flex; flex-direction: column; gap: 20px; }
        .cat-edit label { font-weight: 600; color: #4a5568; display: block; margin-bottom: 6px; }
        .cat-edit input, .cat-edit textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
            font-family: inherit;
        }
        .cat-edit input:focus, .cat-edit textarea:focus {
            outline: none;
            border-color: #ed8936;
            box-shadow: 0 0 0 3px rgba(237, 137, 54, 0.15);
        }
        .cat-edit button {
            background: linear-gradient(135deg, #ed8936, #dd6b20);
            color: #fff;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .cat-edit button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(237, 137, 54, 0.35);
        }
        .cat-edit .error { color: #e53e3e; font-size: 0.85rem; margin-top: 4px; }
        .cat-edit .back-link { display: inline-block; margin-top: 16px; color: #718096; text-decoration: none; }
    </style>

    <div class="cat-edit">
        <h1>Modifier la catégorie {{ $cat->nom }}</h1>

        <form action="{{ route('categories.update', ['cat' => $cat->id]) }}" method="post">
            @csrf
            @method('PATCH')
            <div>
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" minlength="2" maxlength="50"
                value="{{ old('nom', $cat->nom) }}">

                @error('nom')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="description">Description</label>
                <textarea name="description" id="description" maxlength="1000" cols="30" rows="10">{{ old('description', $cat->description) }}</textarea>

                @error('description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit">Enregistrer les modifications</button>
        </form>

        <a class="back-link" href="{{ route('categorie.show', ['cat' => $cat->id]) }}">Revenir au détail</a>
    </div>
@endsection