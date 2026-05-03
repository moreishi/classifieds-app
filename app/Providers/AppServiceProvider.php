<?php

namespace App\Providers;

use App\Services\Payment\PayMongoGateway;
use App\Services\Payment\PaymentGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the default payment gateway for user verification.
        // Swap to any PaymentGateway implementation to change providers.
        $this->app->bind(PaymentGateway::class, PayMongoGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
