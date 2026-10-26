@extends('layouts.app')

@section('titre', 'Details de la dépenses ' . $dep->libelle)

@section('contenu')

    <h1>Détails de la dépenses {{ $dep->libelle }}</h1>


    <table>
        <thead>
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Catégorie</th>
                
                <th scope="col">Montant (FCFA)</th>
                <th scope="col">Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>
                    {{ $dep->date_depenses }}
                </th>
                <th>
                    {{ $dep->categorie->nom }}
                </th>
                <th>
                    {{ number_format($dep->montant, 0, ',', ' ') }} FCFA
                </th>
                <th>

                    @if ($dep->description)
                        <p>{{ $dep->description }}</p>
                    @else
                        <p>Aucune description</p>
                    @endif
                </th>
            </tr>
        </tbody>
    </table>

    <a href="{{ route('depenses.edit', [
    'dep' => $dep->id]) }}">Modifier la dépense</a>
    <form action="{{ route('depenses.delete', ['dep' => $dep->id]) }}" method="post">
        @csrf
        @method('delete')
        <button type="submit">Supprimer définitivement</button>
    </form>

@endsection
