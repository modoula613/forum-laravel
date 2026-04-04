@props(['user' => null, 'alt' => null])

<span {{ $attributes->merge(['class' => 'inline-flex items-center justify-center overflow-hidden rounded-full']) }}>
    @if ($user)
        <img
            src="{{ $user->profile_photo_url }}"
            alt="{{ $alt ?? 'Photo de profil de '.$user->name }}"
            class="h-full w-full object-cover"
            loading="lazy"
        >
    @else
        <span class="flex h-full w-full items-center justify-center text-xs font-semibold uppercase text-white/80">?</span>
    @endif
</span>
