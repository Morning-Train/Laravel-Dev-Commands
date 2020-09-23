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

            $this->publishes([
                __DIR__ . '/../config/dev-commands.php' => config_path('dev-commands.php'),
            ], 'dev-commands');

            $this->commands([

                /// Database commands
                Database\Create::class,
                Database\Setup::class,
                Database\Drop::class,

                /// Environment commands
                Environment\CopyFromExample::class,
                Environment\Set::class,
                Environment\Setup::class,

                /// System commands
                System\Build::class,
                System\Refresh::class,
                System\Setup::class,

            ]);
        }
    }

}
