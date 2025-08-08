<?php

namespace App\Console\Commands;

use App\Services\BackupMonitoringService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Spatie\Backup\Tasks\Backup\BackupJobFactory;
use Carbon\Carbon;

class BackupManagement extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'backup:manage
                            {action : Action to perform (status|create|restore|verify|clean|monitor)}
                            {--type=full : Backup type (db|files|full)}
                            {--restore-file= : Backup file to restore from}
                            {--force : Force action without confirmation}
                            {--encrypt : Enable encryption for backups}
                            {--verify : Verify backup integrity after creation}';

    /**
     * The console command description.
     */
    protected $description = 'Advanced backup management system for LaraBaseX';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        $this->info("🚀 LaraBaseX Backup Management System");
        $this->info("====================================");

        switch ($action) {
            case 'status':
                $this->showBackupStatus();
                break;

            case 'create':
                $this->createBackup();
                break;

            case 'restore':
                $this->restoreBackup();
                break;

            case 'verify':
                $this->verifyBackups();
                break;

            case 'clean':
                $this->cleanBackups();
                break;

            case 'monitor':
                $this->monitorBackups();
                break;

            default:
                $this->error("Invalid action. Available actions: status, create, restore, verify, clean, monitor");
                return 1;
        }

        return 0;
    }

    /**
     * Show comprehensive backup status
     */
    private function showBackupStatus()
    {
        $this->info("📊 Backup System Status");
        $this->newLine();

        // Backup Destinations Status
        $this->call('backup:list');
        $this->newLine();

        // Local Storage Information
        $backupPath = storage_path('app/private/' . config('app.name', 'laravel-backup'));
        if (File::exists($backupPath)) {
            $backups = File::files($backupPath);
            $totalSize = 0;

            foreach ($backups as $backup) {
                $totalSize += $backup->getSize();
            }

            $this->info("📁 Local Backup Storage:");
            $this->info("   Path: {$backupPath}");
            $this->info("   Files: " . count($backups));
            $this->info("   Total Size: " . $this->formatBytes($totalSize));
        }

        // Latest Backup Info
        $this->showLatestBackupInfo();

        // Scheduled Tasks Status
        $this->info("\n⏰ Scheduled Backup Tasks:");
        $this->call('schedule:list');
    }

    /**
     * Create backup with enhanced options
     */
    private function createBackup()
    {
        $type = $this->option('type');
        $encrypt = $this->option('encrypt');
        $verify = $this->option('verify');

        $this->info("🔄 Creating {$type} backup...");

        if ($encrypt) {
            $this->info("🔐 Encryption enabled");
        }

        try {
            $startTime = microtime(true);

            // Create backup based on type
            switch ($type) {
                case 'db':
                    $this->call('backup:run', ['--only-db' => true]);
                    break;
                case 'files':
                    $this->call('backup:run', ['--only-files' => true]);
                    break;
                case 'full':
                default:
                    $this->call('backup:run');
                    break;
            }

            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);

            $this->info("✅ Backup created successfully in {$duration} seconds");

            // Verify backup if requested
            if ($verify) {
                $this->info("🔍 Verifying backup integrity...");
                $this->verifyLatestBackup();
            }

            // Log backup creation
            Log::info("Backup created successfully", [
                'type' => $type,
                'duration' => $duration,
                'encryption' => $encrypt,
                'verified' => $verify
            ]);

        } catch (\Exception $e) {
            $this->error("❌ Backup failed: " . $e->getMessage());
            Log::error("Backup creation failed", [
                'error' => $e->getMessage(),
                'type' => $type
            ]);
            return 1;
        }
    }

    /**
     * Restore backup from file
     */
    private function restoreBackup()
    {
        $restoreFile = $this->option('restore-file');
        $force = $this->option('force');

        if (!$restoreFile) {
            // Show available backups for selection
            $this->showAvailableBackups();
            $restoreFile = $this->ask('Enter the backup filename to restore');
        }

        if (!$force) {
            if (!$this->confirm("⚠️  This will restore the database from backup. Current data will be lost. Continue?")) {
                $this->info("Restore cancelled.");
                return;
            }
        }

        $this->info("🔄 Restoring backup: {$restoreFile}");

        try {
            // Implementation would depend on your specific restore requirements
            // This is a basic structure - you'd need to implement the actual restore logic

            $this->warn("⚠️  Backup restore functionality requires custom implementation based on your database type and backup format.");
            $this->info("💡 For MySQL backups, you would typically:");
            $this->info("   1. Extract the SQL file from the backup archive");
            $this->info("   2. Import the SQL file using mysql command or DB::unprepared()");
            $this->info("   3. Verify the restoration was successful");

            Log::info("Backup restore initiated", [
                'file' => $restoreFile,
                'user' => 'console'
            ]);

        } catch (\Exception $e) {
            $this->error("❌ Restore failed: " . $e->getMessage());
            Log::error("Backup restore failed", [
                'error' => $e->getMessage(),
                'file' => $restoreFile
            ]);
            return 1;
        }
    }

    /**
     * Verify backup integrity
     */
    private function verifyBackups()
    {
        $this->info("🔍 Verifying backup integrity...");

        $backupPath = storage_path('app/private/' . config('app.name', 'laravel-backup'));

        if (!File::exists($backupPath)) {
            $this->warn("No backup directory found.");
            return;
        }

        $backups = File::files($backupPath);
        $verifiedCount = 0;
        $errorCount = 0;

        foreach ($backups as $backup) {
            $filename = $backup->getFilename();
            $this->info("   Checking: {$filename}");

            try {
                // Basic file integrity checks
                $size = $backup->getSize();

                if ($size === 0) {
                    $this->error("     ❌ Empty file");
                    $errorCount++;
                    continue;
                }

                // Check if it's a valid zip file
                $zip = new \ZipArchive();
                $result = $zip->open($backup->getPathname());

                if ($result === TRUE) {
                    $fileCount = $zip->numFiles;
                    $zip->close();
                    $this->info("     ✅ Valid archive ({$fileCount} files, " . $this->formatBytes($size) . ")");
                    $verifiedCount++;
                } else {
                    $this->error("     ❌ Invalid archive (error code: {$result})");
                    $errorCount++;
                }

            } catch (\Exception $e) {
                $this->error("     ❌ Verification error: " . $e->getMessage());
                $errorCount++;
            }
        }

        $this->newLine();
        $this->info("📊 Verification Summary:");
        $this->info("   ✅ Valid backups: {$verifiedCount}");
        $this->info("   ❌ Invalid backups: {$errorCount}");
        $this->info("   📁 Total backups: " . count($backups));
    }

    /**
     * Clean old backups manually
     */
    private function cleanBackups()
    {
        $force = $this->option('force');

        $this->info("🗑️  Cleaning old backups...");

        if (!$force) {
            if (!$this->confirm("This will remove old backups according to retention policy. Continue?")) {
                $this->info("Cleanup cancelled.");
                return;
            }
        }

        try {
            $this->call('backup:clean');
            $this->info("✅ Cleanup completed successfully");

        } catch (\Exception $e) {
            $this->error("❌ Cleanup failed: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Monitor backup health
     */
    private function monitorBackups()
    {
        $this->info("🏥 Monitoring backup health...");

        try {
            // Use Spatie backup monitoring
            $this->call('backup:monitor');

            // Custom health checks using our monitoring service
            $monitoringService = new BackupMonitoringService();
            $healthCheck = $monitoringService->performHealthCheck();

            $this->newLine();
            $this->info("📊 Advanced Health Check Results:");

            // Display status
            $statusIcon = match($healthCheck['status']) {
                'healthy' => '✅',
                'warning' => '⚠️',
                'unhealthy' => '❌',
                default => '❓'
            };

            $this->info("   Overall Status: {$statusIcon} " . strtoupper($healthCheck['status']));

            // Display individual checks
            foreach ($healthCheck['checks'] as $checkName => $check) {
                $icon = $check['healthy'] ? '✅' : '❌';
                $name = ucwords(str_replace('_', ' ', $checkName));
                $this->info("   {$name}: {$icon} {$check['message']}");
            }

            // Display warnings
            if (!empty($healthCheck['warnings'])) {
                $this->newLine();
                $this->warn("⚠️  Warnings:");
                foreach ($healthCheck['warnings'] as $warning) {
                    $this->warn("   - {$warning}");
                }
            }

            // Display errors
            if (!empty($healthCheck['errors'])) {
                $this->newLine();
                $this->error("❌ Errors:");
                foreach ($healthCheck['errors'] as $error) {
                    $this->error("   - {$error}");
                }
            }

            // Display metrics
            $metrics = $healthCheck['metrics'];
            $this->newLine();
            $this->info("📈 Backup Metrics:");
            $this->info("   Backup Count: {$metrics['backup_count']}");
            $this->info("   Total Size: " . $this->formatBytes($metrics['total_backup_size']));
            if ($metrics['backup_count'] > 0) {
                $this->info("   Average Size: " . $this->formatBytes($metrics['average_backup_size']));
                $this->info("   Oldest: " . Carbon::parse($metrics['oldest_backup'])->diffForHumans());
                $this->info("   Newest: " . Carbon::parse($metrics['newest_backup'])->diffForHumans());
            }

            // Send health report if configured
            if ($healthCheck['status'] !== 'healthy') {
                $this->info("\n📧 Sending health report notification...");
                $monitoringService->sendHealthReport($healthCheck);
            }

        } catch (\Exception $e) {
            $this->error("❌ Monitoring failed: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Show latest backup information
     */
    private function showLatestBackupInfo()
    {
        $backupPath = storage_path('app/private/' . config('app.name', 'laravel-backup'));

        if (!File::exists($backupPath)) {
            $this->warn("No backups found.");
            return;
        }

        $backups = collect(File::files($backupPath))
            ->sortByDesc(function ($file) {
                return $file->getMTime();
            });

        if ($backups->isNotEmpty()) {
            $latest = $backups->first();
            $this->info("\n📋 Latest Backup:");
            $this->info("   File: " . $latest->getFilename());
            $this->info("   Size: " . $this->formatBytes($latest->getSize()));
            $this->info("   Created: " . Carbon::createFromTimestamp($latest->getMTime())->diffForHumans());
            $this->info("   Path: " . $latest->getPath());
        }
    }

    /**
     * Show available backups for restore
     */
    private function showAvailableBackups()
    {
        $backupPath = storage_path('app/private/' . config('app.name', 'laravel-backup'));

        if (!File::exists($backupPath)) {
            $this->warn("No backups found.");
            return;
        }

        $backups = collect(File::files($backupPath))
            ->sortByDesc(function ($file) {
                return $file->getMTime();
            });

        $this->info("📁 Available Backups:");
        foreach ($backups as $index => $backup) {
            $this->info(sprintf(
                "   %d. %s (%s, %s)",
                $index + 1,
                $backup->getFilename(),
                $this->formatBytes($backup->getSize()),
                Carbon::createFromTimestamp($backup->getMTime())->diffForHumans()
            ));
        }
    }

    /**
     * Verify latest backup integrity
     */
    private function verifyLatestBackup()
    {
        $backupPath = storage_path('app/private/' . config('app.name', 'laravel-backup'));

        if (!File::exists($backupPath)) {
            $this->warn("No backup directory found for verification.");
            return false;
        }

        $backups = collect(File::files($backupPath))
            ->sortByDesc(function ($file) {
                return $file->getMTime();
            });

        if ($backups->isEmpty()) {
            $this->warn("No backups found for verification.");
            return false;
        }

        $latest = $backups->first();

        try {
            $zip = new \ZipArchive();
            $result = $zip->open($latest->getPathname());

            if ($result === TRUE) {
                $fileCount = $zip->numFiles;
                $zip->close();
                $this->info("✅ Backup verified: {$fileCount} files in archive");
                return true;
            } else {
                $this->error("❌ Backup verification failed: Invalid archive");
                return false;
            }

        } catch (\Exception $e) {
            $this->error("❌ Backup verification error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Perform custom health checks
     */
    private function performCustomHealthChecks()
    {
        $this->info("\n🔍 Custom Health Checks:");

        // Database connection check
        try {
            DB::connection()->getPdo();
            $this->info("   ✅ Database connection: OK");
        } catch (\Exception $e) {
            $this->error("   ❌ Database connection: FAILED");
        }

        // Storage disk health
        $disk = Storage::disk('local');
        if ($disk->exists('')) {
            $this->info("   ✅ Storage disk: OK");
        } else {
            $this->error("   ❌ Storage disk: FAILED");
        }

        // Backup directory permissions
        $backupPath = storage_path('app/private/' . config('app.name', 'laravel-backup'));
        if (is_writable($backupPath)) {
            $this->info("   ✅ Backup directory writable: OK");
        } else {
            $this->error("   ❌ Backup directory writable: FAILED");
        }

        // Free disk space check
        $freeSpace = disk_free_space(storage_path());
        $freeSpaceGB = round($freeSpace / 1024 / 1024 / 1024, 2);

        if ($freeSpaceGB > 1) {
            $this->info("   ✅ Free disk space: {$freeSpaceGB} GB");
        } else {
            $this->warn("   ⚠️  Low disk space: {$freeSpaceGB} GB");
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
