@extends('layouts.app')

@section('title', 'Commande #' . $order->id)

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
    <nav class="mb-6 flex items-center gap-2 text-sm text-slate-500" aria-label="Fil d'Ariane">
        <a href="{{ route('orders.index') }}" class="font-medium text-slate-700 hover:text-slate-950">Mes commandes</a>
        <span>/</span>
        <span>#{{ $order->id }}</span>
    </nav>

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

    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="eyebrow">Commande</p>
            <h1 class="mt-2 section-title">Commande #{{ $order->id }}</h1>
            <p class="mt-3 text-sm text-slate-600">Passee le {{ $order->order_date->format('d/m/Y H:i') }}</p>
        </div>
        <span class="status-pill {{ $statusStyles[$order->status] ?? 'bg-slate-100 text-slate-700' }}">
            {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
        </span>
    </div>

    <section class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <div class="panel overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-bold text-slate-950">Articles commandes</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-4">Produit</th>
                            <th class="px-5 py-4">Prix unitaire</th>
                            <th class="px-5 py-4">Quantite</th>
                            <th class="px-5 py-4 text-right">Sous-total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($order->orderItems as $item)
                            <tr>
                                <td class="px-5 py-4 font-semibold text-slate-950">{{ $item->product->name }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ number_format($item->unit_price, 2) }} EUR</td>
                                <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $item->quantity }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-slate-950">{{ number_format($item->subtotal, 2) }} EUR</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @php
                $rawSubtotal = $order->orderItems->sum('subtotal');
            @endphp
            
            <div class="border-t border-slate-200 bg-slate-50 px-5 py-4 space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="font-semibold text-slate-600">Sous-total</span>
                    <span class="font-semibold text-slate-800">{{ number_format($rawSubtotal, 2) }} EUR</span>
                </div>
                
                @if($order->discount_amount > 0)
                    <div class="flex items-center justify-between text-sm text-emerald-700">
                        <span class="font-semibold">Réduction</span>
                        <span class="font-bold">-{{ number_format($order->discount_amount, 2) }} EUR</span>
                    </div>
                @endif
                
                <div class="flex items-center justify-between border-t border-slate-200 pt-2">
                    <span class="text-sm font-semibold text-slate-600">Total payé</span>
                    <span class="text-xl font-bold text-slate-950">{{ number_format($order->total_amount, 2) }} EUR</span>
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="panel p-5">
                <h2 class="text-base font-bold text-slate-950">Informations client</h2>
                <dl class="mt-5 space-y-4 text-sm">
                    <div>
                        <dt class="font-semibold text-slate-500">Client</dt>
                        <dd class="mt-1 text-slate-950">{{ $order->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-500">Email</dt>
                        <dd class="mt-1 break-all text-slate-950">{{ $order->user->email }}</dd>
                    </div>
                </dl>
            </div>

            @if($order->status === 'pending')
                <div class="panel p-5 border-rose-100 bg-rose-50/20 text-center">
                    <h3 class="text-sm font-bold text-rose-900 mb-2">Besoin d'annuler ?</h3>
                    <p class="text-xs text-rose-700 mb-4 leading-relaxed">Vous pouvez encore annuler cette commande car elle n'a pas encore été traitée. Les articles seront remis en stock.</p>
                    <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette commande ? Les articles seront remis en stock.');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full inline-flex justify-center items-center rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-700 transition focus:outline-none">
                            Annuler la commande
                        </button>
                    </form>
                </div>
            @endif

            <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="btn-primary w-full flex items-center justify-center gap-2 mb-3">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0a2.25 2.25 0 11-4.5 0m-3.6 0a2.25 2.25 0 11-4.5 0m3.36 0h10.08m-11.25-2.25h12.0m-11.25-2.25h12.0M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                Imprimer / Facture PDF
            </a>
            <a href="{{ route('orders.index') }}" class="btn-secondary w-full">Retour aux commandes</a>
        </aside>
    </section>
</div>

@include('chatbot.widget')
@endsection
