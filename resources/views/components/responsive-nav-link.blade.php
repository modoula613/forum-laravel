@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-2xl bg-[var(--brand)] px-4 py-3 text-start text-base font-medium text-white shadow-[0_10px_24px_rgba(79,70,229,0.22)] transition duration-150 ease-in-out'
            : 'block w-full rounded-2xl px-4 py-3 text-start text-base font-medium text-slate-600 transition duration-150 ease-in-out hover:bg-white/70 hover:text-slate-900 focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
