<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Schema::defaultStringLength(191);
        
        // 主题 https://jenil.github.io/bulmaswatch
        View::share('_theme', 'materia');
        // 颜色
        View::share('_color', 'primary');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
