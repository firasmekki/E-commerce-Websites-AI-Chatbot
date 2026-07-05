@extends('layouts.app')

@section('title', 'Admin catégories')

@section('content')
<div class="page-shell">
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="eyebrow">Administration</p>
            <h1 class="mt-2 section-title">Gestion des catégories</h1>
            <p class="mt-3 text-sm text-slate-600">Ajoutez, modifiez ou supprimez les catégories du catalogue.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn-primary">Ajouter une catégorie</a>
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
                        <th class="px-5 py-4">Catégorie</th>
                        <th class="px-5 py-4">Description</th>
                        <th class="px-5 py-4">Produits</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($categories as $category)
                        <tr>
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ $category->name }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ Str::limit($category->description, 60) }}</td>
                            <td class="px-5 py-4">
                                <span class="status-pill bg-sky-50 text-sky-700">{{ $category->products_count }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn-secondary px-3 py-2">Modifier</a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg bg-rose-600 px-3 py-2 text-sm font-semibold text-white hover:bg-rose-700" onclick="return confirm('Supprimer cette catégorie ?')">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-sm text-slate-600">Aucune catégorie.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
