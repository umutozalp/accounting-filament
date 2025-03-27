<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        \App\Models\VoucherItem::observe(\App\Observers\VoucherItemObserver::class);
    }

    protected $observers = [
        \App\Models\VoucherItem::class => [\App\Observers\VoucherItemObserver::class],
    ];
} 