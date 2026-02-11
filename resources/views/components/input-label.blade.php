@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm/6 font-medium text-gray-900 dark:text-white']) }}>
    {{ $value ?? $slot }}
</label>
