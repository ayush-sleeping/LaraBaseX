<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * CODE STRUCTURE SUMMARY:
 * name and signature of the console command
 * console command description
 * execute the console command
 */
class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--only-db : Only backup the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database and application files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting backup process...');
        try {
            if ($this->option('only-db')) {
                $this->call('backup:run', ['--only-db' => true]);
            } else {
                $this->call('backup:run');
            }

            $this->info('Backup completed successfully!');
        } catch (\Exception $e) {
            $this->error('Backup failed: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
