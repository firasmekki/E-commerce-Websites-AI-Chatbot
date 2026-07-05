@extends('layouts.app')

@section('title', 'Modifier client')

@section('content')
<div class="page-shell max-w-4xl">
    <p class="eyebrow">Administration</p>
    <h1 class="mt-2 section-title">Modifier un client</h1>
    <form action="{{ route('admin.customers.update', $customer) }}" method="POST" class="panel mt-6 grid gap-5 p-6">
        @csrf
        @method('PUT')
        @include('admin.customers.partials.form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.customers.index') }}" class="btn-secondary">Annuler</a>
            <button class="btn-primary">Mettre a jour</button>
        </div>
    </form>
</div>
@endsection
