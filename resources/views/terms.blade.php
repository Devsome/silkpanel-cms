<x-main-layout>
    <section class="min-h-screen bg-white dark:bg-gray-900 pt-16 px-4 sm:px-6 lg:px-8 pb-12">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">
                {{ __('terms.title') }}
            </h1>

            <div class="prose prose-gray dark:prose-invert max-w-none">
                {!! $tosText !!}
            </div>
        </div>
    </section>
</x-main-layout>
