<?php

namespace MorningTrain\Laravel\Dev\Commands;

use Illuminate\Support\ServiceProvider;

class LaravelDevCommandsServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([

                /// Database commands
                Database\Create::class,
                Database\Setup::class,

                /// Environment commands
                Environment\CopyFromExample::class,
                Environment\Set::class,
                Environment\Setup::class,

            ]);
        }
    }

}
