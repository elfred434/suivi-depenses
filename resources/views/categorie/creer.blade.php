@extends('layouts.app')

@section('titre', 'Créer une catégorie')

@section('contenu')
    <style>
        .cat-create {
            max-width: 560px;
            margin: 40px auto;
            padding: 36px;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 12px 32px rgba(0,0,0,0.06);
            animation: popIn 0.5s ease-out;
        }
        @keyframes popIn {
            from { opacity: 0; transform: scale(0.92); }
            to { opacity: 1; transform: scale(1); }
        }
        .cat-create h1 { color: #2d3748; margin-bottom: 24px; }
        .cat-create form { display: flex; flex-direction: column; gap: 20px; }
        .cat-create label { font-weight: 600; color: #4a5568; display: block; margin-bottom: 6px; }
        .cat-create input, .cat-create textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
            font-family: inherit;
        }
        .cat-create input:focus, .cat-create textarea:focus {
            outline: none;
            border-color: #48bb78;
            box-shadow: 0 0 0 3px rgba(72, 187, 120, 0.15);
        }
        .cat-create button {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: #fff;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .cat-create button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(72, 187, 120, 0.35);
        }
        .cat-create .error { color: #e53e3e; font-size: 0.85rem; margin-top: 4px; }
    </style>

    <div class="cat-create">
        <h1>Créer une catégorie</h1>
        <form action="{{ route('categories.store') }}" method="post">
            @csrf

            <div>
                <label for="nom">Nom (obligatoire)</label>
                <input type="text" name="nom" id="nom" minlength="2" maxlength="50" value="{{ old('nom') }}" required>
                @error('nom')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description">Description (facultatif)</label>
                <textarea name="description" id="description" maxlength="1000" cols="30" rows="10">{{ old('description') }}</textarea>
                @error('description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit">Créer</button>
        </form>
    </div>
@endsection