@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-full bg-[var(--brand)] px-4 py-2 text-sm font-semibold leading-5 text-white shadow-[0_10px_24px_rgba(79,70,229,0.24)] transition duration-150 ease-in-out'
            : 'inline-flex items-center rounded-full px-4 py-2 text-sm font-medium leading-5 text-slate-600 transition duration-150 ease-in-out hover:bg-white/70 hover:text-slate-900 focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
