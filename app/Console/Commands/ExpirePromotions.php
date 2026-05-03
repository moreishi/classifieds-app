<?php

namespace App\Console\Commands;

use App\Models\ListingPromotion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpirePromotions extends Command
{
    protected $signature = 'promotions:expire';

    protected $description = 'Deactivate expired listing promotions and clear featured_until flags';

    public function handle(): int
    {
        $count = ListingPromotion::expireOld();

        if ($count > 0) {
            Log::info("[Promotions] Expired {$count} old promotions");
        }

        $this->info("Expired {$count} promotions.");

        return self::SUCCESS;
    }
}
