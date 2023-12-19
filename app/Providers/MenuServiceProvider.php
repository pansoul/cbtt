<?php

namespace App\Providers;

use App\Menu\Menu;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
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
        $menu = [
            [
                'title' => 'Главная',
                'url' => route('home'),
            ],
            [
                'title' => 'Каталог товаров',
                'url' => route('users'),
            ],
            [
                'title' => 'Корзина',
                'url' => route('transactions'),
            ],
        ];

        View::share('menu', Menu::make($menu));
    }
}
