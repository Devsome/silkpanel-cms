<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('donation.title') }} — {{ $provider->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <a href="{{ route('donate.index') }}"
                class="inline-flex items-center text-sm text-purple-600 dark:text-purple-400 hover:underline">
                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                {{ __('donation.choose_different_method') }}
            </a>

            <div class="divide-y divide-gray-900/10">

                <div class="grid grid-cols-1 gap-x-8 gap-y-8 py-10 md:grid-cols-3">
                    <div class="px-4 sm:px-0">
                        <h2 class="text-base/7 font-semibold text-gray-900">
                            {{ $provider->name }}
                        </h2>
                        <p class="mt-1 text-sm/6 text-gray-600">
                            {{ __('donation.epin_description') }}
                        </p>
                    </div>

                    <form action="{{ route('donate.redeem-epin', $provider) }}" method="POST"
                        class="bg-white shadow-xs outline outline-gray-900/5 sm:rounded-xl md:col-span-2">
                        @csrf
                        <div class="px-4 py-6 sm:p-8">
                            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-4">
                                    <label for="epin_code" class="block text-sm/6 font-medium text-gray-900">
                                        {{ __('donation.epin_code') }}
                                    </label>
                                    <div class="mt-2">
                                        <input id="epin_code" type="text" name="epin_code" required
                                            value="{{ old('epin_code') }}"
                                            placeholder="{{ __('donation.epin_code_placeholder') }}"
                                            class="block w-full rounded-md border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/20" />
                                        @error('epin_code')
                                            <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="sm:col-span-4">
                                    <label for="epin_secret" class="block text-sm/6 font-medium text-gray-900">
                                        {{ __('donation.epin_secret') }}
                                    </label>
                                    <div class="mt-2">
                                        <input id="epin_secret" type="text" name="epin_secret" required
                                            value="{{ old('epin_secret') }}"
                                            placeholder="{{ __('donation.epin_secret_placeholder') }}"
                                            class="block w-full rounded-md border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/20" />
                                        @error('epin_secret')
                                            <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>


                            </div>
                        </div>
                        <div
                            class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 py-4 sm:px-8">
                            <a href="{{ route('donate.index') }}" type="button"
                                class="text-sm/6 font-semibold text-gray-900 cursor-pointer">
                                {{ __('donation.cancel') }}
                            </a>
                            <button type="submit"
                                class="rounded-md cursor-pointer bg-purple-600 hover:bg-purple-700 focus:ring-purple-500 px-3 py-2 text-sm font-semibold text-white shadow-xs focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600">
                                {{ __('donation.redeem_epin') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
