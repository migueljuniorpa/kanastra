<?php

namespace App\Providers;

use App\Services\Contratcs\BoletoGeneratorInterface;
use App\Services\BoletoGeneratePdf;
use App\Services\Contratcs\GeneratedBoletoMailInterface;
use App\Services\GeneratedBoletoMail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BoletoGeneratorInterface::class, BoletoGeneratePdf::class);
        $this->app->bind(GeneratedBoletoMailInterface::class, GeneratedBoletoMail::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
