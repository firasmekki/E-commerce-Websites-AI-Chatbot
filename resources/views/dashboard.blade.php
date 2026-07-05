@extends('layouts.app')

@section('title', 'Tableau de bord')

@php
    $statusStyles = [
        'pending' => 'bg-amber-50 text-amber-700',
        'processing' => 'bg-sky-50 text-sky-700',
        'shipped' => 'bg-indigo-50 text-indigo-700',
        'delivered' => 'bg-emerald-50 text-emerald-700',
        'cancelled' => 'bg-rose-50 text-rose-700',
    ];

    $statusLabels = [
        'pending' => 'En attente',
        'processing' => 'En traitement',
        'shipped' => 'Expediee',
        'delivered' => 'Livree',
        'cancelled' => 'Annulee',
    ];
@endphp

@section('content')
<div class="page-shell">

    @if(session('success'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800">
            {{ session('error') }}
        </div>
    @endif

    <section class="mb-8 overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-sky-50 text-slate-950 shadow-sm">
        <div class="grid gap-6 p-6 lg:grid-cols-[1fr_320px] lg:p-8">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-700">Espace client</p>
                <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">
                    Bonjour {{ Auth::user()->name }}
                </h1>
                <p class="mt-4 max-w-2xl text-sm leading-6 text-slate-600">
                    Suivez vos commandes, retrouvez les nouveaux produits et reprenez rapidement vos achats sur NextCommerce.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-lg bg-sky-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800">
                        Explorer le catalogue
                    </a>
                    <a href="{{ route('cart.index') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                        Reprendre mon panier
                    </a>
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50">
                        Voir mes commandes
                    </a>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white/80 p-5 shadow-sm">
                <p class="text-sm font-semibold text-slate-600">Resume client</p>
                <dl class="mt-5 grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs text-slate-500">Commandes</dt>
                        <dd class="mt-1 text-2xl font-bold">{{ $stats['orders'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">En attente</dt>
                        <dd class="mt-1 text-2xl font-bold">{{ $stats['pendingOrders'] }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-xs text-slate-500">Total depense</dt>
                        <dd class="mt-1 text-2xl font-bold">{{ number_format($stats['totalSpent'], 2) }} EUR</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <section class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-sky-100 bg-sky-50/70 p-5 shadow-sm">
            <p class="text-sm font-semibold text-sky-700">Produits</p>
            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['products'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Articles dans le catalogue</p>
        </div>
        <div class="rounded-lg border border-emerald-100 bg-emerald-50/70 p-5 shadow-sm">
            <p class="text-sm font-semibold text-emerald-700">Disponibles</p>
            <p class="mt-2 text-3xl font-bold text-emerald-700">{{ $stats['inStock'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Produits en stock</p>
        </div>
        <div class="rounded-lg border border-violet-100 bg-violet-50/70 p-5 shadow-sm">
            <p class="text-sm font-semibold text-violet-700">Categories</p>
            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['categories'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Rayons actifs</p>
        </div>
        <div class="rounded-lg border border-amber-100 bg-amber-50/70 p-5 shadow-sm">
            <p class="text-sm font-semibold text-amber-700">Panier</p>
            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['cartItems'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Articles en attente</p>
        </div>
    </section>

    @if($saleProducts->isNotEmpty())
        <section class="mb-8 rounded-lg border border-emerald-100 bg-gradient-to-br from-white via-emerald-50 to-amber-50 p-5 shadow-sm">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-700">Promos actives</p>
                    <h2 class="mt-1 text-xl font-black text-slate-950">Offres a ne pas manquer</h2>
                </div>
                <a href="{{ route('products.index') }}" class="text-sm font-bold text-slate-700 hover:text-slate-950">Voir la boutique</a>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                @foreach($saleProducts as $product)
                    <a href="{{ route('products.show', $product) }}" class="rounded-lg border border-emerald-100 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">{{ $product->category?->name ?? 'Sans categorie' }}</p>
                        <h3 class="mt-1 font-black text-slate-950">{{ $product->name }}</h3>
                        <div class="mt-3 flex items-end gap-2">
                            <span class="text-xs font-bold text-slate-400 line-through">{{ number_format($product->price, 2) }} EUR</span>
                            <span class="text-lg font-black text-emerald-700">{{ number_format($product->sale_price, 2) }} EUR</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="grid gap-6 lg:grid-cols-[1fr_380px]">
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-white to-slate-50 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200/80 px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-slate-950">Dernieres commandes</h2>
                    <p class="mt-1 text-sm text-slate-500">Suivi rapide de vos achats recents.</p>
                </div>
                <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-slate-700 hover:text-slate-950">Tout voir</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentOrders as $order)
                    <a href="{{ route('orders.show', $order) }}" class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-slate-50">
                        <div>
                            <p class="font-semibold text-slate-950">Commande #{{ $order->id }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $order->order_date->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-slate-950">{{ number_format($order->total_amount, 2) }} EUR</p>
                            <span class="mt-2 status-pill {{ $statusStyles[$order->status] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-10 text-center">
                        <p class="text-sm font-medium text-slate-600">Aucune commande pour le moment.</p>
                        <a href="{{ route('products.index') }}" class="mt-4 btn-secondary">Commencer mes achats</a>
                    </div>
                @endforelse
            </div>
        </div>

        <aside class="space-y-6">
            <div class="rounded-lg border border-slate-200 bg-gradient-to-br from-white to-sky-50 p-5 shadow-sm">
                <h2 class="text-base font-bold text-slate-950">Actions rapides</h2>
                <div class="mt-4 grid gap-3">
                    <a href="{{ route('products.index') }}" class="inline-flex w-full items-center justify-center rounded-lg bg-sky-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800">Rechercher un produit</a>
                    <a href="{{ route('categories.index') }}" class="btn-secondary w-full">Parcourir les categories</a>
                    <a href="{{ route('profile.edit') }}" class="btn-secondary w-full">Modifier mon profil</a>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-gradient-to-br from-white to-emerald-50 p-5 shadow-sm">
                <h2 class="text-base font-bold text-slate-950">Nouveaux produits</h2>
                <div class="mt-4 space-y-4">
                    @forelse($recentProducts as $product)
                        <a href="{{ route('products.show', $product) }}" class="flex items-center gap-3">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-white text-xs font-bold text-sky-700 ring-1 ring-slate-200">
                                {{ Str::upper(Str::substr($product->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-950">{{ $product->name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $product->category?->name ?? 'Sans categorie' }}</p>
                                <p class="mt-1 text-sm font-semibold text-slate-700">{{ number_format($product->price, 2) }} EUR</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-slate-600">Aucun produit disponible.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </section>
</div>

@include('chatbot.widget')
@endsection
