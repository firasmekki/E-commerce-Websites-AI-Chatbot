<nav x-data="{
    open: false,
    searchVisible: true,
    lastScroll: 0,
    initScroll() {
        let locked = false;
        window.addEventListener('scroll', () => {
            if (locked) return;
            const curr = window.scrollY;
            const delta = curr - this.lastScroll;
            if (Math.abs(delta) < 10) return;
            locked = true;
            if (curr > this.lastScroll && curr > 60) {
                this.searchVisible = false;
            } else {
                this.searchVisible = true;
            }
            this.lastScroll = curr;
            setTimeout(() => { locked = false; this.lastScroll = window.scrollY; }, 250);
        });
    }
}" x-init="initScroll()" class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between gap-4">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('products.index') }}" class="flex items-center gap-3">
                        <x-application-logo class="h-9 w-9 text-slate-900" />
                        <span class="hidden text-sm font-semibold tracking-wide text-slate-900 sm:block">NextCommerce</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                    @if(!Auth::user()->is_admin)
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.index')">
                            Boutique
                        </x-nav-link>
                        <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.index')">
                            Cat&eacute;gories
                        </x-nav-link>
                    @endif
                    @if(!Auth::user()->is_admin)
                    <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                        Panier ({{ collect(session('cart', []))->sum('quantity') }})
                    </x-nav-link>
                    <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                        Commandes
                    </x-nav-link>
                    @endif
                    @if(Auth::user()->is_admin)
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        Admin
                    </x-nav-link>
                    <x-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                        Produits
                    </x-nav-link>
                    <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.index') || request()->routeIs('admin.categories.*')">
                        Cat&eacute;gories
                    </x-nav-link>
                    <x-nav-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.*')">
                        Clients
                    </x-nav-link>
                    <x-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                        Commandes
                    </x-nav-link>
                    <x-nav-link :href="route('admin.coupons.index')" :active="request()->routeIs('admin.coupons.*')">
                        Coupons
                    </x-nav-link>
                    @endif
                    @endauth
                </div>
            </div>

            @guest
                <form x-show="searchVisible"
                      action="{{ route('products.index') }}" method="GET" class="hidden flex-1 items-center justify-center lg:flex">
                    <label for="nav-search" class="sr-only">Rechercher</label>
                    <div class="flex w-full max-w-sm items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 transition focus-within:border-slate-400 focus-within:bg-white">
                        <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                        </svg>
                        <input id="nav-search" name="search" value="{{ request('search') }}" class="w-full border-0 bg-transparent p-0 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0" placeholder="Rechercher un produit">
                    </div>
                </form>
            @endguest

            <!-- Settings Dropdown -->
            @auth
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium leading-4 text-slate-600 shadow-sm transition hover:border-slate-300 hover:text-slate-900 focus:outline-none">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            @else
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
                    Connexion
                </a>
                <a href="{{ route('register') }}" class="ml-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700">
                    Cr&eacute;er un compte
                </a>
            </div>
            @endauth

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    @auth
        <div class="hidden sm:block overflow-hidden transition-all duration-200 ease-in-out"
             :class="searchVisible ? 'max-h-20 opacity-100' : 'max-h-0 opacity-0'">
            <div class="border-t border-slate-100 bg-white/80">
            <div class="mx-auto max-w-7xl px-4 py-2 sm:px-6 lg:px-8">
                <form action="{{ route('products.index') }}" method="GET" class="ml-auto max-w-md">
                    <label for="auth-nav-search" class="sr-only">Rechercher</label>
                    <div class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 transition focus-within:border-slate-400 focus-within:bg-white">
                        <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                        </svg>
                        <input id="auth-nav-search" name="search" value="{{ request('search') }}" class="w-full border-0 bg-transparent p-0 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0" placeholder="Rechercher un produit">
                    </div>
                </form>
            </div>
            </div>
        </div>
    @endauth

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <form action="{{ route('products.index') }}" method="GET" class="px-4 pb-3">
                <label for="mobile-search" class="sr-only">Rechercher</label>
                <div class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                    <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                    </svg>
                    <input id="mobile-search" name="search" value="{{ request('search') }}" class="w-full border-0 bg-transparent p-0 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0" placeholder="Rechercher un produit">
                </div>
            </form>
            @auth
            @if(!Auth::user()->is_admin)
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.index')">
                    Boutique
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.index')">
                    Cat&eacute;gories
                </x-responsive-nav-link>
            @endif
            @if(!Auth::user()->is_admin)
            <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                Panier ({{ collect(session('cart', []))->sum('quantity') }})
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                Commandes
            </x-responsive-nav-link>
            @endif
            @if(Auth::user()->is_admin)
            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                Admin
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                Produits
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.index') || request()->routeIs('admin.categories.*')">
                Cat&eacute;gories
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.*')">
                Clients
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                Commandes
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.coupons.index')" :active="request()->routeIs('admin.coupons.*')">
                Coupons
            </x-responsive-nav-link>
            @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @else
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('login')">
                    Connexion
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">
                    Cr&eacute;er un compte
                </x-responsive-nav-link>
            </div>
        </div>
        @endauth
    </div>
</nav>
