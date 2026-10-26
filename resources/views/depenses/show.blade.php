@extends('layouts.app')

@section('titre', 'Détails de la dépense ' . $dep->libelle)

@section('contenu')
    <style>
        .dep-show { max-width: 700px; margin: 40px auto; animation: fadeIn 0.6s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .dep-show h1 { color: #2d3748; margin-bottom: 20px; }
        .dep-card { background: #fff; border-radius: 16px; padding: 28px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        .dep-card .row { display: flex; justify-content: space-between; padding: 14px 0; border-bottom: 1px solid #f0f0f0; }
        .dep-card .row:last-child { border-bottom: none; }
        .dep-card .label { font-weight: 600; color: #718096; }
        .dep-card .value { color: #2d3748; font-weight: 500; }
        .dep-card .amount { color: #4299e1; font-size: 1.3rem; font-weight: 700; }
        .dep-show .actions { margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap; }
        .dep-show .btn-edit { background: #ed8936; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: background 0.2s; }
        .dep-show .btn-edit:hover { background: #dd6b20; }
        .dep-show .btn-delete { background: #e53e3e; color: #fff; padding: 10px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .dep-show .btn-delete:hover { background: #c53030; }
        .dep-show .back-link { color: #718096; text-decoration: none; display: inline-block; margin-top: 16px; }
    </style>

    <div class="dep-show">
        <h1>Détails de la dépense {{ $dep->libelle }}</h1>

        <div class="dep-card" data-aos="fade-up">
            <div class="row">
                <span class="label">Date</span>
                <span class="value">{{ \Carbon\Carbon::parse($dep->date_depenses)->format('d/m/Y') }}</span>
            </div>
            <div class="row">
                <span class="label">Catégorie</span>
                <span class="value">{{ $dep->categorie->nom }}</span>
            </div>
            <div class="row">
                <span class="label">Montant</span>
                <span class="amount">{{ number_format($dep->montant, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="row">
                <span class="label">Description</span>
                <span class="value">{{ $dep->description ?? 'Aucune description' }}</span>
            </div>
        </div>

        <div class="actions">
            <a class="btn-edit" href="{{ route('depenses.edit', ['dep' => $dep->id]) }}">Modifier la dépense</a>

            <form action="{{ route('depenses.delete', ['dep' => $dep->id]) }}" method="post" style="display:inline;">
                @csrf
                @method('delete')
                <button class="btn-delete" type="submit">Supprimer définitivement</button>
            </form>
        </div>

        <a class="back-link" href="{{ route('depenses.index') }}">← Retour à la liste</a>
    </div>
@endsection