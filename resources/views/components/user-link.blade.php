@props(['user' => null])

@if ($user)
    <a
        href="{{ route('users.show', $user) }}"
        title="Voir le profil de {{ $user->name }}"
        {{ $attributes->merge(['class' => 'user-link']) }}
    >
        @if (trim((string) $slot) !== '')
            {{ $slot }}
        @else
            {{ $user->name }}
        @endif
    </a>
@else
    <span {{ $attributes->merge(['class' => 'user-link']) }}>
        {{ $slot }}
    </span>
@endif
