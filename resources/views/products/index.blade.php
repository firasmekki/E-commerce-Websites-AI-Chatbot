@extends('layouts.app')

@section('title', 'Boutique NextCommerce')

@section('content')
<div class="bg-[#f7f5ef]">
    <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <section class="grid gap-5 lg:grid-cols-[1.15fr_0.85fr] lg:items-stretch">
            <div class="rounded-lg border border-stone-200 bg-[#fffdf8] p-5 shadow-sm sm:p-7 lg:p-9">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-emerald-800">Stock live</span>
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-800">{{ $stats['in_stock'] }} produits disponibles</span>
                </div>

                <h1 class="mt-5 max-w-3xl text-4xl font-black tracking-tight text-stone-950 sm:text-5xl lg:text-6xl">
                    La vitrine tech qui donne envie de cliquer.
                </h1>
                <p class="mt-4 max-w-2xl text-base leading-7 text-stone-600">
                    Produits disponibles, prix visibles, categories rapides et selection en stock. Le visiteur voit tout de suite ce qu'il peut acheter.
                </p>

                <form action="{{ route('products.index') }}" method="GET" class="mt-7">
                    <div class="grid gap-3 rounded-lg border border-stone-200 bg-white p-2 shadow-sm md:grid-cols-[1fr_190px_auto]">
                        <label class="sr-only" for="search">Rechercher</label>
                        <input id="search" type="text" name="search" class="form-field border-0 bg-transparent shadow-none focus:ring-0" placeholder="Chercher un produit..." value="{{ request('search') }}">

                        <label class="sr-only" for="category">Categorie</label>
                        <select id="category" name="category" class="form-field border-0 bg-stone-50 shadow-none focus:ring-0">
                            <option value="">Toutes cat&eacute;gories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="rounded-lg bg-stone-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-700">Explorer</button>
                    </div>
                </form>

                <div class="mt-8 grid grid-cols-3 gap-3">
                    <div class="border-l-2 border-emerald-500 pl-3">
                        <p class="text-2xl font-black text-stone-950">{{ $stats['products'] }}</p>
                        <p class="text-xs font-semibold text-stone-500">Produits</p>
                    </div>
                    <div class="border-l-2 border-amber-500 pl-3">
                        <p class="text-2xl font-black text-stone-950">{{ $stats['categories'] }}</p>
                        <p class="text-xs font-semibold text-stone-500">Cat&eacute;gories</p>
                    </div>
                    <div class="border-l-2 border-rose-500 pl-3">
                        <p class="text-2xl font-black text-stone-950">{{ $products->count() }}</p>
                        <p class="text-xs font-semibold text-stone-500">Resultats</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-1">
                @if($heroProduct)
                    <a href="{{ route('products.show', $heroProduct) }}" class="group relative min-h-[360px] overflow-hidden rounded-lg bg-stone-950 shadow-sm">
                        @if($heroProduct->image_url)
                            <img src="{{ $heroProduct->image_url }}" class="absolute inset-0 h-full w-full object-cover opacity-90 transition duration-500 group-hover:scale-105" alt="{{ $heroProduct->name }}">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center bg-stone-200 text-sm font-semibold text-stone-500">Image indisponible</div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-stone-950 via-stone-950/30 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 p-5 text-white sm:p-6">
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-black text-stone-950">Produit phare</span>
                            <h2 class="mt-4 text-3xl font-black leading-tight">{{ $heroProduct->name }}</h2>
                            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                                    @if($heroProduct->hasActiveSale())
                                        <span class="text-sm font-bold text-white/70 line-through">{{ number_format($heroProduct->price, 2) }} EUR</span>
                                        <span class="text-2xl font-black">{{ number_format($heroProduct->sale_price, 2) }} EUR</span>
                                    @else
                                        <span class="text-2xl font-black">{{ number_format($heroProduct->price, 2) }} EUR</span>
                                    @endif
                                <span class="rounded-full bg-emerald-400 px-3 py-1 text-xs font-black text-emerald-950">{{ $heroProduct->stock }} en stock</span>
                            </div>
                        </div>
                    </a>
                @endif

                @if($featuredProducts->isNotEmpty())
                    <div class="grid gap-3">
                        @foreach($featuredProducts->take(2) as $product)
                            <a href="{{ route('products.show', $product) }}" class="group grid grid-cols-[92px_1fr] overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" class="h-full min-h-[112px] w-full object-cover transition duration-300 group-hover:scale-105" alt="{{ $product->name }}">
                                @else
                                    <div class="flex min-h-[112px] items-center justify-center bg-stone-100 text-xs text-stone-500">Image</div>
                                @endif
                                <div class="p-4">
                                    <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">{{ $product->category?->name ?? 'Sans categorie' }}</p>
                                    <h3 class="mt-1 font-black text-stone-950">{{ $product->name }}</h3>
                                    @if($product->hasActiveSale())
                                        <div class="mt-2 flex items-end gap-2">
                                            <span class="text-xs font-bold text-stone-400 line-through">{{ number_format($product->price, 2) }} EUR</span>
                                            <span class="text-sm font-black text-emerald-700">{{ number_format($product->sale_price, 2) }} EUR</span>
                                        </div>
                                    @else
                                        <p class="mt-2 text-sm font-bold text-stone-700">{{ number_format($product->price, 2) }} EUR</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        @if($mainPromo)
            @php
                $discountPercent = $mainPromo->price > 0
                    ? round((($mainPromo->price - $mainPromo->sale_price) / $mainPromo->price) * 100)
                    : 0;
                $mainPromoLabel = '-' . $discountPercent . '%';
            @endphp
            <section class="mt-6 overflow-hidden rounded-lg border border-emerald-100 bg-gradient-to-br from-white via-emerald-50 to-amber-50 shadow-sm">
                <div class="grid gap-6 p-5 text-stone-950 sm:p-7 lg:grid-cols-[1fr_auto] lg:items-center lg:p-8">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-emerald-600 px-3 py-1 text-xs font-black uppercase tracking-wide text-white">Selection promo</span>
                            @if($mainPromo->sale_ends_at)
                                <span class="rounded-full border border-emerald-200 bg-white px-3 py-1 text-xs font-bold text-emerald-800">Jusqu'au {{ $mainPromo->sale_ends_at->format('d/m/Y') }}</span>
                            @endif
                        </div>

                        <h2 class="mt-5 max-w-4xl text-3xl font-black tracking-tight sm:text-4xl">
                            Produits en promotion : jusqu'a {{ $mainPromoLabel }} sur {{ $mainPromo->name }}.
                        </h2>
                        <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600 sm:text-base">
                            Des offres visibles directement sur les produits : prix barre, prix reduit et selection prete a acheter.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 lg:justify-end">
                        <div class="min-w-[180px] rounded-lg border border-emerald-200 bg-white px-5 py-3 text-center shadow-sm">
                            <span class="block text-[10px] font-black uppercase tracking-[0.18em] text-emerald-700">Avantage client</span>
                            <span class="mt-1 block text-2xl font-black text-stone-950">{{ $mainPromoLabel }}</span>
                        </div>
                        <a href="#catalogue" class="rounded-lg bg-stone-950 px-5 py-3 text-sm font-black text-white transition hover:bg-emerald-700">Voir les promos</a>
                    </div>
                </div>
            </section>
        @endif

        <section class="mt-10">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-emerald-700">Catalogue</p>
                    <h2 class="mt-2 text-2xl font-black tracking-tight text-stone-950 sm:text-3xl">Produits disponibles</h2>
                </div>

                @if(request('search') || request('category'))
                    <a href="{{ route('products.index') }}" class="rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-bold text-stone-700 shadow-sm transition hover:border-stone-950 hover:text-stone-950">Reinitialiser</a>
                @endif
            </div>

            <div id="catalogue" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @forelse($products as $product)
                    <article class="group overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <a href="{{ route('products.show', $product) }}" class="relative block overflow-hidden bg-stone-100">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105" alt="{{ $product->name }}">
                            @else
                                <div class="flex aspect-[4/3] w-full items-center justify-center bg-stone-100 text-sm font-semibold text-stone-500">Image indisponible</div>
                            @endif
                            <div class="absolute left-3 top-3 rounded-full bg-white/95 px-3 py-1 text-xs font-black text-stone-950 shadow-sm">
                                {{ number_format($product->hasActiveSale() ? $product->sale_price : $product->price, 2) }} EUR
                            </div>
                            @if($product->hasActiveSale())
                                <div class="absolute right-3 top-3 rounded-full bg-rose-500 px-3 py-1 text-xs font-black text-white">Promo</div>
                            @endif
                            @if($product->stock > 0)
                                <div class="absolute bottom-3 left-3 rounded-full bg-emerald-400 px-3 py-1 text-xs font-black text-emerald-950">En stock</div>
                            @else
                                <div class="absolute bottom-3 left-3 rounded-full bg-rose-500 px-3 py-1 text-xs font-black text-white">Rupture</div>
                            @endif
                        </a>

                        <div class="p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-stone-500">{{ $product->category?->name ?? 'Sans categorie' }}</p>
                            <h3 class="mt-1 line-clamp-2 min-h-[3.5rem] text-lg font-black leading-snug text-stone-950">
                                <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                            </h3>

                            <div class="mt-2 flex items-center gap-1">
                                <div class="flex items-center text-amber-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="h-3.5 w-3.5 {{ $product->reviews_count > 0 && $i <= round($product->reviews_avg_rating) ? 'fill-current' : 'text-stone-200' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-xs font-semibold text-stone-500">
                                    {{ $product->reviews_count > 0 ? number_format($product->reviews_avg_rating, 1) . ' (' . $product->reviews_count . ')' : 'Aucun avis' }}
                                </span>
                            </div>

                            <p class="mt-3 line-clamp-2 text-sm leading-6 text-stone-600">{{ Str::limit($product->description, 90) }}</p>

                            <div class="mt-4 flex items-center justify-between gap-3">
                                <span class="text-xs font-bold text-stone-500">{{ $product->stock }} dispo.</span>
                                <a href="{{ route('products.show', $product) }}" class="rounded-lg bg-stone-950 px-3 py-2 text-sm font-bold text-white transition hover:bg-emerald-700">Voir</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg border border-stone-200 bg-white p-8 text-center shadow-sm sm:col-span-2 lg:col-span-4">
                        <p class="text-sm font-bold text-stone-700">Aucun produit trouve.</p>
                        <a href="{{ route('products.index') }}" class="mt-4 inline-flex rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-bold text-stone-700">Reinitialiser les filtres</a>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>

@include('chatbot.widget')
@endsection
