@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-2xl bg-[rgba(29,155,240,0.12)] px-4 py-3 text-start text-base font-semibold text-[var(--brand-deep)] transition duration-150 ease-in-out'
            : 'block w-full rounded-2xl px-4 py-3 text-start text-base font-medium text-slate-700 transition duration-150 ease-in-out hover:bg-[rgba(29,155,240,0.08)] hover:text-slate-950 focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
