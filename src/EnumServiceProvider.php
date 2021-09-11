<?php

namespace Kwaadpepper\Enum;

use Illuminate\Support\ServiceProvider;

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
    }
}
