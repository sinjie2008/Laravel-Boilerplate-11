<?php

declare(strict_types=1);

namespace Modules\SanctumMonitor\App\Console;

use Illuminate\Console\Command;
use Modules\SanctumMonitor\App\Models\ApiActivity;
use Modules\SanctumMonitor\App\Models\TokenAuditLog;

class PruneLogs extends Command
{
    protected $signature = 'sanctum-monitor:prune-logs {--days=}';

    protected $description = 'Prune Sanctum monitor logs.';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?? config('sanctummonitor.log_retention_days'));

        ApiActivity::where('created_at', '<', now()->subDays($days))->delete();
        TokenAuditLog::where('created_at', '<', now()->subDays($days))->delete();

        $this->info('Sanctum monitor logs pruned.');

        return self::SUCCESS;
    }
}
