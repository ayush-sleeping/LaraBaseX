<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * CODE STRUCTURE SUMMARY:
 * BackupMonitoringService
 * Monitors the health of backup processes
 * Check if backups are recent enough
 * Check backup file sizes
 * Check available storage space
 * Check backup file integrity
 * Check database connectivity
 * Collect backup metrics
 * Send health check report via email
 * Get backup statistics for dashboard
 * Calculate overall health score
 * Format bytes to human readable format
 */
class BackupMonitoringService
{
    /* Monitor backup health and send alerts if needed */
    /**
     * @return array{
     *   status: string,
     *   checks: array<string, array<string, mixed>>,
     *   warnings: array<int, string>,
     *   errors: array<int, string>,
     *   metrics: array<string, mixed>
     * }
     */
    public function performHealthCheck(): array
    {
        $results = [
            'status' => 'healthy',
            'checks' => [],
            'warnings' => [],
            'errors' => [],
            'metrics' => [],
        ];

        // Check backup age
        $ageCheck = $this->checkBackupAge();
        $results['checks']['backup_age'] = $ageCheck;
        if (! $ageCheck['healthy']) {
            $results['errors'][] = $ageCheck['message'];
            $results['status'] = 'unhealthy';
        }

        // Check backup size
        $sizeCheck = $this->checkBackupSize();
        $results['checks']['backup_size'] = $sizeCheck;
        if (! $sizeCheck['healthy']) {
            $results['warnings'][] = $sizeCheck['message'];
            if ($results['status'] === 'healthy') {
                $results['status'] = 'warning';
            }
        }

        // Check storage space
        $storageCheck = $this->checkStorageSpace();
        $results['checks']['storage_space'] = $storageCheck;
        if (! $storageCheck['healthy']) {
            $results['warnings'][] = $storageCheck['message'];
            if ($results['status'] === 'healthy') {
                $results['status'] = 'warning';
            }
        }

        // Check backup integrity
        $integrityCheck = $this->checkBackupIntegrity();
        $results['checks']['backup_integrity'] = $integrityCheck;
        if (! $integrityCheck['healthy']) {
            $results['errors'][] = $integrityCheck['message'];
            $results['status'] = 'unhealthy';
        }

        // Check database connectivity
        $dbCheck = $this->checkDatabaseConnectivity();
        $results['checks']['database_connectivity'] = $dbCheck;
        if (! $dbCheck['healthy']) {
            $results['errors'][] = $dbCheck['message'];
            $results['status'] = 'unhealthy';
        }

        // Collect metrics
        $results['metrics'] = $this->collectMetrics();

        return $results;
    }

    /* Check if backups are recent enough */
    /**
     * @return array{
     *   healthy: bool,
     *   message: string,
     *   last_backup: string|null,
     *   hours_old?: int
     * }
     */
    private function checkBackupAge(): array
    {
        $backupPath = storage_path('app/private/'.config('app.name', 'laravel-backup'));

        if (! File::exists($backupPath)) {
            return [
                'healthy' => false,
                'message' => 'No backup directory found',
                'last_backup' => null,
            ];
        }

        $backups = collect(File::files($backupPath))
            ->sortByDesc(function ($file) {
                return $file->getMTime();
            });

        if ($backups->isEmpty()) {
            return [
                'healthy' => false,
                'message' => 'No backups found',
                'last_backup' => null,
            ];
        }

        $latest = $backups->first();
        $lastBackupTime = Carbon::createFromTimestamp($latest->getMTime());
        $hours = (int) $lastBackupTime->diffInHours(now());

        // Consider unhealthy if backup is older than 25 hours (daily backup + 1 hour grace)
        $isHealthy = $hours <= 25;

        return [
            'healthy' => $isHealthy,
            'message' => $isHealthy
                ? "Latest backup is {$hours} hours old"
                : "Latest backup is {$hours} hours old (too old)",
            'last_backup' => $lastBackupTime->toISOString(),
            'hours_old' => $hours,
        ];
    }

    /* Check backup file sizes */
    /**
     * @return array{
     *   healthy: bool,
     *   message: string,
     *   total_size: int,
     *   latest_size?: int,
     *   backup_count?: int
     * }
     */
    private function checkBackupSize(): array
    {
        $backupPath = storage_path('app/private/'.config('app.name', 'laravel-backup'));

        if (! File::exists($backupPath)) {
            return [
                'healthy' => false,
                'message' => 'No backup directory found',
                'total_size' => 0,
            ];
        }

        $backups = File::files($backupPath);
        $totalSize = 0;
        $latestSize = 0;

        foreach ($backups as $backup) {
            $size = $backup->getSize();
            $totalSize += $size;

            // Get latest backup size
            if ($backup->getMTime() === collect($backups)->max('getMTime')) {
                $latestSize = $size;
            }
        }

        // Warning if latest backup is suspiciously small (less than 1KB)
        $isHealthy = $latestSize > 1024;

        return [
            'healthy' => $isHealthy,
            'message' => $isHealthy
                ? 'Backup sizes are normal (latest: '.$this->formatBytes($latestSize).')'
                : 'Latest backup is suspiciously small: '.$this->formatBytes($latestSize),
            'total_size' => $totalSize,
            'latest_size' => $latestSize,
            'backup_count' => count($backups),
        ];
    }

    /* Check available storage space */
    /**
     * @return array{
     *   healthy: bool,
     *   message: string,
     *   free_space: int,
     *   total_space: int,
     *   usage_percentage: float
     * }
     */
    private function checkStorageSpace(): array
    {
        $path = storage_path();
        $freeSpaceRaw = disk_free_space($path);
        $totalSpaceRaw = disk_total_space($path);
        $freeSpace = $freeSpaceRaw !== false ? (int) $freeSpaceRaw : 0;
        $totalSpace = $totalSpaceRaw !== false ? (int) $totalSpaceRaw : 0;
        $usedSpace = $totalSpace - $freeSpace;
        $usagePercentage = $totalSpace > 0 ? round(($usedSpace / $totalSpace) * 100, 2) : 0.0;

        // Warning if usage is above 80%
        $isHealthy = $usagePercentage < 80;

        return [
            'healthy' => $isHealthy,
            'message' => $isHealthy
                ? "Storage usage is {$usagePercentage}% (".$this->formatBytes($freeSpace).' free)'
                : "Storage usage is high: {$usagePercentage}% (".$this->formatBytes($freeSpace).' free)',
            'free_space' => $freeSpace,
            'total_space' => $totalSpace,
            'usage_percentage' => $usagePercentage,
        ];
    }

    /* Check backup file integrity */
    /**
     * @return array{
     *   healthy: bool,
     *   message: string,
     *   checked_files: int,
     *   corrupt_files?: int
     * }
     */
    private function checkBackupIntegrity(): array
    {
        $backupPath = storage_path('app/private/'.config('app.name', 'laravel-backup'));

        if (! File::exists($backupPath)) {
            return [
                'healthy' => false,
                'message' => 'No backup directory found',
                'checked_files' => 0,
            ];
        }

        $backups = File::files($backupPath);
        $corruptCount = 0;
        $checkedCount = 0;

        foreach ($backups as $backup) {
            $checkedCount++;

            try {
                // Basic integrity check - verify it's a valid zip
                $zip = new \ZipArchive;
                $result = $zip->open($backup->getPathname());

                if ($result !== true) {
                    $corruptCount++;
                } else {
                    $zip->close();
                }
            } catch (\Exception $e) {
                $corruptCount++;
            }
        }

        $isHealthy = $corruptCount === 0;

        return [
            'healthy' => $isHealthy,
            'message' => $isHealthy
                ? "All {$checkedCount} backup files are valid"
                : "{$corruptCount} out of {$checkedCount} backup files are corrupted",
            'checked_files' => $checkedCount,
            'corrupt_files' => $corruptCount,
        ];
    }

    /* Check database connectivity */
    /**
     * @return array{
     *   healthy: bool,
     *   message: string,
     *   connection: string,
     *   error?: string
     * }
     */
    private function checkDatabaseConnectivity(): array
    {
        try {
            DB::connection()->getPdo();

            // Test a simple query
            $result = DB::select('SELECT 1 as test');

            $isHealthy = ! empty($result);

            return [
                'healthy' => $isHealthy,
                'message' => $isHealthy ? 'Database connection is healthy' : 'Database query failed',
                'connection' => config('database.default', 'unknown'),
            ];

        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'message' => 'Database connection failed: '.$e->getMessage(),
                'connection' => config('database.default', 'unknown'),
                'error' => $e->getMessage(),
            ];
        }
    }

    /* Collect backup metrics */
    /**
     * @return array{
     *   backup_count: int,
     *   total_backup_size: int,
     *   average_backup_size: int,
     *   oldest_backup: string|null,
     *   newest_backup: string|null
     * }
     */
    private function collectMetrics(): array
    {
        $backupPath = storage_path('app/private/'.config('app.name', 'laravel-backup'));
        $metrics = [
            'backup_count' => 0,
            'total_backup_size' => 0,
            'average_backup_size' => 0,
            'oldest_backup' => null,
            'newest_backup' => null,
        ];

        if (! File::exists($backupPath)) {
            return $metrics;
        }

        $backups = collect(File::files($backupPath));
        $metrics['backup_count'] = $backups->count();

        if ($backups->isNotEmpty()) {
            $totalSize = $backups->sum(function ($backup) {
                return $backup->getSize();
            });

            $metrics['total_backup_size'] = $totalSize;
            $metrics['average_backup_size'] = round($totalSize / $backups->count());

            $sorted = $backups->sortBy(function ($file) {
                return $file->getMTime();
            });

            $metrics['oldest_backup'] = Carbon::createFromTimestamp($sorted->first()->getMTime())->toISOString();
            $metrics['newest_backup'] = Carbon::createFromTimestamp($sorted->last()->getMTime())->toISOString();
        }

        return $metrics;
    }

    /* Send health check report via email */
    /**
     * @param array{
     *   status: string,
     *   checks: array<string, array<string, mixed>>,
     *   warnings: array<int, string>,
     *   errors: array<int, string>,
     *   metrics: array<string, mixed>
     * } $healthCheck
     */
    public function sendHealthReport(array $healthCheck): bool
    {
        try {
            $emailTo = config('backup.notifications.mail.to');

            if (! $emailTo) {
                Log::warning('No backup notification email configured');

                return false;
            }

            // For now, just log the report. In production, you'd send an actual email
            Log::info('Backup health report', [
                'status' => $healthCheck['status'],
                'checks' => $healthCheck['checks'],
                'warnings' => $healthCheck['warnings'],
                'errors' => $healthCheck['errors'],
                'metrics' => $healthCheck['metrics'],
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send backup health report', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /* Get backup statistics for dashboard */
    /**
     * @return array{
     *   status: string,
     *   last_backup: string|null,
     *   backup_count: int,
     *   total_size: string,
     *   storage_usage: float|int,
     *   health_score: int
     * }
     */
    public function getBackupStatistics(): array
    {
        $healthCheck = $this->performHealthCheck();

        return [
            'status' => $healthCheck['status'],
            'last_backup' => $healthCheck['checks']['backup_age']['last_backup'] ?? null,
            'backup_count' => $healthCheck['metrics']['backup_count'],
            'total_size' => $this->formatBytes($healthCheck['metrics']['total_backup_size']),
            'storage_usage' => $healthCheck['checks']['storage_space']['usage_percentage'] ?? 0,
            'health_score' => $this->calculateHealthScore($healthCheck),
        ];
    }

    /* Calculate overall health score (0-100) */
    /**
     * @param array{
     *   checks: array<string, array<string, mixed>>
     * } $healthCheck
     */
    private function calculateHealthScore(array $healthCheck): int
    {
        $checks = $healthCheck['checks'];
        $totalChecks = count($checks);
        $healthyChecks = 0;

        foreach ($checks as $check) {
            if ($check['healthy']) {
                $healthyChecks++;
            }
        }

        return $totalChecks > 0 ? round(($healthyChecks / $totalChecks) * 100) : 0;
    }

    /* Format bytes to human readable format */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision).' '.$units[$i];
    }
}
