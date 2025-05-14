@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-base font-medium text-gray-700 mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
