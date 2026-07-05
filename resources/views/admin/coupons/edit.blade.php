@extends('layouts.app')

@section('title', 'Modifier le coupon')

@section('content')
<div class="page-shell max-w-4xl">
    <p class="eyebrow">Administration</p>
    <h1 class="mt-2 section-title">Modifier le coupon : {{ $coupon->code }}</h1>

    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="panel mt-6 grid gap-5 p-6">
        @csrf
        @method('PUT')
        @include('admin.coupons.partials.form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.coupons.index') }}" class="btn-secondary">Annuler</a>
            <button type="submit" class="btn-primary">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
