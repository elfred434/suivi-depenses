@extends('layouts.app')

@section('titre', 'Détails de la catégorie ' . $cat->nom)

@section('contenu')
    <h1>Détails de la catégorie {{ $cat->nom }}</h1>

    @error('categorie')
        <p role="alert">{{ $message }}</p>
    @enderror

    <p>{{ $cat->description ?? 'Aucune description' }}</p>

    <h2>Dépenses ({{ $depenses->total() }})</h2>

    @if ($depenses->isEmpty())
        <p>Aucune dépense dans cette catégorie.</p>
    @else
        <ul>
            @foreach ($depenses as $depense)
                <li>
                    <a href="{{ route('depenses.show', [
                    'dep' => $depense->id]) }}"><strong>{{ $depense->libelle }}</strong></a> —
                    {{ number_format($depense->montant, 0, ',', ' ') }} FCFA —
                    {{ \Carbon\Carbon::parse($depense->date_depenses)->format('d/m/Y') }}
                </li>
            @endforeach
        </ul>

        {{ $depenses->links() }}
    @endif

    <a href="{{ route('categories.edit', [
    'cat' => $cat->id]) }}">Modifier la catégorie</a>

    <form action="{{ route('categories.delete', ['cat' => $cat->id]) }}" method="post">
        @csrf
        @method('DELETE')
        <button type="submit">Supprimez définitivement </button>

    </form>
@endsection