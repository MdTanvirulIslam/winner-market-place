@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'panel-input']) }}>
