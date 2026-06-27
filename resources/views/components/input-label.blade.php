@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-bold uppercase tracking-wider text-zinc-400 mb-2']) }}>
    {{ $value ?? $slot }}
</label>
