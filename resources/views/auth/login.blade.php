<x-main-layout>
    <section class="bg-white dark:bg-gray-900">
        <div class="lg:grid lg:min-h-screen lg:grid-cols-6">
            <section class="relative flex h-32 items-end bg-gray-900 lg:col-span-3 lg:h-full">
                <img alt="Night" src="{{ Vite::asset('resources/images/banner/background-one.png') }}"
                    class="absolute inset-0 h-full w-full object-cover opacity-80" />
                <div class="hidden lg:relative lg:block lg:p-12">
                    <a class="block text-white" href="/">
                        <span class="sr-only">
                            {{ __('auth/login.home') }}
                        </span>
                    </a>

                    <h2 class="mt-6 text-2xl font-bold text-white sm:text-3xl md:text-4xl">
                        {{ __('auth/login.title', ['app_name' => config('app.name')]) }}
                    </h2>

                    <p class="mt-4 leading-relaxed text-white/90">
                        {{ __('auth/login.description') }}
                    </p>
                </div>
            </section>
            <main aria-label="Main"
                class="flex items-center justify-center px-8 py-8 sm:px-12 lg:col-span-3 lg:py-12 lg:px-16 w-full">
                <div class="w-full">
                    <form method="POST" class="mt-2 grid grid-cols-6 gap-6" action="{{ route('login') }}">
                        @csrf

                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white col-span-6 sm:text-3xl md:text-4xl">
                            {{ __('auth/login.form.title') }}
                        </h1>

                        <x-auth-session-status class="mb-4 col-span-6" :status="session('status')" />

                        <div class="col-span-6 sm:col-span-3">
                            <x-input-label for="email" :value="__('auth/login.form.email')" />
                            <x-text-input id="email" class="block mt-1 w-full dark:bg-gray-800 dark:text-white"
                                type="email" name="email" :value="old('email')" required autofocus
                                autocomplete="email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <x-input-label for="password" :value="__('auth/login.form.password')" />
                            <x-text-input id="password" class="block mt-1 w-full dark:bg-gray-800 dark:text-white"
                                type="password" name="password" required autocomplete="current-password" />

                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="remember_me" class="inline-flex items-center">
                                <x-checkbox id="remember_me" name="remember" />
                                <span class="ms-2 text-sm text-gray-600">{{ __('auth/login.form.remember_me') }}</span>
                            </label>
                        </div>

                        <div class="col-span-6 flex flex-col sm:flex-row sm:items-center sm:gap-4">
                            <x-button>
                                {{ __('auth/login.form.login') }}
                            </x-button>

                            @if (Route::has('password.request'))
                                <p class="mt-4 text-sm text-gray-500 sm:mt-0 sm:whitespace-nowrap">
                                    <a href="{{ route('password.request') }}"
                                        class="text-gray-700 dark:text-gray-100 underline">
                                        {{ __('auth/login.form.forgot_password') }}
                                    </a>
                                </p>
                            @endif
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </section>
</x-main-layout>
