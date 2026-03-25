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

            <div class="mb-6">
                <a href="{{ route('donate.index') }}"
                    class="inline-flex items-center text-sm text-purple-600 dark:text-purple-400 hover:underline">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    {{ __('donation.choose_different_method') }}
                </a>
            </div>

            @if ($packages->isEmpty())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-gray-500 dark:text-gray-400">
                        {{ __('donation.no_packages') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($packages as $package)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg flex flex-col">
                            @if ($package->image)
                                <img src="{{ Storage::url($package->image) }}" alt="{{ $package->name }}"
                                    class="w-full h-48 object-cover">
                            @endif

                            <div class="p-6 flex flex-col flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $package->name }}
                                </h3>

                                @if ($package->description)
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $package->description }}
                                    </p>
                                @endif

                                <div class="mt-4 flex items-center justify-between">
                                    <span class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                        {{ number_format($package->silk_amount) }} {{ __('donation.silk') }}
                                    </span>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $package->currency }} {{ number_format($package->price, 2) }}
                                    </span>
                                </div>

                                <div class="mt-6">
                                    <form action="{{ route('donate.checkout', $package) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="provider" value="{{ $provider->slug->value }}">
                                        <button type="submit"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 bg-purple-600 hover:bg-purple-700 focus:ring-purple-500 cursor-pointer">
                                            {{ __('donation.buy_now') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
