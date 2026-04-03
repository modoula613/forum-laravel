@props([
    'label' => 'Ajouter un emoji',
    'helper' => 'Ajoute une reaction rapide a ton message.',
    'emojis' => ['🙂', '😂', '🔥', '👏', '😍', '🤔', '😮', '🥲', '💬', '❤️'],
])

<div class="space-y-2">
    <div class="emoji-toolbar" role="toolbar" aria-label="{{ $label }}">
        @foreach ($emojis as $emoji)
            <button
                type="button"
                class="emoji-toolbar__button"
                @click="insertEmoji(@js($emoji))"
                aria-label="Ajouter {{ $emoji }}"
            >
                <span aria-hidden="true">{{ $emoji }}</span>
            </button>
        @endforeach
    </div>
    <p class="emoji-toolbar__helper">{{ $helper }}</p>
</div>
