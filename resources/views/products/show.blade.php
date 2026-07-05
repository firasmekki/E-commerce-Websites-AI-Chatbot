@extends('layouts.app')

@section('title', $product->name)

@section('content')
@php
    $promoLabel = null;
    $promoPrice = null;

    if ($product->hasActiveSale()) {
        $promoPrice = (float) $product->sale_price;
        $discountPercent = $product->price > 0
            ? round((($product->price - $product->sale_price) / $product->price) * 100)
            : 0;
        $promoLabel = '-' . $discountPercent . '%';
    }
@endphp

<div class="bg-[#f7f5ef]">
    <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <nav class="mb-5 flex flex-wrap items-center gap-2 text-sm text-stone-500" aria-label="Fil d'Ariane">
            <a href="{{ route('products.index') }}" class="font-bold text-stone-700 hover:text-stone-950">Boutique</a>
            <span>/</span>
            <span>{{ $product->category?->name ?? 'Sans categorie' }}</span>
            <span>/</span>
            <span class="text-stone-950">{{ $product->name }}</span>
        </nav>

        <section class="grid gap-6 lg:grid-cols-[1.08fr_0.92fr] lg:items-start">
            <div class="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
                <div class="relative bg-stone-100">
                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" class="aspect-[5/4] w-full object-cover lg:aspect-[4/3]" alt="{{ $product->name }}">
                    @else
                        <div class="flex aspect-[5/4] w-full items-center justify-center text-sm font-semibold text-stone-500 lg:aspect-[4/3]">
                            Image indisponible
                        </div>
                    @endif

                    <div class="absolute left-4 top-4 flex flex-wrap gap-2">
                        @if($product->stock > 0)
                            <span class="rounded-full bg-emerald-500 px-3 py-1 text-xs font-black uppercase tracking-wide text-emerald-950">En stock</span>
                        @else
                            <span class="rounded-full bg-rose-600 px-3 py-1 text-xs font-black uppercase tracking-wide text-white">Rupture</span>
                        @endif
                        @if($product->hasActiveSale())
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-black uppercase tracking-wide text-stone-950 shadow-sm">Promo {{ $promoLabel }}</span>
                        @endif
                    </div>
                </div>

                <div class="grid gap-4 border-t border-stone-200 p-5 sm:grid-cols-3">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-700">Livraison</p>
                        <p class="mt-1 text-sm font-semibold text-stone-700">Traitement rapide</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-700">Paiement</p>
                        <p class="mt-1 text-sm font-semibold text-stone-700">Commande securisee</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-700">Support</p>
                        <p class="mt-1 text-sm font-semibold text-stone-700">Suivi client</p>
                    </div>
                </div>
            </div>

            <aside class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm sm:p-6 lg:sticky lg:top-24">
                <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-700">{{ $product->category?->name ?? 'Sans categorie' }}</p>
                <h1 class="mt-3 text-3xl font-black tracking-tight text-stone-950 sm:text-4xl">{{ $product->name }}</h1>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <div class="flex items-center text-amber-400">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-5 w-5 {{ $i <= round($averageRating) ? 'fill-current' : 'text-stone-200' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-sm font-black text-stone-800">{{ number_format($averageRating, 1) }}</span>
                    <span class="text-sm font-semibold text-stone-500">({{ $reviewsCount }} avis)</span>
                </div>

                <div class="mt-6 rounded-lg bg-stone-50 p-4">
                    @if($promoPrice !== null)
                        <div class="flex flex-wrap items-end gap-3">
                            <span class="text-sm font-bold text-stone-400 line-through">{{ number_format($product->price, 2) }} EUR</span>
                            <span class="text-4xl font-black text-emerald-700">{{ number_format($promoPrice, 2) }} EUR</span>
                        </div>
                        <p class="mt-2 text-xs font-bold uppercase tracking-wide text-emerald-700">Prix promotionnel affiche</p>
                    @else
                        <p class="text-4xl font-black text-stone-950">{{ number_format($product->price, 2) }} EUR</p>
                    @endif
                </div>

                <div class="mt-5">
                    @if($product->stock > 0)
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                            Disponible maintenant : {{ $product->stock }} unite(s) en stock.
                        </div>
                    @else
                        <div class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm font-bold text-rose-800">
                            Ce produit est actuellement indisponible.
                        </div>
                    @endif
                </div>

                <div class="mt-6">
                    <h2 class="text-xs font-black uppercase tracking-[0.18em] text-stone-500">Description</h2>
                    <p class="mt-3 leading-7 text-stone-700">{{ $product->description }}</p>
                </div>

                <div class="mt-6 grid gap-3">
                    @if($product->stock > 0)
                        @auth
                            <form action="{{ route('cart.store', $product) }}" method="POST">
                                @csrf
                                <div class="grid gap-3 sm:grid-cols-[120px_1fr]">
                                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-field h-12">
                                    <button type="submit" class="h-12 rounded-lg bg-stone-950 px-5 text-sm font-black text-white transition hover:bg-emerald-700">Ajouter au panier</button>
                                </div>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex h-12 items-center justify-center rounded-lg bg-stone-950 px-5 text-sm font-black text-white transition hover:bg-emerald-700">Connectez-vous pour acheter</a>
                        @endauth
                    @else
                        <button class="h-12 rounded-lg bg-stone-300 px-5 text-sm font-black text-white" disabled>Indisponible</button>
                    @endif
                    <a href="{{ route('products.index') }}" class="inline-flex h-12 items-center justify-center rounded-lg border border-stone-300 bg-white px-5 text-sm font-black text-stone-700 transition hover:border-stone-950 hover:text-stone-950">Retour au catalogue</a>
                </div>
            </aside>
        </section>

        @if($relatedProducts->isNotEmpty())
            <section class="mt-10">
                <div class="mb-5">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-700">A decouvrir</p>
                    <h2 class="mt-2 text-2xl font-black tracking-tight text-stone-950">Produits similaires</h2>
                </div>

                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($relatedProducts as $related)
                        <a href="{{ route('products.show', $related) }}" class="group overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            @if($related->image_url)
                                <img src="{{ $related->image_url }}" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105" alt="{{ $related->name }}">
                            @else
                                <div class="flex aspect-[4/3] w-full items-center justify-center bg-stone-100 text-sm font-semibold text-stone-500">Image indisponible</div>
                            @endif
                            <div class="p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-stone-500">{{ $related->category?->name ?? 'Sans categorie' }}</p>
                                <h3 class="mt-1 line-clamp-2 min-h-[3.5rem] text-lg font-black leading-snug text-stone-950">{{ $related->name }}</h3>
                                <div class="mt-3 flex items-center justify-between gap-3">
                                    <span class="font-black text-stone-950">{{ number_format($related->price, 2) }} EUR</span>
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-800">Voir</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="mt-12 border-t border-stone-200 pt-10">
            <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-700">Avis clients</p>
                    <h2 class="mt-2 text-2xl font-black tracking-tight text-stone-950">Evaluation de la qualite ({{ $reviewsCount }})</h2>
                </div>
                <div class="flex items-center gap-2 text-sm font-bold text-stone-700">
                    <span class="text-2xl font-black text-stone-950">{{ number_format($averageRating, 1) }}</span>
                    <span>/ 5</span>
                </div>
            </div>

            <div class="grid gap-8 lg:grid-cols-3 lg:items-start">
                <div class="space-y-4 lg:col-span-2">
                    @forelse($product->reviews as $review)
                        <div class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h3 class="font-black text-stone-950">{{ $review->user->name }}</h3>
                                    <p class="text-xs font-semibold text-stone-500">Publie le {{ $review->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="flex items-center text-amber-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="h-4 w-4 {{ $i <= $review->rating ? 'fill-current' : 'text-stone-200' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            <p class="mt-4 rounded-lg border border-stone-100 bg-stone-50 p-4 text-sm leading-6 text-stone-700">
                                {{ $review->comment ?: 'Aucun commentaire laisse.' }}
                            </p>
                        </div>
                    @empty
                        <div class="rounded-lg border border-stone-200 bg-white p-8 text-center shadow-sm">
                            <p class="text-sm font-black text-stone-950">Aucun avis pour le moment.</p>
                            <p class="mt-1 text-sm text-stone-500">Soyez le premier a donner votre avis sur ce produit.</p>
                        </div>
                    @endforelse
                </div>

                <div class="rounded-lg border border-stone-200 bg-white p-6 shadow-sm lg:sticky lg:top-24">
                    @auth
                        <h3 class="font-black text-stone-950">{{ $userReview ? 'Modifier votre avis' : 'Donner votre avis' }}</h3>
                        <p class="mt-1 text-sm text-stone-500">Partagez votre experience sur la qualite de ce produit.</p>

                        @if(session('success'))
                            <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-xs font-bold text-emerald-800">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('products.reviews.store', $product) }}" method="POST" class="mt-5 space-y-4">
                            @csrf
                            <div>
                                <label class="mb-2 block text-xs font-black uppercase tracking-wide text-stone-500">Note qualite</label>
                                <div class="flex items-center gap-1" x-data="{ rating: {{ old('rating', $userReview?->rating ?? 5) }}, hoverRating: 0 }">
                                    <input type="hidden" name="rating" :value="rating">
                                    <template x-for="i in 5">
                                        <button type="button" @click="rating = i" @mouseover="hoverRating = i" @mouseleave="hoverRating = 0" class="text-3xl transition-transform hover:scale-110 focus:outline-none" :class="(hoverRating ? i <= hoverRating : i <= rating) ? 'text-amber-400' : 'text-stone-200'">
                                            &#9733;
                                        </button>
                                    </template>
                                </div>
                                @error('rating')
                                    <p class="mt-1 text-xs font-bold text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="comment" class="mb-2 block text-xs font-black uppercase tracking-wide text-stone-500">Votre commentaire</label>
                                <textarea id="comment" name="comment" rows="4" class="form-field text-stone-900" placeholder="Qualite, emballage, usage...">{{ old('comment', $userReview?->comment ?? '') }}</textarea>
                                @error('comment')
                                    <p class="mt-1 text-xs font-bold text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="w-full rounded-lg bg-stone-950 px-5 py-3 text-sm font-black text-white transition hover:bg-emerald-700">
                                {{ $userReview ? 'Mettre a jour mon avis' : 'Soumettre mon avis' }}
                            </button>
                        </form>
                    @else
                        <div class="py-4 text-center">
                            <p class="text-sm font-black text-stone-950">Connectez-vous pour laisser un avis</p>
                            <p class="mt-1 text-sm text-stone-500">Les avis sont reserves aux clients connectes.</p>
                            <a href="{{ route('login') }}" class="mt-4 inline-flex w-full justify-center rounded-lg bg-stone-950 px-5 py-3 text-sm font-black text-white transition hover:bg-emerald-700">Se connecter</a>
                        </div>
                    @endauth
                </div>
            </div>
        </section>
    </div>
</div>

@include('chatbot.widget')
@endsection
