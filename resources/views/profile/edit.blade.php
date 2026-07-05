@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<div class="page-shell">
    <section class="mb-8 rounded-lg border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-sky-50 p-6 shadow-sm lg:p-8">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-700">Mon Compte</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Gestion du Profil</h1>
        <p class="mt-2 text-sm text-slate-600">
            Mettez à jour vos informations personnelles, changez votre mot de passe ou gérez votre compte.
        </p>
    </section>

    <div class="space-y-6">
        <div class="panel p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="panel p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="panel p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
