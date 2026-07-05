@extends('layouts.app')

@section('title', 'Ajouter un produit')

@section('content')
<div class="page-shell max-w-4xl">
    <p class="eyebrow">Administration</p>
    <h1 class="mt-2 section-title">Ajouter un produit</h1>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="panel mt-6 grid gap-5 p-6">
        @csrf
        @include('admin.products.partials.form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="btn-secondary">Annuler</a>
            <button type="submit" class="btn-primary">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
