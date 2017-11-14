<?php

namespace Btybug\btybug\Providers;

use Illuminate\Support\ServiceProvider;


class BtybugServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/Lang', 'btybug');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'btybug');

        $this->app->register("Btybug\User\Providers\ModuleServiceProvider");
        $this->app->register('Btybug\Console\Providers\ModuleServiceProvider');
        $this->app->register('Btybug\Framework\Providers\ModuleServiceProvider');
        $this->app->register('Btybug\Manage\Providers\ModuleServiceProvider');
        $this->app->register('Btybug\Resources\Providers\ModuleServiceProvider');
        $this->app->register('Btybug\Settings\Providers\ModuleServiceProvider');
        $this->app->register('Btybug\Uploads\Providers\ModuleServiceProvider');
        $this->app->register('Btybug\Modules\Providers\ModuleServiceProvider');
        $this->app->register('Btybug\Studios\Providers\ModuleServiceProvider');
        $this->app->register('Avatar\Avatar\Providers\AvatarServiceProvider');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__ . '/../standards/constants.php';
        $this->app->register(EventyServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

}