@section('title', 'Connexion')

<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-700">Connexion</p>
        <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-950">Bienvenue sur NextCommerce</h2>
        <p class="mt-2 text-sm leading-6 text-slate-600">
            Accedez a vos commandes, vos informations et votre assistant client.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Adresse email" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="vous@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" value="Mot de passe" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-sky-700 transition hover:text-sky-900" href="{{ route('password.request') }}">
                        Mot de passe oublie ?
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="mt-2 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="Votre mot de passe" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="flex items-center">
            <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-sky-700 shadow-sm focus:ring-sky-700" name="remember">
            <span class="ms-2 text-sm text-slate-600">Se souvenir de moi</span>
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-700 focus:ring-offset-2">
            Se connecter
        </button>

        <p class="text-center text-sm text-slate-600">
            Pas encore de compte ?
            <a href="{{ route('register') }}" class="font-semibold text-sky-700 hover:text-sky-900">Creer un compte</a>
        </p>
    </form>
</x-guest-layout>
