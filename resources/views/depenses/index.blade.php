@extends('layouts.app')

@section('titre', 'Mes dépenses')

@section('contenu')
    <style>
        .dep-index { animation: fadeIn 0.6s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .dep-index h1 { color: #2d3748; margin-bottom: 16px; }
        .filter-bar {
            background: #fff;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }
        .filter-bar label { font-weight: 600; color: #4a5568; font-size: 0.85rem; display: block; margin-bottom: 4px; }
        .filter-bar input, .filter-bar select {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.95rem;
            box-sizing: border-box;
        }
        .filter-bar button {
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: #fff;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            align-self: end;
        }
        .total-bar {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: #fff;
            padding: 16px 24px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 1.1rem;
            font-weight: 700;
            box-shadow: 0 6px 16px rgba(72, 187, 120, 0.25);
        }
        .dep-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
        }
        .dep-table th, .dep-table td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #f0f0f0; }
        .dep-table th { background: #f7fafc; color: #4a5568; font-weight: 700; }
        .dep-table tr:hover { background: #f9fafb; }
        .dep-table a { color: #4299e1; font-weight: 600; text-decoration: none; }
        .dep-table a:hover { text-decoration: underline; }
        .empty-state { text-align: center; padding: 60px 20px; color: #a0aec0; font-size: 1.1rem; }
        .pagination { display: flex; justify-content: center; margin-top: 24px; }
        .reset-link { color: #718096; text-decoration: none; font-size: 0.9rem; }
    </style>

    <div class="dep-index">
        <h1>Mes dépenses</h1>

        <form class="filter-bar" action="{{ route('depenses.index') }}" method="get">
            <div>
                <label for="libelle">Libellé</label>
                <input id="libelle" name="libelle" type="search" value="{{ old('libelle', request('libelle')) }}">
                @error('libelle')
                    <p style="color:#e53e3e;font-size:0.8rem;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="categorie_id">Catégorie</label>
                <select id="categorie_id" name="categorie_id">
                    <option value="">Toutes les catégories</option>
                    @foreach ($categories as $categorie)
                        <option value="{{ $categorie->id }}" @selected((string) old('categorie_id', request('categorie_id')) === (string) $categorie->id)>
                            {{ $categorie->nom }}
                        </option>
                    @endforeach
                </select>
                @error('categorie_id')
                    <p style="color:#e53e3e;font-size:0.8rem;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="date_debut">Date de début</label>
                <input id="date_debut" name="date_debut" type="date" value="{{ old('date_debut', request('date_debut')) }}">
                @error('date_debut')
                    <p style="color:#e53e3e;font-size:0.8rem;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="date_fin">Date de fin</label>
                <input id="date_fin" name="date_fin" type="date" value="{{ old('date_fin', request('date_fin')) }}">
                @error('date_fin')
                    <p style="color:#e53e3e;font-size:0.8rem;">{{ $message }}</p>
                @enderror
            </div>
            <div style="display:flex;gap:8px;align-items:end;">
                <button type="submit">Rechercher</button>
                <a class="reset-link" href="{{ route('depenses.index') }}">Réinitialiser</a>
            </div>
        </form>

        <p class="total-bar">Montant total correspondant aux filtres : {{ number_format($total, 0, ',', ' ') }} FCFA</p>

        @if ($depenses->isEmpty())
            <p class="empty-state">Aucune dépense trouvée.</p>
        @else
            <table class="dep-table">
                <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Libellé</th>
                        <th scope="col">Catégorie</th>
                        <th scope="col">Montant (FCFA)</th>
                        <th scope="col">Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($depenses as $depense)
                        <tr data-aos="fade-up">
                            <td>{{ $depense->date_depenses }}</td>
                            <td><a href="{{ route('depenses.show', ['dep' => $depense->id]) }}">{{ $depense->libelle }}</a></td>
                            <td>{{ $depense->categorie->nom }}</td>
                            <td>{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $depense->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="pagination">{{ $depenses->links() }}</div>
    </div>
@endsection