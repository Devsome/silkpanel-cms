<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('dashboard.title') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ __('dashboard.welcome', ['name' => Auth::user()->name]) }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ Auth::user()->email }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="{{ route('profile.edit') }}"
                    class="group bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-600 transition">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex-shrink-0 w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h4
                                class="font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                {{ __('dashboard.profile') }}
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('dashboard.profile_desc') }}
                            </p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('donate.index') }}"
                    class="group bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md hover:border-emerald-300 dark:hover:border-emerald-600 transition">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex-shrink-0 w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4
                                class="font-semibold text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition">
                                {{ __('dashboard.donations') }}
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('dashboard.donations_desc') }}
                            </p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('voting.index') }}"
                    class="group bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md hover:border-amber-300 dark:hover:border-amber-600 transition">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex-shrink-0 w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                            </svg>
                        </div>
                        <div>
                            <h4
                                class="font-semibold text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition">
                                {{ __('dashboard.voting') }}
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('dashboard.voting_desc') }}
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
