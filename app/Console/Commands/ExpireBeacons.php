<?php

namespace App\Console\Commands;

use App\Models\LiveBeacon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireBeacons extends Command
{
    protected $signature = 'beacons:expire';

    protected $description = 'Expire stale live beacons (older than 2 hours)';

    public function handle(): int
    {
        $count = LiveBeacon::expireStale();

        if ($count > 0) {
            Log::info("[Beacons] Expired {$count} stale live beacons");
        }

        $this->info("Expired {$count} stale live beacons.");

        return self::SUCCESS;
    }
}
