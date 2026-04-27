<x-app-layout>
    <div class="flex items-center justify-center min-h-[70vh] px-4 py-16 sm:px-6 lg:px-8">
        <div class="w-full max-w-lg text-center">
            <p class="text-8xl font-extrabold tracking-tight text-gray-300 dark:text-gray-700">
                429
            </p>

            <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                {{ __('errors.429.title') }}
            </h1>

            <p class="mt-4 text-lg leading-relaxed text-gray-600 dark:text-gray-400">
                {{ __('errors.429.message') }}
            </p>

            <div class="mt-10">
                <a href="{{ url('/') }}"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm transition-all duration-150 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-indigo-500 dark:hover:bg-indigo-600 dark:focus:ring-offset-gray-900">
                    {{ __('errors.back_to_home') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
