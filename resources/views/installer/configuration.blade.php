@extends('installer.layout', ['currentStep' => 'configuration'])

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Configuration</h2>
                <p class="text-gray-600">Configure your SilkPanel CMS installation settings</p>
            </div>

            <form method="POST" action="{{ route('installer.install') }}" id="installation-form">
                @csrf

                <div class="space-y-8">
                    <!-- Application Settings -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-primary-600 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Application Settings
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="app_name" class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                                <input type="text" id="app_name" name="app_name"
                                    value="{{ old('app_name', 'SilkPanel CMS') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    required>
                                @error('app_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="app_url" class="block text-sm font-medium text-gray-700 mb-2">Site URL</label>
                                <input type="url" id="app_url" name="app_url"
                                    value="{{ old('app_url', request()->getSchemeAndHttpHost()) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                @error('app_url')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="app_environment"
                                    class="block text-sm font-medium text-gray-700 mb-2">Environment</label>
                                <select id="app_environment" name="app_environment"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="production"
                                        {{ old('app_environment') === 'production' ? 'selected' : '' }}>Production</option>
                                    <option value="local" {{ old('app_environment') === 'local' ? 'selected' : '' }}>
                                        Local/Development</option>
                                </select>
                            </div>

                            <div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="app_debug" name="app_debug" value="1"
                                        {{ old('app_debug') ? 'checked' : '' }}
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="app_debug" class="ml-2 block text-sm font-medium text-gray-700">
                                        Enable Debug Mode
                                    </label>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Only enable for development environments</p>
                            </div>
                        </div>
                    </div>

                    <!-- Database Settings -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4">
                                </path>
                            </svg>
                            Database Settings
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="db_host" class="block text-sm font-medium text-gray-700 mb-2">Database
                                    Host</label>
                                <input type="text" id="db_host" name="db_host"
                                    value="{{ old('db_host', 'localhost') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                @error('db_host')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="db_port" class="block text-sm font-medium text-gray-700 mb-2">Database
                                    Port</label>
                                <input type="number" id="db_port" name="db_port" value="{{ old('db_port', '3306') }}"
                                    min="1" max="65535"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                @error('db_port')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="db_database" class="block text-sm font-medium text-gray-700 mb-2">Database
                                    Name</label>
                                <input type="text" id="db_database" name="db_database" value="{{ old('db_database') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                @error('db_database')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="db_username" class="block text-sm font-medium text-gray-700 mb-2">Database
                                    Username</label>
                                <input type="text" id="db_username" name="db_username" value="{{ old('db_username') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                @error('db_username')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="db_password" class="block text-sm font-medium text-gray-700 mb-2">Database
                                    Password</label>
                                <input type="password" id="db_password" name="db_password"
                                    value="{{ old('db_password') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('db_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="button" id="test-db-connection"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors">
                                Test Database Connection
                            </button>
                            <div id="db-test-result" class="mt-2 hidden"></div>
                        </div>
                    </div>

                    <!-- MSSQL Database Settings -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-primary-600 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4">
                                </path>
                            </svg>
                            MSSQL Settings (Silkroad)
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="mssql_host" class="block text-sm font-medium text-gray-700 mb-2">MSSQL
                                    Host</label>
                                <input type="text" id="mssql_host" name="mssql_host"
                                    value="{{ old('mssql_host', env('DB_SQL_HOST')) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    required>
                                @error('mssql_host')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="mssql_port" class="block text-sm font-medium text-gray-700 mb-2">MSSQL
                                    Port</label>
                                <input type="number" id="mssql_port" name="mssql_port"
                                    value="{{ old('mssql_port', env('DB_SQL_PORT', '1433')) }}" min="1"
                                    max="65535"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    required>
                                @error('mssql_port')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="mssql_username" class="block text-sm font-medium text-gray-700 mb-2">MSSQL
                                    Username</label>
                                <input type="text" id="mssql_username" name="mssql_username"
                                    value="{{ old('mssql_username', env('DB_SQL_USERNAME')) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    required>
                                @error('mssql_username')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="mssql_password" class="block text-sm font-medium text-gray-700 mb-2">MSSQL
                                    Password</label>
                                <input type="password" id="mssql_password" name="mssql_password"
                                    value="{{ old('mssql_password', env('DB_SQL_PASSWORD')) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                @error('mssql_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="mssql_database_account"
                                    class="block text-sm font-medium text-gray-700 mb-2">DB_SQL_DATABASE_ACCOUNT</label>
                                <input type="text" id="mssql_database_account" name="mssql_database_account"
                                    value="{{ old('mssql_database_account', env('DB_SQL_DATABASE_ACCOUNT')) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    required>
                                @error('mssql_database_account')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="mssql_database_shard"
                                    class="block text-sm font-medium text-gray-700 mb-2">DB_SQL_DATABASE_SHARD</label>
                                <input type="text" id="mssql_database_shard" name="mssql_database_shard"
                                    value="{{ old('mssql_database_shard', env('DB_SQL_DATABASE_SHARD')) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    required>
                                @error('mssql_database_shard')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="mssql_database_log"
                                    class="block text-sm font-medium text-gray-700 mb-2">DB_SQL_DATABASE_LOG</label>
                                <input type="text" id="mssql_database_log" name="mssql_database_log"
                                    value="{{ old('mssql_database_log', env('DB_SQL_DATABASE_LOG')) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    required>
                                @error('mssql_database_log')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="mssql_database_custom"
                                    class="block text-sm font-medium text-gray-700 mb-2">DB_SQL_DATABASE_CUSTOM</label>
                                <input type="text" id="mssql_database_custom" name="mssql_database_custom"
                                    value="{{ old('mssql_database_custom', env('DB_SQL_DATABASE_CUSTOM')) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    required>
                                @error('mssql_database_custom')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @if (isset($serverVersion) && $serverVersion === 'isro')
                                <div>
                                    <label for="mssql_database_portal"
                                        class="block text-sm font-medium text-gray-700 mb-2">DB_SQL_DATABASE_PORTAL
                                        <span class="text-xs text-gray-500">(iSRO only)</span></label>
                                    <input type="text" id="mssql_database_portal" name="mssql_database_portal"
                                        value="{{ old('mssql_database_portal', env('DB_SQL_DATABASE_PORTAL')) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('mssql_database_portal')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        <div class="mt-4">
                            <button type="button" id="test-mssql-connection"
                                class="bg-primary-600 text-white px-4 py-2 rounded-md text-sm hover:bg-primary-700 transition-colors">
                                Test MSSQL Connection
                            </button>
                            <div id="mssql-test-result" class="mt-2 hidden"></div>
                        </div>
                    </div>


                    <!-- Mail Settings (Optional) -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                                Mail Settings
                                <span class="ml-2 text-sm text-gray-500 font-normal">(Optional)</span>
                            </h3>
                            <button type="button" id="toggle-mail-settings"
                                class="text-blue-600 hover:text-blue-800 text-sm">
                                Configure Mail
                            </button>
                        </div>

                        <div id="mail-settings" class="hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="mail_mailer"
                                        class="block text-sm font-medium text-gray-700 mb-2">Mailer</label>
                                    <select id="mail_mailer" name="mail_mailer"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Mailer</option>
                                        <option value="smtp" {{ old('mail_mailer') === 'smtp' ? 'selected' : '' }}>SMTP
                                        </option>
                                        <option value="mail" {{ old('mail_mailer') === 'mail' ? 'selected' : '' }}>PHP
                                            Mail</option>
                                        <option value="sendmail"
                                            {{ old('mail_mailer') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                    </select>
                                </div>

                                <div id="mail-from-name-field">
                                    <label for="mail_from_name" class="block text-sm font-medium text-gray-700 mb-2">From
                                        Name</label>
                                    <input type="text" id="mail_from_name" name="mail_from_name"
                                        value="{{ old('mail_from_name') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="md:col-span-2">
                                    <label for="mail_from_address"
                                        class="block text-sm font-medium text-gray-700 mb-2">From Email</label>
                                    <input type="email" id="mail_from_address" name="mail_from_address"
                                        value="{{ old('mail_from_address') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <div id="smtp-settings" class="mt-6 hidden">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="mail_host" class="block text-sm font-medium text-gray-700 mb-2">SMTP
                                            Host</label>
                                        <input type="text" id="mail_host" name="mail_host"
                                            value="{{ old('mail_host') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div>
                                        <label for="mail_port" class="block text-sm font-medium text-gray-700 mb-2">SMTP
                                            Port</label>
                                        <input type="number" id="mail_port" name="mail_port"
                                            value="{{ old('mail_port', '587') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div>
                                        <label for="mail_username"
                                            class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
                                        <input type="text" id="mail_username" name="mail_username"
                                            value="{{ old('mail_username') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div>
                                        <label for="mail_password"
                                            class="block text-sm font-medium text-gray-700 mb-2">SMTP Password</label>
                                        <input type="password" id="mail_password" name="mail_password"
                                            value="{{ old('mail_password') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="mail_encryption"
                                            class="block text-sm font-medium text-gray-700 mb-2">Encryption</label>
                                        <select id="mail_encryption" name="mail_encryption"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">None</option>
                                            <option value="tls"
                                                {{ old('mail_encryption') === 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl"
                                                {{ old('mail_encryption') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="mt-8 flex justify-between">
                    <a href="{{ route('installer.environment') }}"
                        class="bg-gray-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-gray-700 transition-colors inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        Back
                    </a>

                    <button type="submit" id="install-button"
                        class="bg-green-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-green-700 transition-colors inline-flex items-center">
                        <span id="install-text">Install SilkPanel</span>
                        <svg id="install-icon" class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <svg id="install-spinner" class="w-4 h-4 ml-2 animate-spin hidden" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // State tracking
            let dbConnectionTested = false;
            let dbConnectionValid = false;
            let mssqlConnectionTested = false;
            let mssqlConnectionValid = false;

            // Elements
            const form = document.getElementById('installation-form');
            const installButton = document.getElementById('install-button');
            const installText = document.getElementById('install-text');
            const installIcon = document.getElementById('install-icon');
            const installSpinner = document.getElementById('install-spinner');

            // Database elements
            const testDbButton = document.getElementById('test-db-connection');
            const dbTestResult = document.getElementById('db-test-result');
            const dbFields = ['db_host', 'db_port', 'db_database', 'db_username', 'db_password'];

            // MSSQL database elements
            const testMssqlButton = document.getElementById('test-mssql-connection');
            const mssqlTestResult = document.getElementById('mssql-test-result');
            const mssqlFields = [
                'mssql_host',
                'mssql_port',
                'mssql_username',
                'mssql_password',
                'mssql_database_account',
                'mssql_database_shard',
                'mssql_database_log',
                'mssql_database_custom',
                'mssql_database_portal'
            ];

            // Mail settings
            const toggleMailButton = document.getElementById('toggle-mail-settings');
            const mailSettings = document.getElementById('mail-settings');
            const mailMailer = document.getElementById('mail_mailer');
            const smtpSettings = document.getElementById('smtp-settings');

            // Initialize install button state
            updateInstallButtonState();

            // Database connection validation
            function markDatabaseAsUntested() {
                dbConnectionTested = false;
                dbConnectionValid = false;

                // Clear any existing results
                dbTestResult.classList.add('hidden');

                // Show warning that database needs to be tested
                if (!document.getElementById('db-test-warning')) {
                    const warningDiv = document.createElement('div');
                    warningDiv.id = 'db-test-warning';
                    warningDiv.className =
                        'mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded text-yellow-800 text-sm';
                    warningDiv.textContent =
                        '⚠ Database configuration changed. Please test connection before installing.';
                    testDbButton.parentNode.appendChild(warningDiv);
                }

                updateInstallButtonState();
            }

            function clearDatabaseWarning() {
                const warning = document.getElementById('db-test-warning');
                if (warning) warning.remove();
            }

            function markMssqlAsUntested() {
                mssqlConnectionTested = false;
                mssqlConnectionValid = false;

                mssqlTestResult.classList.add('hidden');

                if (!document.getElementById('mssql-test-warning')) {
                    const warningDiv = document.createElement('div');
                    warningDiv.id = 'mssql-test-warning';
                    warningDiv.className =
                        'mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded text-yellow-800 text-sm';
                    warningDiv.textContent =
                        '⚠ MSSQL configuration changed. Please test connection before installing.';
                    testMssqlButton.parentNode.appendChild(warningDiv);
                }

                updateInstallButtonState();
            }

            function clearMssqlWarning() {
                const warning = document.getElementById('mssql-test-warning');
                if (warning) warning.remove();
            }

            // Update install button state
            function updateInstallButtonState() {
                const canInstall = dbConnectionTested && dbConnectionValid &&
                    mssqlConnectionTested && mssqlConnectionValid;

                installButton.disabled = !canInstall;

                if (!canInstall) {
                    installButton.className =
                        'bg-gray-400 text-white px-6 py-3 rounded-lg font-medium cursor-not-allowed inline-flex items-center';

                    let reason = '';
                    if (!dbConnectionTested) {
                        reason = 'MySQL connection must be tested';
                    } else if (!dbConnectionValid) {
                        reason = 'MySQL connection must be successful';
                    } else if (!mssqlConnectionTested) {
                        reason = 'MSSQL connection must be tested';
                    } else if (!mssqlConnectionValid) {
                        reason = 'MSSQL connection must be successful';
                    }

                    installButton.title = reason;
                } else {
                    installButton.className =
                        'bg-green-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-green-700 transition-colors inline-flex items-center';
                    installButton.title = '';
                }
            }

            // Event listeners for database fields
            dbFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('input', markDatabaseAsUntested);
                }
            });

            mssqlFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('input', markMssqlAsUntested);
                }
            });

            // Toggle mail settings
            toggleMailButton.addEventListener('click', function() {
                mailSettings.classList.toggle('hidden');
                if (mailSettings.classList.contains('hidden')) {
                    toggleMailButton.textContent = 'Configure Mail';
                } else {
                    toggleMailButton.textContent = 'Hide Mail Settings';
                }
            });

            // Show/hide SMTP settings based on mailer selection
            mailMailer.addEventListener('change', function() {
                if (this.value === 'smtp') {
                    smtpSettings.classList.remove('hidden');
                } else {
                    smtpSettings.classList.add('hidden');
                }
            });

            // Test database connection
            testDbButton.addEventListener('click', function() {
                const button = this;
                const originalText = button.textContent;

                button.textContent = 'Testing...';
                button.disabled = true;
                clearDatabaseWarning();

                const formData = new FormData();
                formData.append('db_host', document.getElementById('db_host').value);
                formData.append('db_port', document.getElementById('db_port').value);
                formData.append('db_database', document.getElementById('db_database').value);
                formData.append('db_username', document.getElementById('db_username').value);
                formData.append('db_password', document.getElementById('db_password').value);

                // Add CSRF token
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                formData.append('_token', token);

                fetch('{{ route('installer.test-database') }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        dbTestResult.classList.remove('hidden');
                        dbConnectionTested = true;
                        dbConnectionValid = data.success;

                        if (data.success) {
                            dbTestResult.className =
                                'mt-2 p-3 bg-green-50 border border-green-200 rounded text-green-800 text-sm';
                            dbTestResult.textContent = '✓ ' + data.message;
                        } else {
                            dbTestResult.className =
                                'mt-2 p-3 bg-red-50 border border-red-200 rounded text-red-800 text-sm';
                            dbTestResult.textContent = '✗ ' + data.message;
                        }

                        updateInstallButtonState();
                    })
                    .catch(error => {
                        dbTestResult.classList.remove('hidden');
                        dbTestResult.className =
                            'mt-2 p-3 bg-red-50 border border-red-200 rounded text-red-800 text-sm';
                        dbTestResult.textContent = '✗ Connection test failed';
                        dbConnectionTested = true;
                        dbConnectionValid = false;
                        updateInstallButtonState();
                    })
                    .finally(() => {
                        button.textContent = originalText;
                        button.disabled = false;
                    });
            });

            testMssqlButton.addEventListener('click', function() {
                const button = this;
                const originalText = button.textContent;

                button.textContent = 'Testing...';
                button.disabled = true;
                clearMssqlWarning();

                const formData = new FormData();
                formData.append('mssql_host', document.getElementById('mssql_host').value);
                formData.append('mssql_port', document.getElementById('mssql_port').value);
                formData.append('mssql_username', document.getElementById('mssql_username').value);
                formData.append('mssql_password', document.getElementById('mssql_password').value);
                formData.append('mssql_database_account', document.getElementById('mssql_database_account')
                    .value);
                formData.append('mssql_database_shard', document.getElementById('mssql_database_shard')
                    .value);
                formData.append('mssql_database_log', document.getElementById('mssql_database_log').value);
                formData.append('mssql_database_custom', document.getElementById('mssql_database_custom')
                    .value);

                // Add portal database if field exists (iSRO only)
                const portalField = document.getElementById('mssql_database_portal');
                if (portalField) {
                    formData.append('mssql_database_portal', portalField.value);
                }

                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                formData.append('_token', token);

                fetch('{{ route('installer.test-mssql-database') }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        mssqlTestResult.classList.remove('hidden');
                        mssqlConnectionTested = true;
                        mssqlConnectionValid = data.success;

                        if (data.success) {
                            const warning = data.warning ? ' ⚠' : '✓';
                            const colorClass = data.warning ?
                                'mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded text-yellow-800 text-sm' :
                                'mt-2 p-3 bg-green-50 border border-green-200 rounded text-green-800 text-sm';
                            mssqlTestResult.className = colorClass;
                            mssqlTestResult.textContent = warning + ' ' + data.message;
                        } else {
                            mssqlTestResult.className =
                                'mt-2 p-3 bg-red-50 border border-red-200 rounded text-red-800 text-sm';
                            mssqlTestResult.textContent = '✗ ' + data.message;
                        }

                        updateInstallButtonState();
                    })
                    .catch(() => {
                        mssqlTestResult.classList.remove('hidden');
                        mssqlTestResult.className =
                            'mt-2 p-3 bg-red-50 border border-red-200 rounded text-red-800 text-sm';
                        mssqlTestResult.textContent = '✗ MSSQL connection test failed';
                        mssqlConnectionTested = true;
                        mssqlConnectionValid = false;
                        updateInstallButtonState();
                    })
                    .finally(() => {
                        button.textContent = originalText;
                        button.disabled = false;
                    });
            });

            // Handle form submission
            form.addEventListener('submit', function(e) {
                if (!dbConnectionTested || !dbConnectionValid) {
                    e.preventDefault();
                    alert('MySQL connection must be tested and successful before installation.');
                    return false;
                }

                if (!mssqlConnectionTested || !mssqlConnectionValid) {
                    e.preventDefault();
                    alert('MSSQL connection must be tested and successful before installation.');
                    return false;
                }

                // If validation passes, proceed with installation
                installButton.disabled = true;
                installText.textContent = 'Installing...';
                installIcon.classList.add('hidden');
                installSpinner.classList.remove('hidden');
            });

        });
    </script>
@endsection
