@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold uppercase tracking-[0.18em] text-stone-600']) }}>
    {{ $value ?? $slot }}
</label>
