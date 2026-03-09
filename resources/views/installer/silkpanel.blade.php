@extends('installer.layout', ['currentStep' => 'silkpanel'])

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">SilkPanel Configuration</h2>
                <p class="text-gray-600">Configure your SilkPanel license and Silkroad Online server version</p>
            </div>

            <form id="silkpanel-form" class="space-y-6">
                @csrf

                <!-- API Key Section -->
                <div class="border border-gray-200 rounded-lg p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">License Key</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Enter your SilkPanel API license key. This will be verified with our server.
                        </p>
                    </div>

                    <div>
                        <label for="api_key" class="block text-sm font-medium text-gray-700 mb-2">
                            API License Key
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="api_key" name="api_key"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            placeholder="xxxxxx-xxxxxx-xxxxxx-xxxxxx-xxxxxx-xx" required>
                        <p class="mt-2 text-sm text-gray-500">
                            Don't have a license key? <a href="https://devso.me" target="_blank"
                                class="text-primary-600 hover:text-primary-700 font-medium">Get one here</a>
                        </p>
                    </div>

                    <div class="mt-4">
                        <button type="button" id="verify-key-button"
                            class="bg-primary-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-primary-700 transition-colors inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            Verify License Key
                        </button>

                        <!-- Verification Result -->
                        <div id="verify-result" class="mt-4 hidden"></div>
                    </div>
                </div>

                <!-- Server Version Section -->
                <div class="border border-gray-200 rounded-lg p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Server Version</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Select your Silkroad Online server version (vSRO or iSRO)
                        </p>
                    </div>

                    <div class="space-y-3">
                        <label
                            class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-primary-500 transition-colors">
                            <input type="radio" name="server_version" value="vsro" class="w-4 h-4 text-primary-600"
                                checked>
                            <div class="ml-3">
                                <div class="font-medium text-gray-900">vSRO (International)</div>
                                <div class="text-sm text-gray-500">Vietnamese/Global Silkroad Online</div>
                            </div>
                        </label>

                        <label
                            class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-primary-500 transition-colors">
                            <input type="radio" name="server_version" value="isro" class="w-4 h-4 text-primary-600">
                            <div class="ml-3">
                                <div class="font-medium text-gray-900">iSRO (International)</div>
                                <div class="text-sm text-gray-500">International Silkroad Online</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Warning Box -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-400 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Important</h3>
                            <p class="mt-1 text-sm text-yellow-700">
                                Your license key must be verified before continuing. The installer will download and install
                                required SilkPanel packages after verification.
                            </p>
                        </div>
                    </div>
                </div>
            </form>

            <div class="mt-8 flex justify-between">
                <a href="{{ route('installer.environment') }}"
                    class="bg-gray-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-gray-700 transition-colors inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back
                </a>

                <button type="button" id="continue-button" disabled
                    class="bg-gray-400 text-white px-6 py-3 rounded-lg font-medium cursor-not-allowed inline-flex items-center">
                    Continue
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const apiKeyInput = document.getElementById('api_key');
            const verifyButton = document.getElementById('verify-key-button');
            const verifyResult = document.getElementById('verify-result');
            const continueButton = document.getElementById('continue-button');
            let isVerified = false;

            verifyButton.addEventListener('click', function() {
                const apiKey = apiKeyInput.value.trim();

                if (!apiKey) {
                    showError('Please enter your API license key');
                    return;
                }

                // Disable button and show loading
                verifyButton.disabled = true;
                verifyButton.innerHTML = `
                    <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Verifying...
                `;

                // Make API request
                fetch('{{ route('installer.verify-api-key') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            api_key: apiKey
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            isVerified = true;
                            showSuccess(data.message, data.client, data.valid_until);
                            enableContinue();
                        } else {
                            isVerified = false;
                            showError(data.message || 'Invalid license key');
                            disableContinue();
                        }
                    })
                    .catch(error => {
                        isVerified = false;
                        showError(
                            'Verification failed. Please check your internet connection and try again.'
                        );
                        disableContinue();
                    })
                    .finally(() => {
                        // Reset button
                        verifyButton.disabled = false;
                        verifyButton.innerHTML = `
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Verify License Key
                        `;
                    });
            });

            function showSuccess(message, client, validUntil) {
                const formattedValidUntil = formatValidUntil(validUntil);

                verifyResult.className = 'mt-4 bg-green-50 border border-green-200 rounded-lg p-4';
                verifyResult.innerHTML = `
                    <div class="flex">
                        <svg class="w-5 h-5 text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-700">✓ License Verified</h3>
                            <p class="mt-1 text-sm text-green-700">${message}</p>
                            ${client ? `<p class="mt-1 text-sm text-green-700">Licensed to: <strong>${client}</strong></p>` : ''}
                            ${formattedValidUntil ? `<p class="text-sm text-green-700">Valid until: <strong>${formattedValidUntil}</strong></p>` : ''}
                        </div>
                    </div>
                `;
                verifyResult.classList.remove('hidden');
            }

            function formatValidUntil(validUntil) {
                if (!validUntil) {
                    return null;
                }

                let date;

                if (typeof validUntil === 'number' || /^\d+$/.test(String(validUntil))) {
                    const numericValue = Number(validUntil);
                    const timestamp = numericValue > 9999999999 ? numericValue : numericValue * 1000;
                    date = new Date(timestamp);
                } else {
                    date = new Date(validUntil);
                }

                if (Number.isNaN(date.getTime())) {
                    return String(validUntil);
                }

                return date.toLocaleString('de-DE', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                });
            }

            function showError(message) {
                verifyResult.className = 'mt-4 bg-red-50 border border-red-200 rounded-lg p-4';
                verifyResult.innerHTML = `
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Verification Failed</h3>
                            <p class="mt-1 text-sm text-red-700">${message}</p>
                        </div>
                    </div>
                `;
                verifyResult.classList.remove('hidden');
            }

            function enableContinue() {
                continueButton.disabled = false;
                continueButton.className =
                    'bg-primary-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-primary-700 transition-colors inline-flex items-center cursor-pointer';
            }

            function disableContinue() {
                continueButton.disabled = true;
                continueButton.className =
                    'bg-gray-400 text-white px-6 py-3 rounded-lg font-medium cursor-not-allowed inline-flex items-center';
            }

            continueButton.addEventListener('click', function() {
                if (!isVerified) {
                    showError('Please verify your license key first');
                    return;
                }

                const apiKey = apiKeyInput.value.trim();
                const serverVersion = document.querySelector('input[name="server_version"]:checked').value;

                // Redirect to configuration with query params
                window.location.href =
                    `{{ route('installer.configuration') }}?api_key=${encodeURIComponent(apiKey)}&server_version=${serverVersion}`;
            });

            // Reset verification when API key changes
            apiKeyInput.addEventListener('input', function() {
                if (isVerified) {
                    isVerified = false;
                    verifyResult.classList.add('hidden');
                    disableContinue();
                }
            });
        });
    </script>
@endsection
