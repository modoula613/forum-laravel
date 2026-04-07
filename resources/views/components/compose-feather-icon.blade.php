@props(['class' => 'h-5 w-5'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
    <path
        d="M20.4 3.6c-6 .5-10.8 5.3-11.3 11.3L8.1 20l5.1-1c6-.5 10.8-5.3 11.3-11.3l.3-3.1a.75.75 0 0 0-.82-.82l-3.58.32Z"
        stroke="currentColor"
        stroke-width="1.65"
        stroke-linecap="round"
        stroke-linejoin="round"
    />
    <path
        d="M9.5 14.5 20.3 3.7"
        stroke="currentColor"
        stroke-width="1.65"
        stroke-linecap="round"
        stroke-linejoin="round"
    />
    <path
        d="m8.55 16.15 3.1.6M8.95 13.15l4.45.85M10.4 10.2l5.2 1M12.4 7.35l5 1"
        stroke="currentColor"
        stroke-width="1.45"
        stroke-linecap="round"
        stroke-linejoin="round"
    />
</svg>
