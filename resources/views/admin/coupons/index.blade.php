@extends('layouts.app')

@section('title', 'Admin coupons')

@section('content')
<div class="page-shell">
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="eyebrow">Administration</p>
            <h1 class="mt-2 section-title">Gestion des coupons de réduction</h1>
            <p class="mt-3 text-sm text-slate-600">Créez, gérez et activez des codes promotionnels pour la boutique.</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="btn-primary">Ajouter un coupon</a>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800">{{ session('error') }}</div>
    @endif

    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-4">Code Promo</th>
                        <th class="px-5 py-4">Type de remise</th>
                        <th class="px-5 py-4">Valeur</th>
                        <th class="px-5 py-4">Expire le</th>
                        <th class="px-5 py-4">Statut</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($coupons as $coupon)
                        <tr>
                            <td class="px-5 py-4 font-mono font-bold text-slate-900">{{ $coupon->code }}</td>
                            <td class="px-5 py-4 text-slate-600">
                                @if($coupon->type === 'percent')
                                    Pourcentage (%)
                                @else
                                    Montant fixe (EUR)
                                @endif
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-950">
                                @if($coupon->type === 'percent')
                                    {{ number_format($coupon->value, 0) }}%
                                @else
                                    {{ number_format($coupon->value, 2) }} EUR
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $coupon->expires_at ? $coupon->expires_at->format('d/m/Y H:i') : 'Jamais' }}
                            </td>
                            <td class="px-5 py-4">
                                @if(!$coupon->is_active)
                                    <span class="status-pill bg-rose-50 text-rose-700">Désactivé</span>
                                @elseif($coupon->expires_at && $coupon->expires_at->isPast())
                                    <span class="status-pill bg-amber-50 text-amber-700">Expiré</span>
                                @else
                                    <span class="status-pill bg-emerald-50 text-emerald-700">Actif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn-secondary px-3 py-2">Modifier</a>
                                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg bg-rose-600 px-3 py-2 text-sm font-semibold text-white hover:bg-rose-700" onclick="return confirm('Supprimer ce coupon ?')">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-sm text-slate-600">Aucun coupon.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
