@extends('installer.layout', ['currentStep' => 'complete'])

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Installation Complete!</h2>
                <p class="text-gray-600">SilkPanel CMS has been successfully installed and configured</p>
            </div>

            <!-- Installation Output -->
            @if (!empty($output))
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Installation Log</h3>
                    <div class="bg-black text-green-400 p-4 rounded font-mono text-sm overflow-y-auto max-h-64">
                        @foreach ($output as $line)
                            <div>{{ $line }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Next Steps -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Admin Panel -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <h3 class="text-lg font-semibold text-blue-900">Access Admin Panel</h3>
                    </div>
                    <p class="text-blue-800 mb-4">Start managing your content through the admin interface</p>
                    <div class="space-y-2">
                        <a href="/admin"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors inline-flex items-center">
                            Open Admin Panel
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                        <p class="text-xs text-blue-700">First, create an admin user via /register or import accounts with
                            the command below</p>
                    </div>
                </div>

                <!-- Frontend -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <h3 class="text-lg font-semibold text-green-900">View Your Website</h3>
                    </div>
                    <p class="text-green-800 mb-4">See your SilkPanel CMS site in action</p>
                    <a href="/"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-green-700 transition-colors inline-flex items-center">
                        Visit Website
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Important Information -->
            <div class="space-y-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-400 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Security Notice</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p class="mb-2">For security, the installer has been automatically disabled. If you need
                                    to run it again:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Delete the <code class="bg-yellow-100 px-1 rounded">installer.lock</code>
                                        file</li>
                                    <li>Set <code class="bg-yellow-100 px-1 rounded">INSTALLER_ENABLED=true</code> in your
                                        <code class="bg-yellow-100 px-1 rounded">.env</code> file
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Need Help?</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <a href="https://documentation.devso.me/" target="_blank"
                            class="text-center hover:bg-gray-100 rounded-lg p-3 transition-colors">
                            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-1">Documentation</h4>
                            <p class="text-gray-600">Read the full documentation</p>
                        </a>
                        <a href="https://discord.gg/eFwSjTv9PT" target="_blank"
                            class="text-center hover:bg-gray-100 rounded-lg p-3 transition-colors">
                            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-1">Discord</h4>
                            <p class="text-gray-600">Visit us at our discord</p>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Import Silkroad Accounts -->
            <div class="mt-8 bg-primary-900 text-white rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2m0 0v-8m0 8H7m5-8V5m0 0H7m5 0h5"></path>
                    </svg>
                    Import Silkroad Accounts
                </h3>
                <p class="text-primary-100 mb-4">You can now import your existing Silkroad accounts from the MSSQL
                    database:</p>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-primary-200 mb-2">To preview accounts without importing:</p>
                        <div class="bg-black rounded p-3 font-mono text-sm text-green-400">
                            <div>php artisan silkpanel:import-accounts --dry-run</div>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-primary-200 mb-2">To import all accounts into the CMS:</p>
                        <div class="bg-black rounded p-3 font-mono text-sm text-green-400">
                            <div>php artisan silkpanel:import-accounts</div>
                        </div>
                    </div>
                </div>
                <p class="text-primary-200 text-xs mt-3">Tip: Use the --dry-run option first to preview which accounts
                    will be imported</p>
            </div>

            <div class="mt-8 text-center">
                <p class="text-gray-500 text-sm">
                    Congratulations! Your SilkPanel CMS installation is complete and ready to use.
                </p>
            </div>
        </div>
    </div>
@endsection
