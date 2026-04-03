<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-full border border-transparent bg-[linear-gradient(135deg,var(--brand),var(--brand-deep))] px-5 py-3 text-xs font-semibold uppercase tracking-[0.18em] text-white shadow-[0_18px_34px_rgba(34,92,143,0.24)] transition duration-200 ease-in-out hover:-translate-y-0.5 hover:shadow-[0_22px_40px_rgba(34,92,143,0.28)] focus:bg-[var(--brand-deep)] active:bg-[var(--brand-deep)] focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
