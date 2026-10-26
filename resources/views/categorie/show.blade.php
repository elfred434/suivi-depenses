@extends('layouts.app')

@section('titre', 'Détails de la catégorie ' . $cat->nom)

@section('contenu')
    <div style="max-width:700px;margin:0 auto;">
        <h1>Détails de la catégorie {{ $cat->nom }}</h1>

        @error('categorie')
            <p class="error" role="alert">{{ $message }}</p>
        @enderror

        <div class="card">
            <p style="color:var(--text-light);">{{ $cat->description ?? 'Aucune description' }}</p>
        </div>

        <h2>Dépenses ({{ $depenses->total() }})</h2>

        @if ($depenses->isEmpty())
            <p class="empty-state">Aucune dépense dans cette catégorie.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Libellé</th>
                        <th>Montant</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($depenses as $depense)
                        <tr>
                            <td><a href="{{ route('depenses.show', ['dep' => $depense->id]) }}">{{ $depense->libelle }}</a></td>
                            <td>{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</td>
                            <td>{{ \Carbon\Carbon::parse($depense->date_depenses)->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $depenses->links() }}</div>
        @endif

        <div style="margin-top:24px;display:flex;gap:12px;flex-wrap:wrap;">
            <a class="btn" href="{{ route('categories.edit', ['cat' => $cat->id]) }}">Modifier la catégorie</a>
            <form action="{{ route('categories.delete', ['cat' => $cat->id]) }}" method="post" style="display:inline;">
                @csrf
                @method('DELETE')
                <button class="danger" type="submit">Supprimer définitivement</button>
            </form>
        </div>
    </div>
@endsection