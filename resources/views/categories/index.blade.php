@extends('layouts.app')

@section('title', auth()->check() && auth()->user()->is_admin ? 'Gestion des categories' : 'Categories')

@section('content')
<div class="bg-[#f7f5ef]">
    <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @if(auth()->check() && auth()->user()->is_admin)
            <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-700">Administration</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-stone-950">Gestion des categories</h1>
                    <p class="mt-3 text-sm text-stone-600">Organisez les rayons visibles dans la boutique.</p>
                </div>
                <a href="{{ route('admin.categories.create') }}" class="rounded-lg bg-stone-950 px-4 py-2 text-sm font-black text-white transition hover:bg-emerald-700">Ajouter une categorie</a>
            </div>

            @if(session('success'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm font-bold text-rose-800">{{ session('error') }}</div>
            @endif

            <div class="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200 text-sm">
                        <thead class="bg-stone-50 text-left text-xs font-black uppercase tracking-wide text-stone-500">
                            <tr>
                                <th class="px-5 py-4">Categorie</th>
                                <th class="px-5 py-4">Description</th>
                                <th class="px-5 py-4">Produits</th>
                                <th class="px-5 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 bg-white">
                            @forelse($categories as $category)
                                <tr>
                                    <td class="px-5 py-4 font-black text-stone-950">{{ $category->name }}</td>
                                    <td class="px-5 py-4 text-stone-600">{{ Str::limit($category->description, 70) }}</td>
                                    <td class="px-5 py-4"><span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-700">{{ $category->products->count() }}</span></td>
                                    <td class="px-5 py-4">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.categories.edit', $category) }}" class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-bold text-stone-700">Modifier</a>
                                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg bg-rose-600 px-3 py-2 text-sm font-bold text-white" onclick="return confirm('Supprimer cette categorie ?')">Supprimer</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="p-8 text-center text-sm text-stone-600">Aucune categorie.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <section class="mb-8 rounded-lg border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
                <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-700">Rayons</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-stone-950 sm:text-4xl">Explorez par categorie</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-stone-600">Accedez rapidement aux univers produits et trouvez le bon article plus vite.</p>
            </section>

            <section class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($categories as $category)
                    <a href="{{ route('categories.show', $category) }}" class="group overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-stone-950 text-lg font-black text-white">
                                    {{ Str::upper(Str::substr($category->name, 0, 2)) }}
                                </div>
                                <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-black text-stone-700">{{ $category->products->count() }} produit(s)</span>
                            </div>
                            <h2 class="mt-5 text-xl font-black text-stone-950">{{ $category->name }}</h2>
                            <p class="mt-3 min-h-[3rem] text-sm leading-6 text-stone-600">{{ Str::limit($category->description, 120) }}</p>
                            <div class="mt-5 inline-flex rounded-lg bg-stone-950 px-4 py-2 text-sm font-black text-white transition group-hover:bg-emerald-700">Voir les produits</div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-lg border border-stone-200 bg-white p-8 text-center shadow-sm sm:col-span-2 lg:col-span-3">
                        <p class="text-sm font-bold text-stone-700">Aucune categorie trouvee.</p>
                    </div>
                @endforelse
            </section>
        @endif
    </div>
</div>

@include('chatbot.widget')
@endsection
