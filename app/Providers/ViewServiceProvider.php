<?php

namespace App\Providers;

use App\Menu\Menu;
use App\View\Composers\NavigationComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
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
        View::composer('*', function(\Illuminate\View\View $view){
            $menu = [
                [
                    'title' => 'Users',
                    'url' => route('home'),
                ],
                [
                    'title' => 'Actions',
                    'url' => route('actions'),
                ],
                [
                    'title' => 'Transactions',
                    'url' => route('transactions'),
                ],
                [
                    'title' => 'Logs',
                    'url' => route('logs'),
                ],
            ];

            $view->with('menu', Menu::make($menu));
        });
    }
}
