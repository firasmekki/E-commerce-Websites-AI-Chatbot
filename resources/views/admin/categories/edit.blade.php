@extends('layouts.app')

@section('title', 'Modifier une catégorie')

@section('content')
<div class="page-shell max-w-4xl">
    <p class="eyebrow">Administration</p>
    <h1 class="mt-2 section-title">Modifier la catégorie</h1>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="panel mt-6 grid gap-5 p-6">
        @csrf
        @method('PUT')
        @include('admin.categories.partials.form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Annuler</a>
            <button type="submit" class="btn-primary">Mettre à jour</button>
        </div>
    </form>
</div>
@endsection
