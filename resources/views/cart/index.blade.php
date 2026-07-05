@extends('layouts.app')

@section('title', 'Panier')

@section('content')
<div class="bg-[#f7f5ef]">
    <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-700">Checkout</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-stone-950 sm:text-4xl">Mon panier</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-stone-600">Ajustez les quantites, appliquez une remise et validez votre commande.</p>
            </div>
            <a href="{{ route('products.index') }}" class="rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-black text-stone-700 shadow-sm transition hover:border-stone-950">Continuer mes achats</a>
        </div>

        @if(session('success'))
            <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm font-bold text-rose-800">{{ session('error') }}</div>
        @endif

        <section class="grid gap-6 lg:grid-cols-[1fr_380px] lg:items-start">
            <div class="space-y-4">
                @forelse($cartItems as $item)
                    <article class="grid gap-4 rounded-lg border border-stone-200 bg-white p-4 shadow-sm sm:grid-cols-[120px_1fr_auto] sm:items-center">
                        <a href="{{ route('products.show', $item['product']) }}" class="overflow-hidden rounded-lg bg-stone-100">
                            @if($item['product']->image_url)
                                <img src="{{ $item['product']->image_url }}" class="aspect-square w-full object-cover" alt="{{ $item['product']->name }}">
                            @else
                                <div class="flex aspect-square w-full items-center justify-center text-xs font-semibold text-stone-500">Image</div>
                            @endif
                        </a>

                        <div>
                            <p class="text-xs font-black uppercase tracking-wide text-emerald-700">{{ $item['product']->category?->name ?? 'Sans categorie' }}</p>
                            <h2 class="mt-1 text-lg font-black text-stone-950">{{ $item['product']->name }}</h2>
                            @if($item['product']->hasActiveSale())
                                <div class="mt-2 flex items-end gap-2">
                                    <span class="text-xs font-bold text-stone-400 line-through">{{ number_format($item['product']->price, 2) }} EUR</span>
                                    <span class="text-sm font-black text-emerald-700">{{ number_format($item['product']->sale_price, 2) }} EUR / unite</span>
                                </div>
                            @else
                                <p class="mt-2 text-sm font-bold text-stone-600">{{ number_format($item['product']->price, 2) }} EUR / unite</p>
                            @endif
                            <p class="mt-1 text-xs font-semibold text-stone-500">Stock disponible : {{ $item['product']->stock }}</p>
                        </div>

                        <div class="grid gap-3 sm:min-w-[190px]">
                            <form action="{{ route('cart.update', $item['product']) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['product']->stock }}" class="form-field h-10 w-24">
                                <button type="submit" class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-black text-stone-700 transition hover:border-stone-950">OK</button>
                            </form>
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-lg font-black text-stone-950">{{ number_format($item['subtotal'], 2) }} EUR</p>
                                <form action="{{ route('cart.destroy', $item['product']) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-black text-rose-600 hover:text-rose-800">Retirer</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg border border-stone-200 bg-white p-10 text-center shadow-sm">
                        <p class="text-sm font-black text-stone-700">Votre panier est vide.</p>
                        <a href="{{ route('products.index') }}" class="mt-4 inline-flex rounded-lg bg-stone-950 px-5 py-3 text-sm font-black text-white">Voir les produits</a>
                    </div>
                @endforelse
            </div>

            <aside class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm lg:sticky lg:top-24">
                <h2 class="text-lg font-black text-stone-950">Resume commande</h2>
                <p class="mt-1 text-sm text-stone-500">{{ $cartItems->count() }} article(s) dans le panier</p>

                @if(!$coupon && !$cartItems->isEmpty())
                    <form action="{{ route('cart.coupon.apply') }}" method="POST" class="mt-5 rounded-lg border border-dashed border-emerald-300 bg-emerald-50 p-4">
                        @csrf
                        <label for="code" class="block text-xs font-black uppercase tracking-[0.18em] text-emerald-800">Code promo</label>
                        <div class="mt-2 flex gap-2">
                            <input type="text" name="code" id="code" placeholder="EX: SOLDES10" class="form-field flex-1 uppercase" required>
                            <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-black text-white">OK</button>
                        </div>
                    </form>
                @endif

                @if($coupon)
                    <div class="mt-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-800">Remise active</p>
                                <p class="mt-1 font-mono text-lg font-black text-emerald-900">{{ $coupon['code'] }}</p>
                            </div>
                            <form action="{{ route('cart.coupon.remove') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-black text-rose-600">Retirer</button>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="mt-5 space-y-3 border-t border-stone-200 pt-5">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-bold text-stone-600">Sous-total</span>
                        <span class="font-black text-stone-950">{{ number_format($total, 2) }} EUR</span>
                    </div>
                    @if($coupon && $discount > 0)
                        <div class="flex items-center justify-between text-sm text-emerald-700">
                            <span class="font-bold">Reduction</span>
                            <span class="font-black">-{{ number_format($discount, 2) }} EUR</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between border-t border-stone-200 pt-4">
                        <span class="text-base font-black text-stone-950">Total</span>
                        <span class="text-3xl font-black text-stone-950">{{ number_format($grandTotal, 2) }} EUR</span>
                    </div>
                </div>

                <form action="{{ route('cart.checkout') }}" method="POST" class="mt-5">
                    @csrf
                    <button type="submit" class="w-full rounded-lg bg-stone-950 px-5 py-3 text-sm font-black text-white transition hover:bg-emerald-700 disabled:bg-stone-300" @disabled($cartItems->isEmpty())>
                        Valider la commande
                    </button>
                </form>
                <p class="mt-3 text-center text-xs font-semibold text-stone-500">Paiement et suivi securises apres validation.</p>
            </aside>
        </section>
    </div>
</div>

@include('chatbot.widget')
@endsection
