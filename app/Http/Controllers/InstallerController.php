<?php

namespace App\Http\Controllers;

use App\Http\Services\EnvironmentChecker;
use App\Http\Services\EnvWriter;
use App\Http\Services\InstallerRunner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Symfony\Component\Process\Process;

class InstallerController extends Controller
{
    private const SILKPANEL_WIDGETS_PACKAGE = 'silkpanel/widgets-dashboard';

    private const SILKPANEL_WIDGETS_VERSION = '^1.0';

    private EnvironmentChecker $environmentChecker;

    private EnvWriter $envWriter;

    private InstallerRunner $installerRunner;

    public function __construct()
    {
        $this->environmentChecker = new EnvironmentChecker;
        $this->envWriter = new EnvWriter;
        $this->installerRunner = new InstallerRunner;
    }

    /**
     * Show welcome page
     */
    public function welcome(): View
    {
        // Bootstrap should have handled .env creation already
        // Just check if we can write to it
        $envStatus = $this->envWriter->canWriteEnv();

        return view('installer.welcome', [
            'envStatus' => $envStatus,
        ]);
    }

    /**
     * Show environment check page
     */
    public function environment(): View
    {
        $checks = $this->environmentChecker->checkAll();

        return view('installer.environment', compact('checks'));
    }

    /**
     * Show SilkPanel configuration page
     */
    public function silkpanel(): RedirectResponse|View
    {
        // Ensure environment checks pass before showing configuration
        if (! $this->environmentChecker->isReady()) {
            return redirect()->route('installer.environment')
                ->with('error', 'Please resolve environment issues before proceeding');
        }

        return view('installer.silkpanel');
    }

    /**
     * Verify SilkPanel API License Key
     */
    public function verifyApiKey(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'api_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required',
            ], 422);
        }

        $apiKey = $request->input('api_key');

        try {
            $response = Http::post('https://devso.me/api/verify-key', [
                'key' => $apiKey,
            ]);

            if (! $response->ok()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to verify license key. Please check your API key and try again.',
                ]);
            }

            $data = $response->json();
            $isValid = $data['valid'] ?? false;

            if (! $isValid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired license key',
                ]);
            }

            // Store validated key in session for later use
            session([
                'silkpanel_api_key' => $apiKey,
                'silkpanel_client' => $data['client'] ?? null,
                'silkpanel_valid_until' => $data['valid_until'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'License key verified successfully!',
                'client' => $data['client'] ?? null,
                'valid_until' => $data['valid_until'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show configuration form
     */
    public function configuration(Request $request): RedirectResponse|View
    {
        // Check if coming from SilkPanel config with validated API key
        $apiKey = $request->query('api_key') ?? session('silkpanel_api_key');
        $serverVersion = $request->query('server_version') ?? session('silkpanel_server_version', 'vsro');

        if (! $apiKey) {
            return redirect()->route('installer.silkpanel')
                ->with('error', 'Please verify your SilkPanel license key first');
        }

        // Store in session
        session([
            'silkpanel_api_key' => $apiKey,
            'silkpanel_server_version' => $serverVersion,
        ]);

        return view('installer.configuration', [
            'apiKey' => $apiKey,
            'serverVersion' => $serverVersion,
        ]);
    }

    /**
     * Test MSSQL database connection
     */
    public function testMssqlDatabase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mssql_host' => 'required|string|max:255',
            'mssql_port' => 'required|integer|min:1|max:65535',
            'mssql_username' => 'required|string|max:255',
            'mssql_password' => 'nullable|string|max:255',
            'mssql_database_account' => 'required|string|max:64',
            'mssql_database_shard' => 'required|string|max:64',
            'mssql_database_log' => 'required|string|max:64',
            'mssql_database_custom' => 'required|string|max:64',
            'mssql_database_portal' => 'nullable|string|max:64',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $mssqlConfig = [
            'host' => $request->input('mssql_host'),
            'port' => $request->input('mssql_port'),
            'username' => $request->input('mssql_username'),
            'password' => $request->input('mssql_password'),
            'database_account' => $request->input('mssql_database_account'),
            'database_shard' => $request->input('mssql_database_shard'),
            'database_log' => $request->input('mssql_database_log'),
            'database_custom' => $request->input('mssql_database_custom'),
        ];

        // Add portal database if provided (iSRO only)
        if ($request->filled('mssql_database_portal')) {
            $mssqlConfig['database_portal'] = $request->input('mssql_database_portal');
        }

        $result = $this->installerRunner->testMssqlConnection($mssqlConfig);

        return response()->json($result);
    }

    /**
     * Test database connection
     */
    public function testDatabase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|integer|min:1|max:65535',
            'db_database' => 'required|string|max:64',
            'db_username' => 'required|string|max:255',
            'db_password' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid database configuration',
                'errors' => $validator->errors(),
            ], 422);
        }

        $dbConfig = [
            'host' => $request->input('db_host'),
            'port' => $request->input('db_port'),
            'database' => $request->input('db_database'),
            'username' => $request->input('db_username'),
            'password' => $request->input('db_password'),
        ];

        $result = $this->installerRunner->testDatabaseConnection($dbConfig);

        return response()->json($result);
    }

    /**
     * Process installation
     */
    public function install(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            // Application settings
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url|max:255',
            'app_environment' => 'required|in:local,production',
            'app_debug' => 'boolean',

            // Database settings
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|integer|min:1|max:65535',
            'db_database' => 'required|string|max:64',
            'db_username' => 'required|string|max:255',
            'db_password' => 'nullable|string|max:255',

            // MSSQL Database settings (required for Silkroad Online)
            'mssql_host' => 'required|string|max:255',
            'mssql_port' => 'required|integer|min:1|max:65535',
            'mssql_username' => 'required|string|max:255',
            'mssql_password' => 'nullable|string|max:255',
            'mssql_database_account' => 'required|string|max:64',
            'mssql_database_shard' => 'required|string|max:64',
            'mssql_database_log' => 'required|string|max:64',
            'mssql_database_custom' => 'required|string|max:64',
            'mssql_database_portal' => 'nullable|string|max:64',

            // Mail settings (optional)
            'mail_mailer' => 'nullable|in:smtp,ses,mail,sendmail',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('installer.configuration')
                ->withErrors($validator)
                ->withInput();
        }

        // Backup existing .env file
        $this->envWriter->backup();

        try {
            // Get SilkPanel config from session
            $apiKey = session('silkpanel_api_key');
            $serverVersion = session('silkpanel_server_version', 'vsro');

            if (! $apiKey) {
                throw new \Exception('SilkPanel API key not found. Please verify your license key.');
            }

            // Update .env file
            $this->envWriter
                ->setAppConfig([
                    'name' => $request->input('app_name'),
                    'url' => $request->input('app_url'),
                    'environment' => $request->input('app_environment'),
                    'debug' => $request->boolean('app_debug'),
                ])
                ->setDatabaseConfig([
                    'host' => $request->input('db_host'),
                    'port' => $request->input('db_port'),
                    'database' => $request->input('db_database'),
                    'username' => $request->input('db_username'),
                    'password' => $request->input('db_password'),
                ])
                ->set('SILKPANEL_API_KEY', $apiKey)
                ->set('SILKPANEL_SERVER_VERSION', $serverVersion)
                // MSSQL Config
                ->set('DB_SQL_HOST', $request->input('mssql_host'))
                ->set('DB_SQL_PORT', $request->input('mssql_port'))
                ->set('DB_SQL_USERNAME', $request->input('mssql_username'))
                ->set('DB_SQL_PASSWORD', $request->input('mssql_password') ?? '')
                ->set('DB_SQL_DATABASE_ACCOUNT', $request->input('mssql_database_account'))
                ->set('DB_SQL_DATABASE_SHARD', $request->input('mssql_database_shard'))
                ->set('DB_SQL_DATABASE_LOG', $request->input('mssql_database_log'))
                ->set('DB_SQL_DATABASE_CUSTOM', $request->input('mssql_database_custom'))

                ->set('SESSION_DRIVER', 'database') // Switch to database sessions after installation
                ->set('CACHE_STORE', 'database') // Switch to database cache after installation
                ->generateAppKey();

            // Set DB_SQL_DATABASE_PORTAL if provided (iSRO only)
            if ($request->filled('mssql_database_portal')) {
                $this->envWriter->set('DB_SQL_DATABASE_PORTAL', $request->input('mssql_database_portal'));
            }

            // Set mail configuration if provided
            if ($request->filled('mail_mailer')) {
                $this->envWriter->setMailConfig([
                    'mailer' => $request->input('mail_mailer'),
                    'host' => $request->input('mail_host'),
                    'port' => $request->input('mail_port'),
                    'username' => $request->input('mail_username'),
                    'password' => $request->input('mail_password'),
                    'encryption' => $request->input('mail_encryption'),
                    'from_address' => $request->input('mail_from_address'),
                    'from_name' => $request->input('mail_from_name'),
                ]);
            }

            if (! $this->envWriter->save()) {
                throw new \Exception('Failed to write .env file');
            }

            // Install SilkPanel packages with composer
            $this->installSilkPanelPackages($apiKey);

            // Clear configuration cache to pick up new .env values
            Artisan::call('config:clear');

            // Force reload of database configuration
            config(['database.connections.mysql' => [
                'driver' => 'mysql',
                'host' => $request->input('db_host'),
                'port' => $request->input('db_port'),
                'database' => $request->input('db_database'),
                'username' => $request->input('db_username'),
                'password' => $request->input('db_password'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]]);

            // Clear any existing database connections to force reconnection
            DB::purge('mysql');

            // Run installation steps
            $config = [
                'database' => [
                    'host' => $request->input('db_host'),
                    'port' => $request->input('db_port'),
                    'database' => $request->input('db_database'),
                    'username' => $request->input('db_username'),
                    'password' => $request->input('db_password'),
                ],
            ];

            $result = $this->installerRunner->runInstallation($config);

            if (! $result['success']) {
                throw new \Exception($result['message']);
            }

            // Lock the installer immediately after successful installation
            $this->lockInstaller();

            // Store installation output for display
            session([
                'installation_output' => $result['output'],
                'installation_success' => true,
            ]);

            // Persist installation result for complete page display
            File::put(
                storage_path('framework/installer-result.json'),
                json_encode([
                    'installation_success' => true,
                    'installation_output' => $result['output'],
                    'saved_at' => now()->toDateTimeString(),
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );

            // Create one-time flag file to allow first access to complete page
            File::put(
                storage_path('framework/installer-first-view.flag'),
                now()->toDateTimeString()
            );

            // Lock installer immediately after success
            $this->lockInstaller();

            // Clear caches after install so first request uses updated env/app URL and fresh config
            Artisan::call('optimize:clear');

            return redirect()->route('installer.complete');
        } catch (\Exception $e) {
            return redirect()->route('installer.configuration')
                ->with('error', 'Installation failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show installation complete page
     */
    public function complete(): View|RedirectResponse
    {
        $lockPath = base_path('installer.lock');
        $resultPath = storage_path('framework/installer-result.json');
        $flagPath = storage_path('framework/installer-first-view.flag');

        // Check if this is the first view after installation
        $isFirstView = File::exists($flagPath);

        if (! $isFirstView && File::exists($lockPath)) {
            // Installation is locked and not first view - redirect to home
            return redirect('/')->with('info', 'Installation already completed.');
        }

        // Load installation output from result file
        $output = [];
        if (File::exists($resultPath)) {
            $result = json_decode(File::get($resultPath), true);
            $output = $result['installation_output'] ?? [];
        }

        // Delete flag file after first view to prevent subsequent access
        if ($isFirstView) {
            File::delete($flagPath);
            Log::info('Installation complete page viewed for the first time. Flag file removed.');
        }

        return view('installer.complete', compact('output'));
    }

    /**
     * Install SilkPanel packages using Composer
     */
    private function installSilkPanelPackages(string $apiKey): void
    {
        Log::info('[Installer] Ensuring SilkPanel packages are installed...');

        $this->configureComposerRepository($apiKey);

        if ($this->isComposerPackageInstalled(self::SILKPANEL_WIDGETS_PACKAGE)) {
            Log::info('[Installer] SilkPanel package already installed', [
                'package' => self::SILKPANEL_WIDGETS_PACKAGE,
            ]);

            return;
        }

        $command = $this->isComposerPackageLocked(self::SILKPANEL_WIDGETS_PACKAGE)
            ? [...$this->resolveComposerCommand(), 'install', '--no-interaction', '--prefer-dist', '--optimize-autoloader']
            : [...$this->resolveComposerCommand(), 'require', self::SILKPANEL_WIDGETS_PACKAGE . ':' . self::SILKPANEL_WIDGETS_VERSION, '--no-interaction', '--prefer-dist', '--no-progress'];

        $this->runComposerCommand($command);

        if (! $this->isComposerPackageInstalled(self::SILKPANEL_WIDGETS_PACKAGE)) {
            throw new \RuntimeException('Composer finished, but silkpanel/widgets-dashboard is still missing from vendor.');
        }

        Log::info('[Installer] SilkPanel package installed successfully', [
            'package' => self::SILKPANEL_WIDGETS_PACKAGE,
        ]);
    }

    private function configureComposerRepository(string $apiKey): void
    {
        $composerPath = base_path('composer.json');

        if (! File::exists($composerPath)) {
            throw new \RuntimeException('composer.json was not found.');
        }

        $composerData = json_decode(File::get($composerPath), true);

        if (! is_array($composerData)) {
            throw new \RuntimeException('composer.json could not be parsed.');
        }

        if (! isset($composerData['repositories']) || ! is_array($composerData['repositories'])) {
            $composerData['repositories'] = [];
        }

        $repositoryIndex = null;

        foreach ($composerData['repositories'] as $index => $repository) {
            if (
                isset($repository['type'], $repository['url']) &&
                $repository['type'] === 'composer' &&
                $repository['url'] === 'https://composer.devso.me'
            ) {
                $repositoryIndex = $index;
                break;
            }
        }

        $privateRepository = [
            'type' => 'composer',
            'url' => 'https://composer.devso.me',
            'options' => [
                'http' => [
                    'header' => [
                        "API-TOKEN: {$apiKey}",
                    ],
                ],
            ],
        ];

        if ($repositoryIndex === null) {
            $composerData['repositories'][] = $privateRepository;
        } else {
            $composerData['repositories'][$repositoryIndex] = $privateRepository;
        }

        $writeResult = File::put(
            $composerPath,
            json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
        );

        if ($writeResult === false) {
            throw new \RuntimeException('composer.json could not be updated with the private repository configuration.');
        }
    }

    private function isComposerPackageLocked(string $packageName): bool
    {
        $lockPath = base_path('composer.lock');

        if (! File::exists($lockPath)) {
            return false;
        }

        $lockData = json_decode(File::get($lockPath), true);

        if (! is_array($lockData)) {
            return false;
        }

        foreach (['packages', 'packages-dev'] as $section) {
            foreach ($lockData[$section] ?? [] as $package) {
                if (($package['name'] ?? null) === $packageName) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isComposerPackageInstalled(string $packageName): bool
    {
        return File::isDirectory(base_path('vendor/' . $packageName));
    }

    private function resolveComposerCommand(): array
    {
        $composerPharPath = base_path('composer.phar');

        if (File::exists($composerPharPath)) {
            return [PHP_BINARY, $composerPharPath];
        }

        foreach (
            [
                '/opt/homebrew/bin/composer',
                '/usr/local/bin/composer',
                '/usr/bin/composer',
            ] as $composerBinary
        ) {
            if (is_executable($composerBinary)) {
                return [$composerBinary];
            }
        }

        return ['composer'];
    }

    private function runComposerCommand(array $command): void
    {
        $process = new Process($command, base_path(), [
            'COMPOSER_ALLOW_SUPERUSER' => '1',
        ]);

        $process->setTimeout(900);
        $process->run();

        $output = trim($process->getOutput() . "\n" . $process->getErrorOutput());

        Log::info('[Installer] Composer command finished', [
            'command' => implode(' ', $command),
            'exit_code' => $process->getExitCode(),
            'output' => $output,
        ]);

        if (! $process->isSuccessful()) {
            throw new \RuntimeException(
                'Composer command failed: ' . ($output !== '' ? $output : 'No output returned.')
            );
        }
    }

    /**
     * Lock the installer to prevent re-installation
     */
    private function lockInstaller(): void
    {
        $lockPath = base_path('installer.lock');

        try {
            $success = File::put($lockPath, now()->toDateTimeString());

            if ($success === false) {
                throw new \Exception('Failed to write installer lock file');
            }

            Log::info('Installer locked successfully. Lock file created at: ' . $lockPath);
        } catch (\Exception $e) {
            Log::warning('Cannot create installer lock file in project root: ' . $e->getMessage());

            try {
                $this->envWriter->disableInstaller()->save();
                Log::info('Installer disabled via .env file as fallback');
            } catch (\Exception $envError) {
                throw new \Exception(
                    "Cannot lock installer: Unable to create lock file ({$e->getMessage()}) " .
                        "and unable to update .env file ({$envError->getMessage()}). " .
                        'Please ensure web server has write permissions to either the project root ' .
                        'or the .env file to prevent reinstallation.'
                );
            }

            return;
        }

        try {
            $this->envWriter->disableInstaller()->save();
        } catch (\Exception $e) {
            Log::warning('Failed to update .env file during installer lock: ' . $e->getMessage());
        }
    }
}
