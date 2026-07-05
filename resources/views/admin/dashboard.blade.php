@extends('layouts.app')

@section('title', 'Dashboard admin')

@php
    $statusStyles = [
        'pending' => 'bg-amber-50 text-amber-700',
        'processing' => 'bg-sky-50 text-sky-700',
        'shipped' => 'bg-indigo-50 text-indigo-700',
        'delivered' => 'bg-emerald-50 text-emerald-700',
        'cancelled' => 'bg-rose-50 text-rose-700',
    ];
@endphp

@section('content')
<div class="page-shell">
    <section class="mb-8 rounded-lg border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-sky-50 p-6 shadow-sm lg:p-8">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-700">Back-office</p>
        <div class="mt-3 flex flex-col justify-between gap-4 lg:flex-row lg:items-end">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Dashboard administrateur</h1>
                <p class="mt-4 max-w-2xl text-sm leading-6 text-slate-600">
                    Pilotez les ventes, les commandes, le catalogue et les clients depuis un seul espace.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.products.create') }}" class="btn-primary">Ajouter produit</a>
                <a href="{{ route('admin.categories.create') }}" class="btn-primary">Ajouter catégorie</a>
                <a href="{{ route('admin.customers.create') }}" class="btn-secondary">Ajouter client</a>
            </div>
        </div>
    </section>

    <section class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-sky-100 bg-sky-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-sky-700">Chiffre d'affaires</p>
            <p class="mt-2 text-3xl font-bold text-slate-950">{{ number_format($stats['revenue'], 2) }} EUR</p>
            <p class="mt-2 text-sm text-slate-600">Revenu total commandes</p>
        </div>
        <div class="rounded-lg border border-amber-100 bg-amber-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-amber-700">Commandes</p>
            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['orders'] }}</p>
            <p class="mt-2 text-sm text-slate-600">{{ $stats['pendingOrders'] }} en attente</p>
        </div>
        <div class="rounded-lg border border-emerald-100 bg-emerald-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-emerald-700">Clients</p>
            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['customers'] }}</p>
            <p class="mt-2 text-sm text-slate-600">{{ $stats['activeCustomers'] }} actifs, {{ $stats['refusedCustomers'] }} refuses</p>
        </div>
        <div class="rounded-lg border border-violet-100 bg-violet-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-violet-700">Produits</p>
            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['products'] }}</p>
            <p class="mt-2 text-sm text-slate-600">{{ $stats['lowStock'] }} stock faible</p>
        </div>
    </section>

    <!-- Graphiques de performance -->
    <section class="mb-8 grid gap-6 md:grid-cols-2">
        <div class="panel p-5">
            <h2 class="font-bold text-slate-950 mb-4">Évolution des revenus (EUR)</h2>
            <div class="relative h-72">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        <div class="panel p-5">
            <h2 class="font-bold text-slate-950 mb-4">Répartition des produits par catégorie</h2>
            <div class="relative h-72 flex justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="panel overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="font-bold text-slate-950">Commandes recentes</h2>
                    <p class="mt-1 text-sm text-slate-500">Suivi operationnel des ventes.</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-sky-700">Gerer</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentOrders as $order)
                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <div>
                            <p class="font-semibold text-slate-950">#{{ $order->id }} - {{ $order->user?->name }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $order->order_date->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-slate-950">{{ number_format($order->total_amount, 2) }} EUR</p>
                            <span class="mt-2 status-pill {{ $statusStyles[$order->status] ?? 'bg-slate-100 text-slate-700' }}">{{ $order->status }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-slate-600">Aucune commande.</div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="panel p-5">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold text-slate-950">Clients recents</h2>
                    <a href="{{ route('admin.customers.index') }}" class="text-sm font-semibold text-sky-700">Gerer</a>
                </div>
                <div class="mt-4 space-y-3">
                    @forelse($recentCustomers as $customer)
                        <div class="flex items-center justify-between gap-3 rounded-lg bg-slate-50 p-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-950">{{ $customer->name }}</p>
                                <p class="text-xs text-slate-500">{{ $customer->email }}</p>
                            </div>
                            <span class="status-pill {{ $customer->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ $customer->status }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-600">Aucun client.</p>
                    @endforelse
                </div>
            </div>

            <div class="panel p-5">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold text-slate-950">Stock faible</h2>
                    <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-sky-700">Catalogue</a>
                </div>
                <div class="mt-4 space-y-3">
                    @forelse($lowStockProducts as $product)
                        <div class="flex items-center justify-between gap-3 rounded-lg bg-slate-50 p-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-950">{{ $product->name }}</p>
                                <p class="text-xs text-slate-500">{{ $product->category?->name ?? 'Sans categorie' }}</p>
                            </div>
                            <span class="font-bold text-rose-700">{{ $product->stock }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-600">Aucun stock critique.</p>
                    @endforelse
                </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Graphique des revenus
        const revenueData = @json($monthlyRevenue);
        const revenueLabels = revenueData.map(item => item.month);
        const revenueAmounts = revenueData.map(item => item.amount);

        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Revenus (€)',
                    data: revenueAmounts,
                    borderColor: 'rgb(14, 165, 233)', // sky-500
                    backgroundColor: 'rgba(14, 165, 233, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // 2. Graphique des catégories
        const categoryData = @json($categoriesBreakdown);
        const categoryLabels = categoryData.map(item => item.name);
        const categoryCounts = categoryData.map(item => item.count);

        const ctxCategory = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctxCategory, {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryCounts,
                    backgroundColor: [
                        'rgba(14, 165, 233, 0.8)', // sky
                        'rgba(168, 85, 247, 0.8)', // purple
                        'rgba(234, 179, 8, 0.8)', // yellow
                        'rgba(16, 185, 129, 0.8)', // emerald
                        'rgba(244, 63, 94, 0.8)', // rose
                        'rgba(100, 116, 139, 0.8)' // slate
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
