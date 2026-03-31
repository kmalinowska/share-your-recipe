@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm text-base-content']) }}>
    {{ $value ?? $slot }}
</label>
