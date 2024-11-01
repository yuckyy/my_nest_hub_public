<?php

namespace App\Providers;

use App\Models\PetsTypes;
use App\Models\Property;
use App\Models\State;
use App\Models\Unit;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */

    public function boot()
    {
//get petsTypes
        View::composer('applications.add', function($view) {
            $view->with(['petsTypes' => PetsTypes::get(), 'states' => State::get(), 'units' => Unit::get()]);
        });

        View::composer('tenant.applications.add', function($view) {
            $view->with(['petsTypes' => PetsTypes::get(), 'states' => State::get(), 'units' => Unit::get()]);
        });

        View::composer('applications.add-from-list', function($view) {
            $view->with(['petsTypes' => PetsTypes::get(), 'states' => State::get(), 'units' => Unit::get()]);
        });
//
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
