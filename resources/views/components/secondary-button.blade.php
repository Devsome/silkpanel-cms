<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'rounded-md bg-secondary px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:bg-secondary/10 dark:text-secondary dark:shadow-none dark:inset-ring-secondary/5 dark:hover:bg-secondary/20 cursor-pointer']) }}>
    {{ $slot }}
</button>
