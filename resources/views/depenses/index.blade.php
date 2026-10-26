@extends('layouts.app')

@section('titre', 'mes dépenses')

@section('contenu')
    <h1>Mes dépenses</h1>

    <form action="{{ route('depenses.index') }}" method="get">
        <div>
            <label for="libelle">Libellé</label>
            <input
                id="libelle"
                name="libelle"
                type="search"
                value="{{ old('libelle', request('libelle')) }}"
            >
        </div>
        @error('libelle')
            <p role="alert">{{ $message }}</p>
        @enderror

        <div>
            <label for="categorie_id">Catégorie</label>
            <select id="categorie_id" name="categorie_id">
                <option value="">Toutes les catégories</option>
                @foreach ($categories as $categorie)
                    <option
                        value="{{ $categorie->id }}"
                        @selected((string) old('categorie_id', request('categorie_id')) === (string) $categorie->id)
                    >
                        {{ $categorie->nom }}
                    </option>
                @endforeach
            </select>
        </div>
        @error('categorie_id')
            <p role="alert">{{ $message }}</p>
        @enderror

        <div>
            <label for="date_debut">Date de début</label>
            <input
                id="date_debut"
                name="date_debut"
                type="date"
                value="{{ old('date_debut', request('date_debut')) }}"
            >
        </div>
        @error('date_debut')
            <p role="alert">{{ $message }}</p>
        @enderror

        <div>
            <label for="date_fin">Date de fin</label>
            <input
                id="date_fin"
                name="date_fin"
                type="date"
                value="{{ old('date_fin', request('date_fin')) }}"
            >
        </div>
        @error('date_fin')
            <p role="alert">{{ $message }}</p>
        @enderror

        <button type="submit">Rechercher</button>
        <a href="{{ route('depenses.index') }}">Réinitialiser</a>
    </form>

    <p>Montant total correspondant aux filtres : {{ number_format($total, 0, ',', ' ') }} FCFA</p>

    @if ($depenses->isEmpty())
        <p>Aucune dépense trouvée.</p>
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

    {{ $depenses->links() }}
@endsection