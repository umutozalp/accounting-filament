<?php

namespace App\Providers;

use App\Models\VoucherItem;
use App\Observers\VoucherItemObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        VoucherItem::observe(VoucherItemObserver::class);
    }
}
