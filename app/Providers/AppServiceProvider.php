<?php

namespace App\Providers;

use App\Models\User;
use App\Repositories\ApplicationsRepository;
use App\Repositories\Contracts\ApplicationsRepositoryInterface;
use App\Repositories\Contracts\LeasesRepositoryInterface;
use App\Repositories\Contracts\PropertiesRepositoryInterface;
use App\Repositories\Contracts\UniqueLinkRepositoryInterface;
use App\Repositories\Contracts\UnitRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\LeasesRepository;
use App\Repositories\PropertiesRepository;
use App\Repositories\UniqueLinkRepository;
use App\Repositories\UnitRepository;
use App\Repositories\UserRepository;
use App\Services\Contracts\UniqueLinkServiceInterface;
use App\Services\Contracts\StripeServiceInterface;
use App\Services\Contracts\StripeSubscriptionServiceInterface;
use App\Services\UniqueLinkService;
use App\Grids\UsersGridInterface;
use App\Grids\UsersGrid;
use App\Grids\CouponsGridInterface;
use App\Grids\CouponsGrid;
use App\Grids\ProductsGridInterface;
use App\Grids\ProductsGrid;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public $bindings = [
        ApplicationsRepositoryInterface::class => ApplicationsRepository::class,
        LeasesRepositoryInterface::class => LeasesRepository::class,
        PropertiesRepositoryInterface::class => PropertiesRepository::class,
        UserRepositoryInterface::class => UserRepository::class,
        UniqueLinkRepositoryInterface::class => UniqueLinkRepository::class,
        UnitRepositoryInterface::class => UnitRepository::class,
        UniqueLinkServiceInterface::class => UniqueLinkService::class,
        StripeServiceInterface::class => StripeService::class,
        StripeSubscriptionServiceInterface::class => StripeSubscriptionService::class,
        UsersGridInterface::class => UsersGrid::class,
        CouponsGridInterface::class => CouponsGrid::class,
        ProductsGridInterface::class => ProductsGrid::class
    ];

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
        view()->composer('*', function($view) {
            $view->with('states', \App\Models\State::get());
            $view->with('plans', \App\Models\SubscriptionPlan::get());
            $view->with('addons', \App\Models\Addon::get());
            $view->with('subscriptionOptions', \App\Models\SubscriptionOption::get());
        });
        //
        Validator::extend('greater_than_field', function($attribute, $value, $parameters, $validator) {
            $min_field = $parameters[0];
            $data = $validator->getData();
            $min_value = $data[$min_field];
            return $value >= $min_value;
        });

        Validator::replacer('greater_than_field', function($message, $attribute, $rule, $parameters) {
            return "This field must greater than " . preg_replace('/_|\.|-/', ' ', $parameters[0]) . " field";
        });

        Validator::extend('unique_tenant', function($attribute, $value, $parameters, $validator) {
            $query = $usersByRoles = User::where('email', $value);

            $users = $query->get();
            if (!$users->count())  {
                return true;
            } else {
                $usersByRoles = $query->whereHas('roles', function($q) {
                    $q->where('name', 'Tenant');
                })->get();
                if ($usersByRoles->count()) return true;
            }
            return false;
        });

        Validator::replacer('unique_tenant', function($message, $attribute, $rule, $parameters) {
            return "This email can't be used. Please use different email.";
        });

        Validator::extend('has_role', function($attribute, $value, $parameters, $validator) {
            $query = $usersByRoles = User::where('email', $value);

            $role = ucfirst(reset($parameters));
            $users = $query->get();
            if (!$users->count())  {
                return true;
            } else {
                $usersByRoles = $query->whereHas('roles', function($q) use ($role) {
                    $q->where('name', $role);
                })->get();
            }

            if ($usersByRoles->count()) return true;

            return false;
        });

        Validator::replacer('has_role', function($message, $attribute, $rule, $parameters) {
            return "We can’t share your application with entered information. Please enter different email.";
        });

        Schema::defaultStringLength(191);
    }
}
