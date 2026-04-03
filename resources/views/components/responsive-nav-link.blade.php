@props(['active'])

@php
$classes = 'app-mobile-nav-link '.(($active ?? false) ? 'is-active' : '');
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
