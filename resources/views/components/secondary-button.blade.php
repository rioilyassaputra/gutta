<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 bg-transparent hover:bg-white text-white hover:text-black font-bold tracking-wider uppercase rounded-none border-2 border-white px-6 py-3 text-sm transition-all duration-150 ease-out cursor-pointer select-none']) }}>
    {{ $slot }}
</button>
