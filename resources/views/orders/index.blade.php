@extends('layouts.app')

@section('title', 'Mes commandes')

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
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="eyebrow">Suivi</p>
            <h1 class="mt-2 section-title">Mes commandes</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                Consultez l'historique, le statut et le montant de vos commandes.
            </p>
        </div>
        <a href="{{ route('products.index') }}" class="btn-secondary">Continuer mes achats</a>
    </div>

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

    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-4">Commande</th>
                        <th class="px-5 py-4">Date</th>
                        <th class="px-5 py-4">Montant</th>
                        <th class="px-5 py-4">Statut</th>
                        <th class="px-5 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50">
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-950">#{{ $order->id }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $order->order_date->format('d/m/Y H:i') }}</td>
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-950">{{ number_format($order->total_amount, 2) }} EUR</td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="status-pill {{ $statusStyles[$order->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('orders.show', $order) }}" class="btn-secondary px-3 py-2">Voir</a>
                                    
                                    @if($order->status === 'pending')
                                        <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette commande ? Les articles seront remis en stock.');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-lg bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100 transition focus:outline-none">
                                                Annuler
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm font-medium text-slate-600">
                                Aucune commande trouvee.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('chatbot.widget')
@endsection
