@extends('layouts.app')

@section('titre', 'Détails de la dépense ' . $dep->libelle)

@section('contenu')
    <div style="max-width:700px;margin:0 auto;">
        <h1>Détails de la dépense {{ $dep->libelle }}</h1>

        <div class="card">
            <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--border);">
                <span style="font-weight:600;color:var(--text-light);">Date</span>
                <span>{{ \Carbon\Carbon::parse($dep->date_depenses)->format('d/m/Y') }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--border);">
                <span style="font-weight:600;color:var(--text-light);">Catégorie</span>
                <span>{{ $dep->categorie->nom }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--border);">
                <span style="font-weight:600;color:var(--text-light);">Montant</span>
                <span style="font-weight:700;color:var(--primary);">{{ number_format($dep->montant, 0, ',', ' ') }} FCFA</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:12px 0;">
                <span style="font-weight:600;color:var(--text-light);">Description</span>
                <span>{{ $dep->description ?? 'Aucune description' }}</span>
            </div>
        </div>

        <div style="margin-top:24px;display:flex;gap:12px;flex-wrap:wrap;">
            <a class="btn" href="{{ route('depenses.edit', ['dep' => $dep->id]) }}">Modifier la dépense</a>
            <form action="{{ route('depenses.delete', ['dep' => $dep->id]) }}" method="post" style="display:inline;">
                @csrf
                @method('delete')
                <button class="danger" type="submit">Supprimer définitivement</button>
            </form>
        </div>

        <p style="margin-top:16px;"><a href="{{ route('depenses.index') }}" style="color:var(--text-light);">← Retour à la liste</a></p>
    </div>
@endsection