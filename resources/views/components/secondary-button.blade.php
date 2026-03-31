<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center rounded-full border border-[rgba(90,60,40,0.14)] bg-white/80 px-5 py-3 text-xs font-semibold uppercase tracking-[0.22em] text-stone-700 shadow-sm transition duration-150 ease-in-out hover:bg-white focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:ring-offset-2 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
