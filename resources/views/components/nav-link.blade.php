@props(['active'])

@php
$classes = 'app-nav-link '.(($active ?? false) ? 'is-active' : '');
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
