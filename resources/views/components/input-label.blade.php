@props(['value'])

<label {{ $attributes->merge(['class' => 'panel-label']) }}>
    {{ $value ?? $slot }}
</label>
