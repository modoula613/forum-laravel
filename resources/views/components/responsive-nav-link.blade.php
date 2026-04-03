@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-full bg-white px-4 py-3 text-start text-base font-semibold text-black transition duration-150 ease-in-out'
            : 'block w-full rounded-full px-4 py-3 text-start text-base font-medium text-white/78 transition duration-150 ease-in-out hover:bg-white/10 hover:text-white focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
