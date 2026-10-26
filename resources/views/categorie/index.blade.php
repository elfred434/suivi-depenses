@extends('layouts.app')

@section('titre', 'Mes Catégories')

@section('contenu')
    <h1>Mes Catégories</h1>
    <p style="color:var(--text-light);margin-bottom:20px;">{{ count($categorie) }} sur cette page, {{ $categorie->total() }} au total</p>

    <div class="grid">
        @forelse ($categorie as $cat)
            <div class="grid-card">
                <h3 style="margin:0 0 8px;"><a href="{{ route('categorie.show', ['cat' => $cat->id]) }}">{{ $cat->nom }}</a></h3>
                <p style="color:var(--text-light);font-size:0.9rem;margin:0;">{{ $cat->description ?? 'Aucune description' }}</p>
            </div>
        @empty
            <p class="empty-state" style="grid-column:1/-1;">Aucune catégorie pour le moment.</p>
        @endforelse
    </div>

    <div class="pagination">{{ $categorie->links() }}</div>
@endsection