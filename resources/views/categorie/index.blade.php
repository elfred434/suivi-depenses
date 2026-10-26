@extends('layouts.app')

@section('titre', 'Mes Catégories')

@section('contenu')
    <h1>Mes Catégories</h1>
    <p>Nous avons {{ count($categorie) }} sur cette page et 
        {{ $categorie->total() }} au total
    </p>

    <ul>
        @forelse ($categorie as $cat )
        <li>
            <h4><a href="{{ route('categorie.show', [
            'cat' => $cat->id ]) }}">
                {{ $cat->nom }}</a></h4>
            <p>{{ $cat->description }}</p>
        </li>
            
        @empty
            <li>Aucune catégorie pour le moment.</li>
        @endforelse
    </ul>
@endsection