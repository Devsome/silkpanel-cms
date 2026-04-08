@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge([
        'class' =>
            'block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-gray-800 dark:text-white dark:outline-gray-600 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-500 disabled:outline-gray-200 dark:disabled:bg-gray-900 dark:disabled:text-gray-500',
    ]) }}>
