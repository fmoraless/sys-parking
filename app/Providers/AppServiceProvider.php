<?php

namespace App\Providers;

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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('layouts.template', function($view)        {

            /*$empresa = 'Estacionamiento Plaza';

            $view->with(['empresa'=> $empresa ]);*/

            $tipo = \App\Tipo::count();

            $view->with(['tipos'=> $tipo ]);

        });
    }
}
