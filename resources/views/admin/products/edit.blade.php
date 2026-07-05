@extends('layouts.app')

@section('title', 'Modifier un produit')

@section('content')
<div class="page-shell max-w-4xl">
    <p class="eyebrow">Administration</p>
    <h1 class="mt-2 section-title">Modifier le produit</h1>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="panel mt-6 grid gap-5 p-6">
        @csrf
        @method('PUT')
        @include('admin.products.partials.form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="btn-secondary">Annuler</a>
            <button type="submit" class="btn-primary">Mettre a jour</button>
        </div>
    </form>

    <!-- Liste des avis pour ce produit (Administration) -->
    <div class="mt-12 border-t border-slate-200 pt-10">
        <h2 class="text-xl font-bold tracking-tight text-slate-950">Avis des clients ({{ $product->reviews->count() }})</h2>
        <div class="mt-6 space-y-4">
            @forelse($product->reviews as $review)
                <div class="panel p-5 flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-bold text-slate-950">{{ $review->user->name }}</span>
                            <span class="text-slate-400">•</span>
                            <div class="flex items-center text-amber-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="text-base">{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                            <span class="text-xs text-slate-500">({{ $review->created_at->format('d/m/Y H:i') }})</span>
                        </div>
                        @if($review->comment)
                            <p class="mt-3 text-sm text-slate-700 bg-slate-50 border border-slate-100 p-3 rounded-lg leading-6">{{ $review->comment }}</p>
                        @else
                            <p class="mt-3 text-xs italic text-slate-400">Aucun commentaire</p>
                        @endif
                    </div>
                    <div>
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Supprimer cet avis de manière définitive ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg bg-rose-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-rose-700 shadow-sm transition">
                                Supprimer l'avis
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="panel p-8 text-center text-slate-500 text-sm">
                    Aucun avis n'a été publié pour ce produit.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
