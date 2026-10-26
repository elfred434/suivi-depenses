@extends('layouts.app')

@section('titre', 'Modifier une dépense')

@section('contenu')
    <style>
        .dep-edit {
            max-width: 600px;
            margin: 40px auto;
            padding: 36px;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 12px 32px rgba(0,0,0,0.06);
            border-left: 6px solid #ed8936;
            animation: slideInRight 0.5s ease-out;
        }
        @keyframes slideInRight { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
        .dep-edit h1 { color: #2d3748; margin-bottom: 24px; }
        .dep-edit form { display: flex; flex-direction: column; gap: 20px; }
        .dep-edit label { font-weight: 600; color: #4a5568; display: block; margin-bottom: 6px; }
        .dep-edit input, .dep-edit select, .dep-edit textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
            font-family: inherit;
        }
        .dep-edit input:focus, .dep-edit select:focus, .dep-edit textarea:focus {
            outline: none;
            border-color: #ed8936;
            box-shadow: 0 0 0 3px rgba(237, 137, 54, 0.15);
        }
        .dep-edit button {
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
        .dep-edit button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(237, 137, 54, 0.35);
        }
        .dep-edit .error { color: #e53e3e; font-size: 0.85rem; margin-top: 4px; }
    </style>

    <div class="dep-edit">
        <h1>Modifier une dépense</h1>
        <form action="{{ route('depenses.update', ['dep' => $dep->id]) }}" method="post">
            @csrf
            @method('PATCH')

            <div>
                <label for="libelle">Libellé</label>
                <input type="text" name="libelle" id="libelle" value="{{ old('libelle', $dep->libelle) }}">
                @error('libelle')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description">Description</label>
                <textarea name="description" id="description" maxlength="1000" cols="30" rows="10">{{ old('description', $dep->description) }}</textarea>
                @error('description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="montant">Montant (FCFA)</label>
                <input type="number" name="montant" id="montant" min="1" step="1" value="{{ old('montant', $dep->montant) }}">
                @error('montant')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="date_depenses">Date de la dépense</label>
                <input type="date" name="date_depenses" id="date_depenses"
                    value="{{ old('date_depenses', $dep->date_depenses ? substr($dep->date_depenses, 0, 10) : '') }}">
                @error('date_depenses')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="categorie_id">Catégorie (obligatoire)</label>
                <select name="categorie_id" id="categorie_id" required>
                    <option value="">Choisir une catégorie</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('categorie_id', $dep->categorie_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nom }}
                        </option>
                    @endforeach
                </select>
                @error('categorie_id')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit">Enregistrer les modifications</button>
        </form>
    </div>
@endsection