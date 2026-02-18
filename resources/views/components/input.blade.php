@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => 'bg-primary-500 text-gray-900 dark:text-white px-4 py-2 rounded
         focus-visible:outline-none
         focus-visible:ring-1
         focus-visible:ring-primary-500
         focus:border-primary-500
         focus:ring-1
         focus:ring-primary-500
         rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm transition duration-150 dark:border-gray-600 dark:bg-gray-800 dark:text-white disabled:cursor-not-allowed disabled:bg-gray-100 dark:disabled:bg-gray-900',
]) !!}>
