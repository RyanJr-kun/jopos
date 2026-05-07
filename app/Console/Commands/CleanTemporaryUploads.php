<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanTemporaryUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uploads:clean-tmp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up temporary files older than 24 hours from storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of temporary upload directories...');

        $directories = config('uploads.temporary_directories');
        if (empty($directories)) {
            $this->warn('No temporary directories configured in config/uploads.php. Exiting.');
            return;
        }

        $cutoff = now()->subDay()->getTimestamp();
        $totalDeleted = 0;

        foreach ($directories as $directory) {
            $files = Storage::disk('r2')->files($directory);
            $deletedCount = 0;

            foreach ($files as $file) {
                // Jangan hapus file .gitignore jika ada
                if (basename($file) === '.gitignore') {
                    continue;
                }

                if (Storage::disk('r2')->lastModified($file) < $cutoff) {
                    Storage::disk('r2')->delete($file);
                    $deletedCount++;
                }
            }

            $totalDeleted += $deletedCount;
            $this->line("Cleaned {$deletedCount} old files from '{$directory}'.");
        }

        $this->info("Cleanup complete. Total files deleted: {$totalDeleted}.");
    }
}
