<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 bg-violet-700 hover:bg-black text-white font-bold tracking-wider uppercase rounded-none border-2 border-violet-700 hover:border-white px-6 py-3 text-sm transition-all duration-150 ease-out cursor-pointer select-none']) }}>
    {{ $slot }}
</button>
