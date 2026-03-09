<?php

declare(strict_types=1);

namespace App\Http\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class InstallerRunner
{
    private array $output = [];

    private array $errors = [];

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Run the complete installation process
     */
    public function runInstallation(array $config): array
    {
        $this->clearOutput();

        try {
            // Step 1: Clear configuration cache
            $this->runStep('Clearing configuration cache', function () {
                Artisan::call('config:clear');

                return 'Configuration cache cleared';
            });

            // Step 2: Generate application key if needed
            if (empty(config('app.key'))) {
                $this->runStep('Generating application key', function () {
                    Artisan::call('key:generate', ['--force' => true]);

                    return 'Application key generated';
                });
            }

            // Step 3: Run migrations
            $this->runStep('Running database migrations', function () {
                Artisan::call('migrate', ['--force' => true]);

                return 'Database migrations completed';
            });

            // Step 4: Create storage symlink
            $this->runStep('Creating storage symlink', function () {
                Artisan::call('storage:link');

                return 'Storage symlink created';
            });

            // Step 5: Seed all data
            $this->runStep('Seeding all data', function () {
                return $this->runSeeder('Database\\Seeders\\DatabaseSeeder');
            });

            // Step 6: Clear all caches
            $this->runStep('Optimizing application', function () {
                Artisan::call('config:cache');
                Artisan::call('route:cache');
                Artisan::call('view:cache');

                return 'Application optimized';
            });

            return [
                'success' => true,
                'message' => 'Installation completed successfully',
                'output' => $this->output,
                'errors' => $this->errors,
            ];
        } catch (\Exception $e) {
            $this->errors[] = 'Installation failed: ' . $e->getMessage();

            return [
                'success' => false,
                'message' => 'Installation failed: ' . $e->getMessage(),
                'output' => $this->output,
                'errors' => $this->errors,
            ];
        }
    }

    /**
     * Run a single installation step
     */
    private function runStep(string $description, callable $callback): void
    {
        try {
            $this->output[] = "→ {$description}...";
            $result = $callback();
            $this->output[] = "✓ {$result}";
        } catch (\Exception $e) {
            $error = "✗ {$description} failed: " . $e->getMessage();
            $this->output[] = $error;
            $this->errors[] = $error;
            throw $e;
        }
    }

    /**
     * Run a database seeder by class name
     */
    private function runSeeder(string $seederClass): string
    {
        if (! class_exists($seederClass)) {
            return "Seeder {$seederClass} not found, skipping";
        }

        Artisan::call('db:seed', [
            '--class' => $seederClass,
            '--force' => true,
        ]);

        return Artisan::output() ?: "Seeder {$seederClass} completed";
    }

    /**
     * Test database connection
     */
    public function testDatabaseConnection(array $dbConfig): array
    {
        try {
            // Validate required keys exist
            $requiredKeys = ['host', 'port', 'username', 'password', 'database'];
            foreach ($requiredKeys as $key) {
                if (! array_key_exists($key, $dbConfig)) {
                    throw new \Exception("Missing required database configuration key: {$key}");
                }
            }

            // First connect to MySQL server (use 'mysql' system database to avoid dependency on user database)
            $serverConnection = [
                'driver' => 'mysql',
                'host' => $dbConfig['host'],
                'port' => $dbConfig['port'],
                'database' => 'mysql', // Use system database to test connection
                'username' => $dbConfig['username'],
                'password' => $dbConfig['password'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ];

            config(['database.connections.test_server' => $serverConnection]);
            DB::purge('test_server');
            DB::connection('test_server')->getPdo();

            // Check if the target database exists
            $dbName = $dbConfig['database'];
            $result = DB::connection('test_server')->select("SHOW DATABASES LIKE '" . $dbName . "'");
            $databaseExists = ! empty($result);

            if (! $databaseExists) {
                return [
                    'success' => true,
                    'message' => "Connected to MySQL server, but database '{$dbName}' does not exist. It will be created during installation if permissions allow.",
                ];
            }

            // Now connect with the database selected
            $fullConnection = array_merge($serverConnection, ['database' => $dbName]);
            config(['database.connections.test' => $fullConnection]);
            DB::purge('test');
            DB::connection('test')->getPdo();

            return [
                'success' => true,
                'message' => "Successfully connected to database '{$dbName}'",
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Test MSSQL connection and verify required Silkroad databases exist
     */
    public function testMssqlConnection(array $mssqlConfig): array
    {
        try {
            $requiredKeys = ['host', 'port', 'username', 'password', 'database_account', 'database_shard', 'database_log', 'database_custom'];
            foreach ($requiredKeys as $key) {
                if (! array_key_exists($key, $mssqlConfig)) {
                    throw new \Exception("Missing required MSSQL configuration key: {$key}");
                }
            }

            $serverConnection = [
                'driver' => 'sqlsrv',
                'host' => $mssqlConfig['host'],
                'port' => $mssqlConfig['port'],
                'database' => 'master',
                'username' => $mssqlConfig['username'],
                'password' => $mssqlConfig['password'],
                'charset' => 'utf8',
                'encrypt' => true,
                'trust_server_certificate' => true,
            ];

            config(['database.connections.test_mssql_server' => $serverConnection]);
            DB::purge('test_mssql_server');
            DB::connection('test_mssql_server')->getPdo();

            $databases = [
                $mssqlConfig['database_account'],
                $mssqlConfig['database_shard'],
                $mssqlConfig['database_log'],
                $mssqlConfig['database_custom'],
            ];

            if (isset($mssqlConfig['database_portal']) && $mssqlConfig['database_portal'] !== '') {
                $databases[] = $mssqlConfig['database_portal'];
            }

            $existingDbs = [];
            $missingDbs = [];

            foreach ($databases as $dbName) {
                try {
                    $result = DB::connection('test_mssql_server')
                        ->select('SELECT name FROM sys.databases WHERE name = ?', [$dbName]);

                    if (! empty($result)) {
                        $existingDbs[] = $dbName;
                    } else {
                        $missingDbs[] = $dbName;
                    }
                } catch (\Exception) {
                    $missingDbs[] = $dbName;
                }
            }

            if (count($missingDbs) > 0) {
                return [
                    'success' => true,
                    'warning' => true,
                    'message' => 'Connected to MSSQL server, but some databases are missing: ' . implode(', ', $missingDbs) . '. Found: ' . implode(', ', $existingDbs),
                ];
            }

            return [
                'success' => true,
                'message' => 'Successfully connected to MSSQL server and all required databases were found.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'MSSQL connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get captured output
     */
    public function getOutput(): array
    {
        return $this->output;
    }

    /**
     * Get captured errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Clear output and errors
     */
    private function clearOutput(): void
    {
        $this->output = [];
        $this->errors = [];
    }
}
