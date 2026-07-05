@extends('layouts.app')

@section('title', 'Admin produits')

@section('content')
<div class="page-shell" x-data="{ selectedProduct: null }">
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="eyebrow">Administration</p>
            <h1 class="mt-2 section-title">Gestion des produits</h1>
            <p class="mt-3 text-sm text-slate-600">Ajoutez, modifiez ou supprimez les produits du catalogue.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn-primary">Ajouter un produit</a>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-4">Produit</th>
                        <th class="px-5 py-4">Categorie</th>
                        <th class="px-5 py-4">Prix</th>
                        <th class="px-5 py-4">Promo</th>
                        <th class="px-5 py-4">Stock</th>
                        <th class="px-5 py-4">Note moyenne</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($products as $product)
                        <tr>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-12 w-12 rounded-lg object-cover ring-1 ring-slate-200">
                                    @else
                                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-500">IMG</div>
                                    @endif
                                    <div>
                                        <button type="button" @click="selectedProduct = {{ $product->id }}" class="text-left font-semibold text-slate-950 hover:text-indigo-600 hover:underline transition focus:outline-none">
                                            {{ $product->name }}
                                        </button>
                                        @if($product->is_featured)
                                            <p class="mt-1 text-xs font-bold text-emerald-700">Mis en avant</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $product->category?->name ?? 'Sans categorie' }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ number_format($product->price, 2) }} EUR</td>
                            <td class="px-5 py-4">
                                @if($product->hasActiveSale())
                                    <span class="status-pill bg-emerald-50 text-emerald-700">{{ number_format($product->sale_price, 2) }} EUR</span>
                                @else
                                    <span class="text-xs text-slate-400">Aucune</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $product->stock }}</td>
                            <td class="px-5 py-4">
                                @if($product->reviews_count > 0)
                                    <div class="flex items-center gap-1 font-semibold text-slate-950">
                                        <span class="text-amber-400 text-sm">★</span>
                                        <span>{{ number_format($product->reviews_avg_rating, 1) }}</span>
                                        <span class="text-xs font-normal text-slate-500">({{ $product->reviews_count }})</span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic font-normal">Aucun avis</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn-secondary px-3 py-2">Modifier</a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg bg-rose-600 px-3 py-2 text-sm font-semibold text-white hover:bg-rose-700">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Product Detail Modals -->
    @foreach($products as $product)
        <div x-show="selectedProduct === {{ $product->id }}" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             x-cloak
             style="display: none;"
             role="dialog"
             aria-modal="true">
            
            <!-- Backdrop with fade transition -->
            <div x-show="selectedProduct === {{ $product->id }}"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="selectedProduct = null"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

            <!-- Modal Wrapper -->
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                
                <!-- Modal Card with scale transition -->
                <div x-show="selectedProduct === {{ $product->id }}"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @keydown.escape.window="selectedProduct = null"
                     class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 w-full sm:max-w-3xl border border-slate-100">
                    
                    <!-- Top header color bar -->
                    <div class="h-2 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900"></div>

                    <!-- Close button -->
                    <button type="button" 
                            @click="selectedProduct = null" 
                            class="absolute right-4 top-5 rounded-full p-1.5 text-slate-400 hover:bg-slate-50 hover:text-slate-600 transition focus:outline-none"
                            aria-label="Fermer">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div class="px-6 py-6 sm:p-8">
                        <!-- Product Title and Category -->
                        <div class="mb-6">
                            <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                                {{ $product->category?->name ?? 'Sans catégorie' }}
                            </span>
                            <h2 class="mt-2 text-2xl font-bold text-slate-900 sm:text-3xl tracking-tight">{{ $product->name }}</h2>
                        </div>

                        <!-- Main Info Grid (Image + Specs) -->
                        <div class="grid grid-cols-1 gap-8 md:grid-cols-12 mb-8">
                            
                            <!-- Left: Product Image -->
                            <div class="md:col-span-5 flex flex-col items-center justify-center bg-slate-50 rounded-xl p-4 border border-slate-100 aspect-square overflow-hidden relative group">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain rounded-lg transition duration-300 group-hover:scale-105">
                                @else
                                    <div class="text-center p-6 flex flex-col items-center justify-center">
                                        <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375 0 11-.75 0 .375 0 01.75 0z" />
                                        </svg>
                                        <span class="mt-2 block text-xs font-medium text-slate-400">Aucune image</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Right: Detailed Specs -->
                            <div class="md:col-span-7 flex flex-col justify-between space-y-4">
                                <div class="space-y-4">
                                    <!-- Price & Stock panel -->
                                    <div class="grid grid-cols-2 gap-4 bg-slate-50 rounded-xl p-4 border border-slate-100">
                                        <div>
                                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Prix</span>
                                            <span class="text-xl sm:text-2xl font-bold text-slate-900">{{ number_format($product->price, 2) }} EUR</span>
                                        </div>
                                        <div>
                                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Stock</span>
                                            @if($product->stock > 5)
                                                <span class="inline-flex items-center gap-1.5 text-emerald-700 font-semibold text-sm mt-1 bg-emerald-50 px-2.5 py-0.5 rounded-full ring-1 ring-inset ring-emerald-600/10">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    {{ $product->stock }} disponibles
                                                </span>
                                            @elseif($product->stock > 0)
                                                <span class="inline-flex items-center gap-1.5 text-amber-700 font-semibold text-sm mt-1 bg-amber-50 px-2.5 py-0.5 rounded-full ring-1 ring-inset ring-amber-600/10">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                    {{ $product->stock }} restant(s)
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 text-rose-700 font-semibold text-sm mt-1 bg-rose-50 px-2.5 py-0.5 rounded-full ring-1 ring-inset ring-rose-600/10">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                                    Rupture de stock
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Description</h4>
                                        <p class="text-sm text-slate-600 leading-relaxed max-h-[120px] overflow-y-auto pr-1">
                                            {{ $product->description ?? 'Aucune description disponible pour ce produit.' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Average rating card -->
                                <div class="bg-indigo-50/50 rounded-xl p-4 border border-indigo-100/50 flex items-center justify-between">
                                    <div>
                                        <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Note Moyenne</span>
                                        <div class="flex items-center gap-1.5 mt-1">
                                            @if($product->reviews_count > 0)
                                                <div class="flex items-center text-amber-400 text-lg">
                                                    @php
                                                        $avg = round($product->reviews_avg_rating);
                                                    @endphp
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <span>{{ $i <= $avg ? '★' : '☆' }}</span>
                                                    @endfor
                                                </div>
                                                <span class="text-base font-bold text-slate-900">{{ number_format($product->reviews_avg_rating, 1) }}/5</span>
                                            @else
                                                <span class="text-sm text-slate-500 italic">Aucune note</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Avis</span>
                                        <span class="text-sm font-semibold text-slate-800">{{ $product->reviews_count }} avis client(s)</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-slate-100 my-6"></div>

                        <!-- Bottom Section: Customer Reviews -->
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                                <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                </svg>
                                Avis des clients
                            </h3>

                            @if($product->reviews->count() > 0)
                                <div class="space-y-4 max-h-[220px] overflow-y-auto pr-1">
                                    @foreach($product->reviews as $review)
                                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 hover:border-indigo-100 transition duration-200">
                                            <div class="flex items-start justify-between gap-4">
                                                <!-- Client Avatar + Name -->
                                                <div class="flex items-center gap-3">
                                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-xs font-bold text-white uppercase shadow-sm">
                                                        {{ substr($review->user?->name ?? 'C', 0, 2) }}
                                                    </div>
                                                    <div>
                                                        <h5 class="text-sm font-semibold text-slate-900">{{ $review->user?->name ?? 'Client' }}</h5>
                                                        <p class="text-[11px] text-slate-400">{{ $review->created_at?->format('d/m/Y') }}</p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Client Rating -->
                                                <div class="flex items-center text-amber-400 text-sm bg-white px-2 py-0.5 rounded-full border border-slate-100 shadow-sm">
                                                    <span class="mr-1">★</span>
                                                    <span class="font-bold text-slate-800 text-[12px]">{{ number_format($review->rating, 1) }}</span>
                                                </div>
                                            </div>
                                            @if($review->comment)
                                                <p class="mt-3 text-sm text-slate-600 leading-relaxed italic bg-white/50 rounded-lg p-2.5 border border-slate-100/50">
                                                    "{{ $review->comment }}"
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-6 bg-slate-50 rounded-xl border border-slate-100 border-dashed">
                                    <svg class="mx-auto h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                    </svg>
                                    <p class="mt-2 text-xs font-medium text-slate-400">Aucun avis n'a encore été laissé pour ce produit.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-100">
                        <button type="button" 
                                @click="selectedProduct = null" 
                                class="btn-secondary px-4 py-2">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
