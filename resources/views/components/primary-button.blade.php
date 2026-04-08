<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-primary-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 dark:bg-primary-500 dark:shadow-none dark:hover:bg-primary-400 dark:focus-visible:outline-primary-500 cursor-pointer']) }}>
    {{ $slot }}
</button>
