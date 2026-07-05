@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-slate-900 bg-slate-100 py-2 pe-4 ps-3 text-start text-base font-semibold text-slate-950 transition duration-150 ease-in-out focus:outline-none'
            : 'block w-full border-l-4 border-transparent py-2 pe-4 ps-3 text-start text-base font-medium text-slate-600 transition duration-150 ease-in-out hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900 focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
