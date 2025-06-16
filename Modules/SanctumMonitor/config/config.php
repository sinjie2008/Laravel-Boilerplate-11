<?php

return [
    'name' => 'SanctumMonitor',
    'log_retention_days' => env('SANCTUM_MONITOR_LOG_RETENTION_DAYS', 30),
    'enable_logging' => true,
];
