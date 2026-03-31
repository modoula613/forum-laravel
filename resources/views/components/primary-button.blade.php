<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-full border border-transparent bg-[var(--brand)] px-5 py-3 text-xs font-semibold uppercase tracking-[0.22em] text-white shadow-[0_16px_35px_rgba(79,70,229,0.28)] transition duration-150 ease-in-out hover:-translate-y-0.5 hover:bg-[var(--brand-deep)] focus:bg-[var(--brand-deep)] active:bg-[var(--brand-deep)] focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
