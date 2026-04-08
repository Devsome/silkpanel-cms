<x-app-layout>
    <section class="bg-white dark:bg-gray-900">
        <div class="lg:grid lg:min-h-screen lg:grid-cols-6">
            <section class="relative flex h-32 items-end bg-gray-900 lg:col-span-3 lg:h-full">
                <img alt="Night" src="{{ Vite::asset('resources/images/banner/background-one.png') }}"
                    class="absolute inset-0 h-full w-full object-cover opacity-80" />
                <div class="hidden lg:relative lg:block lg:p-12">
                    <a class="block text-white" href="/">
                        <span class="sr-only">
                            {{ __('auth/register.home') }}
                        </span>
                    </a>

                    <h2 class="mt-6 text-2xl font-bold text-white sm:text-3xl md:text-4xl">
                        {{ __('auth/register.title', ['app_name' => config('app.name')]) }}
                    </h2>

                    <p class="mt-4 leading-relaxed text-white/90">
                        {{ __('auth/register.description') }}
                    </p>
                </div>
            </section>
            <main aria-label="Main"
                class="flex items-center justify-center px-8 py-8 sm:px-12 lg:col-span-3 lg:py-12 lg:px-16">
                <div class="w-full">
                    <form method="POST" class="mt-2 grid grid-cols-6 gap-6" action="{{ route('register') }}">
                        @csrf

                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white col-span-6 sm:text-3xl md:text-4xl">
                            {{ __('auth/register.form.title') }}
                        </h1>

                        <div class="col-span-6">
                            <p class="mt-4 leading-relaxed text-black dark:text-gray-300">
                                {{ __('auth/register.form.description') }}
                            </p>
                        </div>

                        <x-auth-session-status class="mb-4 col-span-6" :status="session('status')" />

                        <div class="col-span-6">
                            <x-validation-errors class="mb-4" />
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <x-label for="silkroad_id" value="{{ __('auth/register.form.silkroad_id') }}" />
                            <x-input id="silkroad_id" class="block mt-1 w-full dark:bg-gray-800 dark:text-white"
                                type="text" name="silkroad_id" :value="old('silkroad_id')" required autofocus />
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <x-label for="name" value="{{ __('auth/register.form.name') }}" />
                            <x-input id="name" class="block mt-1 w-full dark:bg-gray-800 dark:text-white"
                                type="text" name="name" :value="old('name')" autocomplete="name" />
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <x-label for="email" value="{{ __('auth/register.form.email') }}" />
                            <x-input id="email" class="block mt-1 w-full dark:bg-gray-800 dark:text-white"
                                type="email" name="email" :value="old('email')" required autocomplete="email" />
                        </div>

                        <div class="col-span-6 sm:col-span-3 sm:col-start-1">
                            <x-label for="password" value="{{ __('auth/register.form.password') }}" />
                            <x-input id="password" class="block mt-1 w-full dark:bg-gray-800 dark:text-white"
                                type="password" name="password" required autocomplete="new-password" />
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <x-label for="password_confirmation"
                                value="{{ __('auth/register.form.confirm_password') }}" />
                            <x-input id="password_confirmation"
                                class="block mt-1 w-full dark:bg-gray-800 dark:text-white" type="password"
                                name="password_confirmation" required autocomplete="new-password" />
                        </div>

                        @if ($tosEnabled)
                            <div class="col-span-6">
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" name="terms" id="terms" value="1"
                                        {{ old('terms') ? 'checked' : '' }}
                                        class="mt-1 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('auth/register.form.terms_accept') }}
                                        <a href="{{ route('terms') }}" target="_blank"
                                            class="text-indigo-600 dark:text-indigo-400 underline hover:text-indigo-800 dark:hover:text-indigo-300">
                                            {{ __('auth/register.form.terms_link') }}
                                        </a>
                                    </span>
                                </label>
                                @error('terms')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <div class="col-span-6 flex flex-col sm:flex-row sm:items-center sm:gap-4">
                            <x-button>
                                {{ __('auth/register.form.register') }}
                            </x-button>

                            <p class="mt-4 text-sm text-gray-500 sm:mt-0 sm:whitespace-nowrap">
                                <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-100 underline">
                                    {{ __('auth/register.form.already_registered') }}
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </section>
</x-app-layout>
