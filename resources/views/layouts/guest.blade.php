<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen bg-gradient-to-br from-white via-slate-50 to-sky-50 px-4 py-8">
            <div class="mx-auto grid min-h-[calc(100vh-4rem)] w-full max-w-6xl items-center gap-8 lg:grid-cols-[1fr_440px]">
                <section class="hidden lg:block">
                    <a href="/" class="inline-flex items-center gap-3">
                        <x-application-logo class="h-12 w-12 text-slate-900" />
                        <span class="text-xl font-bold text-slate-950">NextCommerce</span>
                    </a>

                    <div class="mt-12 max-w-xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-700">Espace securise</p>
                        <h1 class="mt-4 text-4xl font-bold tracking-tight text-slate-950">
                            Gere tes achats avec une experience plus simple.
                        </h1>
                        <p class="mt-5 text-sm leading-7 text-slate-600">
                            Connecte-toi pour suivre tes commandes, consulter les produits disponibles et utiliser l'assistant NextCommerce.
                        </p>
                    </div>

                    <div class="mt-10 grid max-w-xl grid-cols-3 gap-4">
                        <div class="rounded-lg border border-sky-100 bg-white/80 p-4 shadow-sm">
                            <p class="text-2xl font-bold text-slate-950">24/7</p>
                            <p class="mt-1 text-xs text-slate-500">Assistant client</p>
                        </div>
                        <div class="rounded-lg border border-emerald-100 bg-white/80 p-4 shadow-sm">
                            <p class="text-2xl font-bold text-slate-950">Stock</p>
                            <p class="mt-1 text-xs text-slate-500">Suivi en direct</p>
                        </div>
                        <div class="rounded-lg border border-violet-100 bg-white/80 p-4 shadow-sm">
                            <p class="text-2xl font-bold text-slate-950">Suivi</p>
                            <p class="mt-1 text-xs text-slate-500">Commandes</p>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="mb-8 flex justify-center lg:hidden">
                        <a href="/" class="flex items-center gap-3">
                            <x-application-logo class="h-11 w-11 text-slate-900" />
                            <span class="text-lg font-bold text-slate-950">NextCommerce</span>
                        </a>
                    </div>

                    <div class="w-full overflow-hidden rounded-lg border border-slate-200 bg-white/90 px-6 py-6 shadow-sm backdrop-blur">
                        {{ $slot }}
                    </div>
                </section>
            </div>
        </div>
    </body>
</html>
