@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="bg-[#f7f5ef]">
    <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <nav class="mb-5 flex items-center gap-2 text-sm text-stone-500" aria-label="Fil d'Ariane">
            <a href="{{ route('categories.index') }}" class="font-bold text-stone-700 hover:text-stone-950">Categories</a>
            <span>/</span>
            <span class="text-stone-950">{{ $category->name }}</span>
        </nav>

        <section class="mb-8 rounded-lg border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-700">Categorie</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-stone-950 sm:text-4xl">{{ $category->name }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-stone-600">{{ $category->description }}</p>
                </div>
                <span class="rounded-full bg-stone-950 px-4 py-2 text-sm font-black text-white">{{ $category->products->count() }} produit(s)</span>
            </div>
        </section>

        <section class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($category->products as $product)
                <article class="group overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <a href="{{ route('products.show', $product) }}" class="relative block overflow-hidden bg-stone-100">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105" alt="{{ $product->name }}">
                        @else
                            <div class="flex aspect-[4/3] w-full items-center justify-center bg-stone-100 text-sm font-semibold text-stone-500">Image indisponible</div>
                        @endif
                        @if($product->hasActiveSale())
                            <span class="absolute left-3 top-3 rounded-full bg-rose-500 px-3 py-1 text-xs font-black text-white">Promo</span>
                        @endif
                    </a>

                    <div class="p-4">
                        <h2 class="line-clamp-2 min-h-[3.5rem] text-lg font-black leading-snug text-stone-950">{{ $product->name }}</h2>
                        <p class="mt-3 line-clamp-2 text-sm leading-6 text-stone-600">{{ Str::limit($product->description, 90) }}</p>

                        <div class="mt-5 flex items-end justify-between gap-3">
                            <div>
                                @if($product->hasActiveSale())
                                    <p class="text-xs font-bold text-stone-400 line-through">{{ number_format($product->price, 2) }} EUR</p>
                                    <p class="text-lg font-black text-emerald-700">{{ number_format($product->sale_price, 2) }} EUR</p>
                                @else
                                    <p class="text-lg font-black text-stone-950">{{ number_format($product->price, 2) }} EUR</p>
                                @endif
                            </div>
                            @if($product->stock > 0)
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-700">En stock</span>
                            @else
                                <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-black text-rose-700">Rupture</span>
                            @endif
                        </div>

                        <a href="{{ route('products.show', $product) }}" class="mt-5 inline-flex w-full justify-center rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-black text-stone-700 transition hover:border-stone-950">Voir details</a>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-stone-200 bg-white p-8 text-center shadow-sm sm:col-span-2 lg:col-span-4">
                    <p class="text-sm font-bold text-stone-700">Aucun produit dans cette categorie.</p>
                </div>
            @endforelse
        </section>
    </div>
</div>

@include('chatbot.widget')
@endsection
