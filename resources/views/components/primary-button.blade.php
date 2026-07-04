<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover']) }}>
    {{ $slot }}
</button>
