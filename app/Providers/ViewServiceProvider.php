<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('include.admin._right_navbar', function($view) {
            if(auth()->check()){
                $notifications = auth()->user()->unreadNotifications->where('read_at',NULL);
                $view->with(['notifications'=>$notifications]);
            }
        });

    }
}
