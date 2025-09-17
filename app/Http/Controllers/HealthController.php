<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

/**
 * CODE STRUCTURE SUMMARY:
 * HealthController ( Handles health check endpoints for the application. )
 * Basic health check endpoint.
 * Comprehensive health check with detailed service status.
 * Check database connectivity and performance
 * Check cache service health
 * Check storage systems
 * Check specific storage disk
 * Check disk space
 * Check queue system
 * Check application configuration and environment
 * Check backup system health
 * Get application uptime
 * Format uptime in human readable format
 */
class HealthController extends Controller
{
    /**
     * Basic health check endpoint
     * Returns simple OK status for load balancers
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'OK',
            'timestamp' => now()->toISOString(),
            'service' => config('app.name'),
            'version' => config('app.version', '1.0.0'),
        ], 200);
    }

    /* Comprehensive health check with detailed service status */
    public function detailed(): JsonResponse
    {
        $startTime = microtime(true);
        $checks = [];
        $overallStatus = 'healthy';
        $issues = [];

        // 1. Database Connection Check
        $checks['database'] = $this->checkDatabase();
        if (! $checks['database']['healthy']) {
            $overallStatus = 'unhealthy';
            $issues[] = 'Database connection failed';
        }

        // 2. Cache Service Check
        $checks['cache'] = $this->checkCache();
        if (! $checks['cache']['healthy']) {
            $overallStatus = 'degraded';
            $issues[] = 'Cache service issues';
        }

        // 3. Storage Check
        $checks['storage'] = $this->checkStorage();
        if (! $checks['storage']['healthy']) {
            $overallStatus = 'degraded';
            $issues[] = 'Storage issues detected';
        }

        // 4. Queue Service Check
        $checks['queue'] = $this->checkQueue();
        if (! $checks['queue']['healthy']) {
            $overallStatus = 'degraded';
            $issues[] = 'Queue service issues';
        }

        // 5. Application Health
        $checks['application'] = $this->checkApplication();
        if (! $checks['application']['healthy']) {
            $overallStatus = 'unhealthy';
            $issues[] = 'Application configuration issues';
        }

        // 6. Backup System Health
        $checks['backup'] = $this->checkBackupSystem();
        if (! $checks['backup']['healthy']) {
            $overallStatus = 'degraded';
            $issues[] = 'Backup system issues';
        }

        $endTime = microtime(true);
        $responseTime = round(($endTime - $startTime) * 1000, 2); // in milliseconds

        $response = [
            'status' => $overallStatus,
            'timestamp' => now()->toISOString(),
            'service' => config('app.name'),
            'version' => config('app.version', '1.0.0'),
            'environment' => config('app.env'),
            'checks' => $checks,
            'response_time_ms' => $responseTime,
            'uptime' => $this->getUptime(),
        ];

        if (! empty($issues)) {
            $response['issues'] = $issues;
        }

        // Log health check if there are issues
        if ($overallStatus !== 'healthy') {
            Log::warning('Health check detected issues', [
                'status' => $overallStatus,
                'issues' => $issues,
                'response_time' => $responseTime,
            ]);
        }

        $httpStatus = match ($overallStatus) {
            'healthy' => 200,
            'degraded' => 200, // Still operational but with warnings
            'unhealthy' => 503, // Service unavailable
        };

        return response()->json($response, $httpStatus);
    }

    /* Check database connectivity and performance */
    /**
     * @return array<string, mixed>
     */
    private function checkDatabase(): array
    {
        $startTime = microtime(true);

        try {
            // Test basic connection
            $pdo = DB::connection()->getPdo();

            // Test query performance
            $queryStart = microtime(true);
            $result = DB::select('SELECT 1 as test');
            $queryTime = round((microtime(true) - $queryStart) * 1000, 2);

            // Check if we can write (test with a simple operation)
            $writeTest = DB::table('users')->count();

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'healthy' => true,
                'status' => 'connected',
                'driver' => config('database.default'),
                'query_time_ms' => $queryTime,
                'response_time_ms' => $responseTime,
                'tables_accessible' => true,
                'user_count' => $writeTest,
            ];

        } catch (\Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'healthy' => false,
                'status' => 'connection_failed',
                'error' => $e->getMessage(),
                'response_time_ms' => $responseTime,
            ];
        }
    }

    /* Check cache service health */
    /**
     * @return array<string, mixed>
     */
    private function checkCache(): array
    {
        $startTime = microtime(true);

        try {
            $testKey = 'health_check_'.time();
            $testValue = 'test_'.uniqid();

            // Test write
            $writeStart = microtime(true);
            Cache::put($testKey, $testValue, 60);
            $writeTime = round((microtime(true) - $writeStart) * 1000, 2);

            // Test read
            $readStart = microtime(true);
            $readValue = Cache::get($testKey);
            $readTime = round((microtime(true) - $readStart) * 1000, 2);

            // Test delete
            Cache::forget($testKey);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            $isHealthy = ($readValue === $testValue);

            return [
                'healthy' => $isHealthy,
                'status' => $isHealthy ? 'operational' : 'read_write_failed',
                'driver' => config('cache.default'),
                'write_time_ms' => $writeTime,
                'read_time_ms' => $readTime,
                'response_time_ms' => $responseTime,
            ];

        } catch (\Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'healthy' => false,
                'status' => 'cache_failed',
                'error' => $e->getMessage(),
                'response_time_ms' => $responseTime,
            ];
        }
    }

    /* Check storage systems */
    /**
     * @return array<string, mixed>
     */
    private function checkStorage(): array
    {
        $startTime = microtime(true);

        try {
            $results = [];
            $overallHealthy = true;

            // Check local storage
            $localStorage = $this->checkStorageDisk('local');
            $results['local'] = $localStorage;
            if (! $localStorage['healthy']) {
                $overallHealthy = false;
            }

            // Check public storage
            $publicStorage = $this->checkStorageDisk('public');
            $results['public'] = $publicStorage;
            if (! $publicStorage['healthy']) {
                $overallHealthy = false;
            }

            // Check disk space
            $diskSpace = $this->checkDiskSpace();
            $results['disk_space'] = $diskSpace;
            if (! $diskSpace['healthy']) {
                $overallHealthy = false;
            }

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'healthy' => $overallHealthy,
                'status' => $overallHealthy ? 'operational' : 'storage_issues',
                'details' => $results,
                'response_time_ms' => $responseTime,
            ];

        } catch (\Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'healthy' => false,
                'status' => 'storage_check_failed',
                'error' => $e->getMessage(),
                'response_time_ms' => $responseTime,
            ];
        }
    }

    /* Check specific storage disk */
    /**
     * @return array<string, mixed>
     */
    private function checkStorageDisk(string $disk): array
    {
        try {
            $storage = Storage::disk($disk);
            $testFile = 'health_check_'.time().'.txt';
            $testContent = 'health_check_'.uniqid();

            // Test write
            $storage->put($testFile, $testContent);

            // Test read
            $readContent = $storage->get($testFile);

            // Test delete
            $storage->delete($testFile);

            $isHealthy = ($readContent === $testContent);

            return [
                'healthy' => $isHealthy,
                'status' => $isHealthy ? 'operational' : 'read_write_failed',
                'disk' => $disk,
            ];

        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'status' => 'disk_failed',
                'disk' => $disk,
                'error' => $e->getMessage(),
            ];
        }
    }

    /* Check disk space */
    /**
     * @return array<string, mixed>
     */
    private function checkDiskSpace(): array
    {
        try {
            $path = storage_path();
            $freeBytes = disk_free_space($path);
            $totalBytes = disk_total_space($path);

            if ($freeBytes === false || $totalBytes === false) {
                return [
                    'healthy' => false,
                    'status' => 'disk_space_check_failed',
                ];
            }

            $usedBytes = $totalBytes - $freeBytes;
            $usagePercent = round(($usedBytes / $totalBytes) * 100, 2);

            // Consider unhealthy if > 90% full, degraded if > 80%
            $isHealthy = $usagePercent < 90;
            $status = $usagePercent > 80 ? ($usagePercent > 90 ? 'critical' : 'warning') : 'ok';

            return [
                'healthy' => $isHealthy,
                'status' => $status,
                'usage_percent' => $usagePercent,
                'free_gb' => round($freeBytes / 1024 / 1024 / 1024, 2),
                'total_gb' => round($totalBytes / 1024 / 1024 / 1024, 2),
            ];

        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'status' => 'disk_space_check_failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /* Check queue system */
    /**
     * @return array<string, mixed>
     */
    private function checkQueue(): array
    {
        $startTime = microtime(true);

        try {
            // For now, we'll do basic queue connection check
            // In production, you might want to check queue size, failed jobs, etc.
            $queueDriver = config('queue.default');

            // Check if queue is configured
            if ($queueDriver === 'sync') {
                // Sync queue - always "healthy" but note it's synchronous
                return [
                    'healthy' => true,
                    'status' => 'sync_mode',
                    'driver' => $queueDriver,
                    'note' => 'Running in synchronous mode',
                    'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                ];
            }

            // For other queue drivers, check if connection is possible
            // This is a basic check - you might want to enhance this based on your queue driver
            $connection = Queue::connection();

            return [
                'healthy' => true,
                'status' => 'operational',
                'driver' => $queueDriver,
                'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
            ];

        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'status' => 'queue_failed',
                'error' => $e->getMessage(),
                'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
            ];
        }
    }

    /* Check application configuration and environment */
    /**
     * @return array<string, mixed>
     */
    private function checkApplication(): array
    {
        $startTime = microtime(true);
        $issues = [];
        $isHealthy = true;

        try {
            // Check critical configuration
            if (! config('app.key')) {
                $issues[] = 'APP_KEY not set';
                $isHealthy = false;
            }

            if (config('app.debug') && config('app.env') === 'production') {
                $issues[] = 'Debug mode enabled in production';
                $isHealthy = false;
            }

            // Check if we're in maintenance mode
            if (app()->isDownForMaintenance()) {
                $issues[] = 'Application is in maintenance mode';
                $isHealthy = false;
            }

            // Check PHP version
            $phpVersion = PHP_VERSION;
            if (version_compare($phpVersion, '8.1.0', '<')) {
                $issues[] = 'PHP version below recommended (8.1+)';
            }

            // Check critical directories
            $criticalPaths = [
                storage_path('logs'),
                storage_path('framework/cache'),
                storage_path('framework/sessions'),
                storage_path('framework/views'),
            ];

            foreach ($criticalPaths as $path) {
                if (! is_writable($path)) {
                    $issues[] = "Directory not writable: {$path}";
                    $isHealthy = false;
                }
            }

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'healthy' => $isHealthy,
                'status' => $isHealthy ? 'operational' : 'configuration_issues',
                'php_version' => $phpVersion,
                'laravel_version' => app()->version(),
                'environment' => config('app.env'),
                'debug_mode' => config('app.debug'),
                'timezone' => config('app.timezone'),
                'maintenance_mode' => app()->isDownForMaintenance(),
                'issues' => $issues,
                'response_time_ms' => $responseTime,
            ];

        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'status' => 'application_check_failed',
                'error' => $e->getMessage(),
                'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
            ];
        }
    }

    /* Check backup system health */
    /**
     * @return array<string, mixed>
     */
    private function checkBackupSystem(): array
    {
        $startTime = microtime(true);

        try {
            // Check if backup directory exists and is writable
            $backupPath = storage_path('app/private/'.config('app.name', 'laravel-backup'));

            if (! file_exists($backupPath)) {
                return [
                    'healthy' => false,
                    'status' => 'backup_directory_missing',
                    'path' => $backupPath,
                    'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                ];
            }

            if (! is_writable($backupPath)) {
                return [
                    'healthy' => false,
                    'status' => 'backup_directory_not_writable',
                    'path' => $backupPath,
                    'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                ];
            }

            // Check recent backups
            $files = glob($backupPath.'/*.zip');
            if (empty($files)) {
                return [
                    'healthy' => false,
                    'status' => 'no_backups_found',
                    'path' => $backupPath,
                    'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                ];
            }

            // Get latest backup
            $latestBackup = null;
            $latestTime = 0;
            foreach ($files as $file) {
                $mtime = filemtime($file);
                if ($mtime > $latestTime) {
                    $latestTime = $mtime;
                    $latestBackup = $file;
                }
            }

            $backupAge = time() - $latestTime;
            $backupAgeHours = round($backupAge / 3600, 2);

            // Consider unhealthy if backup is older than 25 hours
            $isHealthy = $backupAgeHours < 25;
            $status = $backupAgeHours > 25 ? 'backup_too_old' : 'operational';

            return [
                'healthy' => $isHealthy,
                'status' => $status,
                'latest_backup' => basename($latestBackup),
                'backup_age_hours' => $backupAgeHours,
                'backup_count' => count($files),
                'backup_path' => $backupPath,
                'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
            ];

        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'status' => 'backup_check_failed',
                'error' => $e->getMessage(),
                'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
            ];
        }
    }

    /* Get application uptime */
    /**
     * @return array<string, mixed>
     */
    private function getUptime(): array
    {
        try {
            // This is a basic implementation
            // In production, you might want to track this more accurately
            $uptimeFile = storage_path('app/uptime.txt');

            if (! file_exists($uptimeFile)) {
                // Create uptime file with current timestamp
                file_put_contents($uptimeFile, time());
                $startTime = time();
            } else {
                $startTime = (int) file_get_contents($uptimeFile);
            }

            $uptime = time() - $startTime;

            return [
                'seconds' => $uptime,
                'human' => $this->formatUptime($uptime),
                'started_at' => Carbon::createFromTimestamp($startTime)->toISOString(),
            ];

        } catch (\Exception $e) {
            return [
                'error' => 'Could not determine uptime',
                'message' => $e->getMessage(),
            ];
        }
    }

    /* Format uptime in human readable format */
    private function formatUptime(int $seconds): string
    {
        if ($seconds < 60) {
            return "{$seconds} seconds";
        }

        if ($seconds < 3600) {
            $minutes = floor($seconds / 60);

            return "{$minutes} minutes";
        }

        if ($seconds < 86400) {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);

            return "{$hours}h {$minutes}m";
        }

        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);

        return "{$days}d {$hours}h";
    }
}
