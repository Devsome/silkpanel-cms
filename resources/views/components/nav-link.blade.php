@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'inline-flex items-center px-1 pt-0.5 border-b-2 border-primary-700 dark:border-primary-600 dark:hover:border-primary-300 text-sm font-medium leading-5 text-primary-900 focus:outline-none focus:border-primary-700 transition dark:text-white dark:focus:outline-none dark:focus:border-primary-200'
            : 'inline-flex items-center px-1 pt-0.5 border-b-2 border-transparent text-sm font-medium leading-5 text-primary-500 hover:text-primary-700 hover:border-primary-300 focus:outline-none focus:text-primary-700 focus:border-primary-300 transition dark:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
