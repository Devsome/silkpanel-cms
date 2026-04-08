<x-app-layout>
    <section class="bg-white dark:bg-gray-900">
        <div class="lg:grid lg:min-h-screen lg:grid-cols-6">
            <section class="relative flex h-32 items-end bg-gray-900 lg:col-span-3 lg:h-full">
                <img alt="Night" src="{{ Vite::asset('resources/images/banner/background-one.png') }}"
                    class="absolute inset-0 h-full w-full object-cover opacity-80" />
                <div class="hidden lg:relative lg:block lg:p-12">
                    <a class="block text-white" href="/">
                        <span class="sr-only">
                            {{ __('auth/verify-email.home') }}
                        </span>
                    </a>

                    <h2 class="mt-6 text-2xl font-bold text-white sm:text-3xl md:text-4xl">
                        {{ __('auth/verify-email.title') }}
                    </h2>

                    <p class="mt-4 leading-relaxed text-white/90">
                        {{ __('auth/verify-email.description') }}
                    </p>
                </div>
            </section>
            <main aria-label="Main"
                class="flex items-center justify-center px-8 py-8 sm:px-12 lg:col-span-3 lg:py-12 lg:px-16">
                <div class="w-full">
                    <form method="POST" class="mt-2 grid grid-cols-6 gap-6" action="{{ route('verification.send') }}">
                        @csrf

                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white col-span-6 sm:text-3xl md:text-4xl">
                            {{ __('auth/verify-email.form.title') }}
                        </h1>

                        <div class="col-span-6">
                            <p class="mt-4 leading-relaxed text-black dark:text-gray-300">
                                {{ __('auth/verify-email.form.description') }}
                            </p>
                        </div>

                        <div class="col-span-6 flex flex-col sm:flex-row sm:items-center sm:gap-4">
                            <x-button>
                                {{ __('auth/verify-email.form.resend') }}
                            </x-button>
                        </div>

                    </form>

                    <form method="POST" class="mt-4 float-right" action="{{ route('logout') }}">
                        @csrf

                        <x-secondary-button type="submit"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('auth/verify-email.form.logout') }}
                        </x-secondary-button>
                    </form>

                </div>
            </main>
        </div>
    </section>
</x-app-layout>
