<?php

namespace Sahakavatar\Cms\Providers;
use Illuminate\Support\ServiceProvider;


class CmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/Lang', 'cms');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'cms');

        $this->app->register("Sahakavatar\User\Providers\ModuleServiceProvider");
        $this->app->register('Sahakavatar\Console\Providers\ModuleServiceProvider');
        $this->app->register('Sahakavatar\Framework\Providers\ModuleServiceProvider');
        $this->app->register('Sahakavatar\Manage\Providers\ModuleServiceProvider');
        $this->app->register('Sahakavatar\Resources\Providers\ModuleServiceProvider');
        $this->app->register('Sahakavatar\Settings\Providers\ModuleServiceProvider');
        $this->app->register('Sahakavatar\Uploads\Providers\ModuleServiceProvider');
        $this->app->register('Sahakavatar\Modules\Providers\ModuleServiceProvider');
        $this->app->register('Avatar\Avatar\Providers\AvatarServiceProvider');
    }
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/../standards/constants.php';
        $this->app->register(EventyServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

}
