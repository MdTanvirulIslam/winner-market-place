<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use ZipArchive;

// Nightly backup: database dump + a zip of the release files. Both land in
// storage/app/backups (outside the web root); the newest KEEP of each kind
// are retained. Scheduled daily in routes/console.php — on shared hosting,
// point a cPanel cron at `php artisan schedule:run` every minute.
class BackupRun extends Command
{
    protected $signature = 'backup:run';

    protected $description = 'Back up the database and release files to storage/app/backups';

    private const KEEP = 7;

    public function handle(): int
    {
        $disk = Storage::disk('local');
        $disk->makeDirectory('backups');
        $stamp = now()->format('Y-m-d-His');

        $this->backupDatabase($stamp);
        $this->backupReleases($stamp);
        $this->prune();

        return self::SUCCESS;
    }

    private function backupDatabase(string $stamp): void
    {
        $connection = config('database.default');

        if ($connection !== 'mysql') {
            $this->warn("Database dump skipped — connection '{$connection}' is not mysql.");

            return;
        }

        $config = config('database.connections.mysql');
        $target = Storage::disk('local')->path('backups/db-' . $stamp . '.sql');

        $process = new Process([
            config('marketplace.backup.mysqldump_path'),
            '--host=' . $config['host'],
            '--port=' . $config['port'],
            '--user=' . $config['username'],
            '--single-transaction',
            '--routines',
            '--result-file=' . $target,
            $config['database'],
        ], null, [
            // Password via environment, never as a CLI argument (visible in
            // process lists) and never printed.
            'MYSQL_PWD' => $config['password'] ?? '',
        ]);

        $process->setTimeout(300)->run();

        if (! $process->isSuccessful() || ! is_file($target) || filesize($target) === 0) {
            @unlink($target);
            $this->error('Database dump FAILED: ' . substr($process->getErrorOutput(), 0, 300));

            return;
        }

        $this->info('Database dumped: backups/db-' . $stamp . '.sql (' . round(filesize($target) / 1024) . ' KB)');
    }

    private function backupReleases(string $stamp): void
    {
        $disk = Storage::disk('local');
        $files = $disk->allFiles('releases');

        if ($files === []) {
            $this->warn('Release backup skipped — no release files.');

            return;
        }

        $zipPath = $disk->path('backups/releases-' . $stamp . '.zip');
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error('Release backup FAILED: could not create zip.');

            return;
        }

        foreach ($files as $file) {
            $zip->addFile($disk->path($file), $file);
        }

        $zip->close();

        $this->info('Releases zipped: backups/releases-' . $stamp . '.zip (' . count($files) . ' files, ' . round(filesize($zipPath) / 1024) . ' KB)');
    }

    private function prune(): void
    {
        $disk = Storage::disk('local');

        foreach (['db-' => '.sql', 'releases-' => '.zip'] as $prefix => $suffix) {
            $files = collect($disk->files('backups'))
                ->filter(fn ($file) => str_starts_with(basename($file), $prefix) && str_ends_with($file, $suffix))
                ->sortDesc()
                ->values();

            foreach ($files->slice(self::KEEP) as $old) {
                $disk->delete($old);
                $this->line('Pruned ' . $old);
            }
        }
    }
}
