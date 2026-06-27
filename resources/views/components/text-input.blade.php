@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-black border-2 border-white/10 focus:border-violet-600 text-white rounded-none py-2 px-3 text-sm focus:outline-none transition-colors']) }}>
