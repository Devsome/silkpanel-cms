<x-app-layout>
    <section class="bg-white dark:bg-gray-900">
        <div class="lg:grid lg:min-h-screen lg:grid-cols-6">
            <section class="relative flex h-32 items-end bg-gray-900 lg:col-span-3 lg:h-full">
                <img alt="Night" src="{{ Vite::asset('resources/images/banner/background-one.png') }}"
                    class="absolute inset-0 h-full w-full object-cover opacity-80" />
                <div class="hidden lg:relative lg:block lg:p-12">
                    <a class="block text-white" href="/">
                        <span class="sr-only">
                            {{ __('auth/forgot-password.home') }}
                        </span>
                    </a>

                    <h2 class="mt-6 text-2xl font-bold text-white sm:text-3xl md:text-4xl">
                        {{ __('auth/forgot-password.title') }}
                    </h2>

                    <p class="mt-4 leading-relaxed text-white/90">
                        {{ __('auth/forgot-password.description') }}
                    </p>
                </div>
            </section>
            <main aria-label="Main"
                class="flex items-center justify-center px-8 py-8 sm:px-12 lg:col-span-3 lg:py-12 lg:px-16 w-full">
                <div class="w-full">
                    <form method="POST" class="mt-2 grid grid-cols-6 gap-6" action="{{ route('password.email') }}">
                        @csrf

                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white col-span-6 sm:text-3xl md:text-4xl">
                            {{ __('auth/forgot-password.form.title') }}
                        </h1>

                        <div class="col-span-6">
                            <p class="mt-4 leading-relaxed text-black dark:text-gray-300">
                                {{ __('auth/forgot-password.form.description') }}
                            </p>
                        </div>

                        <x-auth-session-status class="mb-4 col-span-6" :status="session('status')" />

                        <div class="col-span-6 sm:col-span-3 sm:col-start-1">
                            <x-input-label for="email" :value="__('auth/forgot-password.form.email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                :value="old('email')" required autofocus />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="col-span-6 flex flex-col sm:flex-row sm:items-center sm:gap-4">
                            <x-button>
                                {{ __('auth/forgot-password.form.submit') }}
                            </x-button>

                            <p class="mt-4 text-sm text-gray-500 sm:mt-0 sm:whitespace-nowrap">
                                <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-100 underline">
                                    {{ __('auth/forgot-password.form.already_have_account') }}
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </section>
</x-app-layout>
