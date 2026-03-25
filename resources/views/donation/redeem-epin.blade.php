<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('donation.title') }} — {{ $provider->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-6">
                <a href="{{ route('donate.index') }}"
                    class="inline-flex items-center text-sm text-purple-600 dark:text-purple-400 hover:underline">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    {{ __('donation.choose_different_method') }}
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('donation.epin_description') }}
                </p>

                <form action="{{ route('donate.redeem-epin', $provider) }}" method="POST">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label for="epin_code"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('donation.epin_code') }}
                            </label>
                            <input type="text" name="epin_code" id="epin_code" value="{{ old('epin_code') }}"
                                required
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                placeholder="{{ __('donation.epin_code_placeholder') }}">
                            @error('epin_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="epin_secret"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('donation.epin_secret') }}
                            </label>
                            <input type="text" name="epin_secret" id="epin_secret" value="{{ old('epin_secret') }}"
                                required
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                placeholder="{{ __('donation.epin_secret_placeholder') }}">
                            @error('epin_secret')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 bg-purple-600 hover:bg-purple-700 focus:ring-purple-500 cursor-pointer">
                            {{ __('donation.redeem_epin') }}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
