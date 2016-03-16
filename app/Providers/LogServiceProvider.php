<?php

namespace Gis\Providers;

use Illuminate\Support\ServiceProvider;
use Gis\Services\Logging\ExceptionLog;

class LogServiceProvider extends ServiceProvider
{
    
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
    
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind ( 'Gis\Services\Logging\ExceptionLogInterface', function ()
        {
            return new ExceptionLog ( new \Exception () );
        } );
        $this->app->bind ( 'Gis\Services\Logging\ApplicationLogInterface', 'Gis\Services\Logging\ApplicationLog' );
    }
}
