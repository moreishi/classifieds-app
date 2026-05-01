<?php

namespace App\Providers;

use App\Services\Payment\GCashGateway;
use App\Services\Payment\PaymentGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the default payment gateway.
        // Swap this to any PaymentGateway implementation to change providers.
        $this->app->bind(PaymentGateway::class, GCashGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
