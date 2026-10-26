@extends('layouts.app')

@section('titre', 'Mes Catégories')

@section('contenu')
    <style>
        .cat-index { animation: fadeIn 0.6s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .cat-index h1 { color: #2d3748; margin-bottom: 8px; }
        .cat-index .stats { color: #718096; margin-bottom: 24px; font-size: 0.95rem; }
        .cat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        .cat-card {
            background: #fff;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            border-top: 4px solid #48bb78;
        }
        .cat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.1);
        }
        .cat-card h4 { margin: 0 0 8px; }
        .cat-card h4 a { color: #2d3748; text-decoration: none; font-size: 1.15rem; }
        .cat-card h4 a:hover { color: #48bb78; }
        .cat-card p { color: #718096; font-size: 0.9rem; margin: 0; min-height: 40px; }
        .cat-index .empty { text-align: center; padding: 60px 20px; color: #a0aec0; font-size: 1.1rem; }
        .cat-index .pagination { display: flex; justify-content: center; margin-top: 24px; }
    </style>

    <div class="cat-index">
        <h1>Mes Catégories</h1>
        <p class="stats">Nous avons {{ count($categorie) }} sur cette page et {{ $categorie->total() }} au total</p>

        <div class="cat-grid">
            @forelse ($categorie as $cat)
            <div class="cat-card" data-aos="zoom-in">
                <h4><a href="{{ route('categorie.show', ['cat' => $cat->id]) }}">{{ $cat->nom }}</a></h4>
                <p>{{ $cat->description ?? 'Aucune description' }}</p>
            </div>
            @empty
            <div class="empty">Aucune catégorie pour le moment.</div>
            @endforelse
        </div>

        <div class="pagination">{{ $categorie->links() }}</div>
    </div>
@endsection