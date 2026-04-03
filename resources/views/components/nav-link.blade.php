@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center rounded-full bg-[rgba(29,155,240,0.12)] px-4 py-3 text-sm font-semibold leading-5 text-[var(--brand-deep)] transition duration-200 ease-in-out'
            : 'flex items-center rounded-full px-4 py-3 text-sm font-medium leading-5 text-slate-700 transition duration-200 ease-in-out hover:bg-[rgba(29,155,240,0.08)] hover:text-slate-950 focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
