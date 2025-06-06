<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use SocialiteProviders\Manager\SocialiteWasCalled;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tables\Table;

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
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('azure', \SocialiteProviders\Azure\Provider::class);
        });
         // CSS PERSONALITZAT
        FilamentAsset::register([
            Css::make('custom-stylesheet', __DIR__ . '/../../resources/css/filament.css'),
        ]);
        //Treure pagination tots
        Table::configureUsing(function (Table $table) {
            $table->paginated([10, 25, 50, 100]);
        });

    }
}
