<?php

namespace smk_vendor\smk_package;

use Illuminate\Support\ServiceProvider;
use smk_vendor\smk_package\smk_command\MigrateWx;

class smk_packageServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/smk_resources/smk_config/qy_cfg.php' => config_path('qy_cfg.php'),
        ]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(array(
            MigrateWx::class
        ));

        $this->mergeConfigFrom(
            __DIR__.'/smk_resources/smk_config/qy_cfg.php', 'qy_cfg'
        );
    }
}