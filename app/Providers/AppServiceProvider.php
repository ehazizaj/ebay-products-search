<?php

namespace App\Providers;

use App\Repositories\eBayRepository\eBayRepository;
use App\Repositories\eBayRepository\eBayRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(eBayRepositoryInterface::class, eBayRepository::class);

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
