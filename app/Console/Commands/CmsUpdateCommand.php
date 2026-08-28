<?php

namespace App\Console\Commands;

use App\Services\CmsUpdaterService;
use Illuminate\Console\Command;

class CmsUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:update
                            {--check : Check for updates without downloading or installing}
                            {--force : Force update even if already up to date}
                            {--y|yes : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for or install the latest Lara-CMS release';

    public function handle(CmsUpdaterService $updater): int
    {
        $this->info('Checking for Lara-CMS updates...');

        $status = $updater->check(forceRefresh: true);

        if ($status['status'] === 'check_failed') {
            $this->error($status['message'] ?? 'Failed to reach update server.');

            return self::FAILURE;
        }

        $current = $status['current_version'];
        $latest = $status['latest_version'];
        $updateAvailable = $status['update_available'];

        $this->line("Current version: <comment>v{$current}</comment>");
        $this->line("Latest version:  <comment>v{$latest}</comment>");

        if ($this->option('check')) {
            if ($updateAvailable) {
                $this->warn("A new version (v{$latest}) is available!");
            } else {
                $this->info('Your system is up to date.');
            }

            return self::SUCCESS;
        }

        $force = (bool) $this->option('force');

        if (! $updateAvailable && ! $force) {
            $this->info('Your system is already running the latest version.');

            return self::SUCCESS;
        }

        if ($updateAvailable) {
            $this->warn("An update to v{$latest} is available.");
        } elseif ($force) {
            $this->warn("Forcing update/re-installation of v{$latest}...");
        }

        $skipPrompt = $this->option('yes') || $this->option('no-interaction');

        if (! $skipPrompt && ! $this->confirm('Do you want to proceed with the update?', true)) {
            $this->line('Update aborted.');

            return self::SUCCESS;
        }

        $this->info('Starting update process...');

        $result = $updater->run(
            logger: fn (string $log) => $this->line("  {$log}"),
            force: $force,
        );

        if ($result['success']) {
            $this->newLine();
            $this->info("✓ Successfully updated Lara-CMS to v{$result['version']}!");

            return self::SUCCESS;
        }

        $this->newLine();
        $this->error('Update failed: '.($result['error'] ?? $result['message'] ?? 'Unknown error'));

        return self::FAILURE;
    }
}
