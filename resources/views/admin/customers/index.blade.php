@extends('layouts.app')

@section('title', 'Clients')

@section('content')
<div class="page-shell">
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="eyebrow">Administration</p>
            <h1 class="mt-2 section-title">Gestion des clients</h1>
            <p class="mt-3 text-sm text-slate-600">Ajoutez, modifiez, acceptez ou refusez les comptes clients.</p>
        </div>
        <a href="{{ route('admin.customers.create') }}" class="btn-primary">Ajouter un client</a>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-4">Client</th>
                        <th class="px-5 py-4">Email</th>
                        <th class="px-5 py-4">Statut</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($customers as $customer)
                        <tr>
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ $customer->name }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $customer->email }}</td>
                            <td class="px-5 py-4">
                                <span class="status-pill {{ $customer->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ $customer->status }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    @if($customer->status !== 'active')
                                        <form action="{{ route('admin.customers.accept', $customer) }}" method="POST">@csrf @method('PATCH')<button class="btn-secondary px-3 py-2">Accepter</button></form>
                                    @endif
                                    @if($customer->status !== 'refused')
                                        <form action="{{ route('admin.customers.refuse', $customer) }}" method="POST">@csrf @method('PATCH')<button class="rounded-lg bg-amber-600 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-700">Refuser</button></form>
                                    @endif
                                    <a href="{{ route('admin.customers.edit', $customer) }}" class="btn-secondary px-3 py-2">Modifier</a>
                                    <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST">@csrf @method('DELETE')<button class="rounded-lg bg-rose-600 px-3 py-2 text-sm font-semibold text-white hover:bg-rose-700" onclick="return confirm('Supprimer ce client ?')">Supprimer</button></form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
