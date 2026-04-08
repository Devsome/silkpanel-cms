<x-app-layout>
    <section class="bg-white dark:bg-gray-900">
        <div class="lg:grid lg:min-h-screen lg:grid-cols-6">
            <section class="relative flex h-32 items-end bg-gray-900 lg:col-span-3 lg:h-full">
                <img alt="Night" src="{{ Vite::asset('resources/images/banner/background-one.png') }}"
                    class="absolute inset-0 h-full w-full object-cover opacity-80" />
                <div class="hidden lg:relative lg:block lg:p-12">
                    <a class="block text-white" href="/">
                        <span class="sr-only">
                            {{ __('auth/confirm-password.home') }}
                        </span>
                    </a>

                    <h2 class="mt-6 text-2xl font-bold text-white sm:text-3xl md:text-4xl">
                        {{ __('auth/confirm-password.title') }}
                    </h2>

                    <p class="mt-4 leading-relaxed text-white/90">
                        {{ __('auth/confirm-password.description') }}
                    </p>
                </div>
            </section>
            <main aria-label="Main"
                class="flex items-center justify-center px-8 py-8 sm:px-12 lg:col-span-3 lg:py-12 lg:px-16 w-full">
                <div class="w-full">
                    <form method="POST" class="mt-2 grid grid-cols-6 gap-6" action="{{ route('password.confirm') }}">
                        @csrf

                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white col-span-6 sm:text-3xl md:text-4xl">
                            {{ __('auth/confirm-password.form.title') }}
                        </h1>

                        <div class="col-span-6">
                            <p class="mt-4 leading-relaxed text-black dark:text-gray-300">
                                {{ __('auth/confirm-password.form.description') }}
                            </p>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <x-input-label for="password" :value="__('auth/confirm-password.form.password')" />

                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                                required autocomplete="current-password" />

                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="col-span-6 flex flex-col sm:flex-row sm:items-center sm:gap-4">
                            <x-button>
                                {{ __('auth/confirm-password.form.submit') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </section>
</x-app-layout>
