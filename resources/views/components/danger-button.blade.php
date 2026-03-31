<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-full border border-transparent bg-rose-600 px-5 py-3 text-xs font-semibold uppercase tracking-[0.22em] text-white shadow-[0_16px_35px_rgba(225,29,72,0.2)] transition duration-150 ease-in-out hover:bg-rose-500 active:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
