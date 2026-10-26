@extends('layouts.app')

@section('titre', 'Mes dépenses')

@section('contenu')
    <h1>Mes dépenses</h1>

    <form class="card" style="margin-bottom:20px;" action="{{ route('depenses.index') }}" method="get">
        <div class="form-row">
            <div class="form-group">
                <label for="libelle">Libellé</label>
                <input id="libelle" name="libelle" type="search" value="{{ old('libelle', request('libelle')) }}">
                @error('libelle')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
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
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="date_debut">Date de début</label>
                <input id="date_debut" name="date_debut" type="date" value="{{ old('date_debut', request('date_debut')) }}">
                @error('date_debut')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="date_fin">Date de fin</label>
                <input id="date_fin" name="date_fin" type="date" value="{{ old('date_fin', request('date_fin')) }}">
                @error('date_fin')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div style="display:flex;gap:12px;align-items:center;">
            <button type="submit">Rechercher</button>
            <a href="{{ route('depenses.index') }}" style="color:var(--text-light);">Réinitialiser</a>
        </div>
    </form>

    <p class="stat-bar">Montant total correspondant aux filtres : {{ number_format($total, 0, ',', ' ') }} FCFA</p>

    @if ($depenses->isEmpty())
        <p class="empty-state">Aucune dépense trouvée.</p>
    @else
        <table>
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
                    <tr>
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
@endsection