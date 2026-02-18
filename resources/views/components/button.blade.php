<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-block rounded-md border border-primary-600 bg-primary-600 px-12 py-3 text-sm font-medium text-white transition duration-150 ease-in-out hover:bg-transparent hover:text-primary-600 focus:outline-none focus:ring focus:ring-primary-300 dark:focus:ring-primary-800 active:text-primary-700 cursor-pointer']) }}>
    {{ $slot }}
</button>
