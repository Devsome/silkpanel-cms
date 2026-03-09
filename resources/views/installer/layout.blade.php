<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SilkPanel CMS Installer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                            950: '#3b1078',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .step-indicator {
            @apply w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300 relative;
        }

        .step-indicator.active {
            @apply bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-lg scale-110;
        }

        .step-indicator.completed {
            @apply bg-gradient-to-br from-green-400 to-green-600 text-white shadow-md;
        }

        .step-indicator.pending {
            @apply bg-gray-100 text-gray-400 border-2 border-gray-200;
        }

        .step-indicator::after {
            @apply absolute inset-0 rounded-full bg-gradient-to-r from-primary-400 to-primary-600 opacity-0 scale-150 -z-10 transition-all duration-300;
            content: '';
        }

        .step-indicator.active::after {
            @apply opacity-20 scale-125;
        }

        .step-line {
            @apply flex-1 h-1 mx-3 transition-all duration-300 relative overflow-hidden;
            background: linear-gradient(to right, #e5e7eb, #e5e7eb);
        }

        .step-line.active {
            background: linear-gradient(to right, #7c3aed 0%, #7c3aed 50%, #e5e7eb 50%, #e5e7eb 100%);
        }

        .step-line.completed {
            background: linear-gradient(to right, #10b981, #10b981);
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-4xl mx-auto px-4 py-6">
                <div class="flex items-center">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center">
                            <x-application-logo class="w-6 h-6 text-white" />
                        </div>
                        <div class="ml-3">
                            <h1 class="text-xl font-semibold text-gray-900">
                                SilkPanel
                            </h1>
                            <p class="text-sm text-gray-500">Web Installer</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Progress Steps -->
        <div class="bg-gradient-to-b from-white to-gray-50 border-b border-gray-100 shadow-sm">
            <div class="max-w-4xl mx-auto px-4 py-8">
                <div class="flex items-center justify-center mb-6">
                    @php
                        $steps = [
                            'welcome' => [
                                'label' => 'Welcome',
                                'icon' => 'M14 10h-2V9m0 0l-2 2m2-2l2 2m-2 2h2v1m0 0l2-2m-2 2l-2-2',
                            ],
                            'environment' => [
                                'label' => 'Environment',
                                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                            ],
                            'silkpanel' => [
                                'label' => 'License',
                                'icon' => 'M9 12l2 2 4-4m8 0a9 9 0 11-18 0 9 9 0 0118 0z',
                            ],
                            'configuration' => [
                                'label' => 'Configuration',
                                'icon' =>
                                    'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
                            ],
                            'complete' => ['label' => 'Complete', 'icon' => 'M5 13l4 4L19 7'],
                        ];

                        $currentStep = $currentStep ?? 'welcome';
                        $stepKeys = array_keys($steps);
                        $currentIndex = array_search($currentStep, $stepKeys);
                    @endphp

                    @foreach ($steps as $step => $stepData)
                        @php
                            $stepIndex = array_search($step, $stepKeys);
                            $isCompleted = $stepIndex < $currentIndex;
                            $isActive = $step === $currentStep;
                            $isPending = $stepIndex > $currentIndex;
                        @endphp

                        <div class="flex items-center flex-1">
                            <div class="flex flex-col items-center flex-1">
                                <div
                                    class="step-indicator {{ $isCompleted ? 'completed' : ($isActive ? 'active' : 'pending') }}">
                                    @if ($isCompleted)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        {{ $stepIndex + 1 }}
                                    @endif
                                </div>
                                <span
                                    class="mt-2 text-xs font-semibold transition-colors duration-300 {{ $step === $currentStep ? 'text-primary-600' : ($isCompleted ? 'text-green-600' : 'text-gray-400') }}">
                                    {{ $stepData['label'] }}
                                </span>
                            </div>

                            @if (!$loop->last)
                                <div class="step-line {{ $isCompleted ? 'completed' : ($isActive ? 'active' : '') }}">
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="flex-1 py-8">
            <div class="max-w-4xl mx-auto px-4">
                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-400 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Error</h3>
                                <p class="mt-1 text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <svg class="w-5 h-5 text-green-400 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">Success</h3>
                                <p class="mt-1 text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200">
            <div class="max-w-4xl mx-auto px-4 py-6">
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <div>
                        <p>&copy; {{ date('Y') }} SilkPanel CMS - Built with ❤️ for the Silkroad Online community
                        </p>
                    </div>
                    <div>
                        <p>Devsome</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    @yield('scripts')
</body>

</html>
