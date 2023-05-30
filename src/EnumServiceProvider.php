<?php

namespace Kwaadpepper\Enum;

use Illuminate\Support\ServiceProvider;
use Kwaadpepper\Enum\Console\CreateEnum;

class EnumServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'enum');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/enum'),
        ]);

        // Register the command if we are using the application via the CLI.
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateEnum::class,
            ]);
        }
    }
}
