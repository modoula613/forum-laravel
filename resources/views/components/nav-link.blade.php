@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center rounded-full bg-white px-4 py-3 text-[1.15rem] font-semibold leading-5 text-black transition duration-200 ease-in-out'
            : 'flex items-center rounded-full px-4 py-3 text-[1.15rem] font-medium leading-5 text-white/78 transition duration-200 ease-in-out hover:bg-white/10 hover:text-white focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
