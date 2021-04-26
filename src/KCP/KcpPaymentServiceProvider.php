<?php

namespace EvansKim\KCP;

use Illuminate\Support\ServiceProvider;

class KcpPaymentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $migname = "0000_00_00_000000_create_kcp_payments_table.php";
        $migname = str_replace("0000_00_00_000000", date("Y_m_d_His"), $migname);
        $this->publishes([
            __DIR__.'/migrations/0000_00_00_000000_create_kcp_payments_table.php' => base_path("/database/migrations/".$migname),
        ], 'kcp_payment');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'kcp');
        $this->mergeConfigFrom(__DIR__ . "/config/services.php", 'services');
    }
}
