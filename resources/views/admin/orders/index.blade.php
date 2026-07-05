@extends('layouts.app')

@section('title', 'Commandes admin')

@section('content')
<div class="page-shell">
    <div class="mb-8">
        <p class="eyebrow">Administration</p>
        <h1 class="mt-2 section-title">Gestion des commandes</h1>
        <p class="mt-3 text-sm text-slate-600">Mettez a jour le statut des commandes clients.</p>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-4">Commande</th>
                        <th class="px-5 py-4">Client</th>
                        <th class="px-5 py-4">Montant</th>
                        <th class="px-5 py-4">Statut</th>
                        <th class="px-5 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($orders as $order)
                        <tr>
                            <td class="px-5 py-4 font-semibold text-slate-950">#{{ $order->id }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $order->user?->name }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ number_format($order->total_amount, 2) }} EUR</td>
                            <td class="px-5 py-4">
                                <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-field w-40">
                                        @foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                                            <option value="{{ $status }}" @selected($order->status === $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn-secondary px-3 py-2">OK</button>
                                </form>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('orders.show', $order) }}" class="btn-secondary px-3 py-2">Details</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
