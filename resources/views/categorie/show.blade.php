@extends('layouts.app')

@section('titre', 'Détails de la catégorie ' . $cat->nom)

@section('contenu')
    <style>
        .cat-show {
            max-width: 800px;
            margin: 40px auto;
            animation: fadeIn 0.6s ease-out;
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .cat-show h1 { color: #2d3748; margin-bottom: 8px; }
        .cat-show .desc { color: #718096; font-size: 1.05rem; margin-bottom: 24px; padding: 16px; background: #f7fafc; border-radius: 10px; border-left: 4px solid #48bb78; }
        .cat-show h2 { color: #4a5568; margin-top: 32px; margin-bottom: 16px; }
        .cat-show .expense-list { list-style: none; padding: 0; }
        .cat-show .expense-list li {
            background: #fff;
            padding: 16px 20px;
            margin-bottom: 12px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .cat-show .expense-list li:hover {
            transform: translateX(6px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.08);
        }
        .cat-show .expense-list a { font-weight: 600; color: #2d3748; text-decoration: none; }
        .cat-show .expense-list a:hover { color: #48bb78; }
        .cat-show .amount { color: #48bb78; font-weight: 700; }
        .cat-show .date { color: #a0aec0; font-size: 0.85rem; }
        .cat-show .actions { margin-top: 32px; display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
        .cat-show .btn-edit { background: #ed8936; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: background 0.2s; }
        .cat-show .btn-edit:hover { background: #dd6b20; }
        .cat-show .btn-delete { background: #e53e3e; color: #fff; padding: 10px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .cat-show .btn-delete:hover { background: #c53030; }
        .cat-show .empty { text-align: center; padding: 40px; color: #a0aec0; }
    </style>

    <div class="cat-show">
        <h1>Détails de la catégorie {{ $cat->nom }}</h1>

        @error('categorie')
            <p role="alert" style="color:#e53e3e;">{{ $message }}</p>
        @enderror

        <p class="desc">{{ $cat->description ?? 'Aucune description' }}</p>

        <h2>Dépenses ({{ $depenses->total() }})</h2>

        @if ($depenses->isEmpty())
            <p class="empty">Aucune dépense dans cette catégorie.</p>
        @else
            <ul class="expense-list">
                @foreach ($depenses as $depense)
                    <li data-aos="fade-right">
                        <a href="{{ route('depenses.show', ['dep' => $depense->id]) }}"><strong>{{ $depense->libelle }}</strong></a>
                        <span class="amount">{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</span>
                        <span class="date">{{ \Carbon\Carbon::parse($depense->date_depenses)->format('d/m/Y') }}</span>
                    </li>
                @endforeach
            </ul>

            {{ $depenses->links() }}
        @endif

        <div class="actions">
            <a class="btn-edit" href="{{ route('categories.edit', ['cat' => $cat->id]) }}">Modifier la catégorie</a>

            <form action="{{ route('categories.delete', ['cat' => $cat->id]) }}" method="post" style="display:inline;">
                @csrf
                @method('DELETE')
                <button class="btn-delete" type="submit">Supprimer définitivement</button>
            </form>
        </div>
    </div>
@endsection