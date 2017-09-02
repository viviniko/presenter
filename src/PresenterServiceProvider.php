<?php

namespace Viviniko\Presenter;

use Viviniko\Presenter\Decorators\ArrayDecorator;
use Viviniko\Presenter\Decorators\AtomDecorator;
use Viviniko\Presenter\Decorators\PaginatorDecorator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\ServiceProvider;

class PresenterServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
            __DIR__.'/../config/presenter.php' => config_path('presenter.php'),
        ]);

        view()->composer('*', function ($view) {
            if ($view instanceof View) {
                if ($viewData = array_merge($view->getFactory()->getShared(), $view->getData())) {
                    foreach ($viewData as $key => $value) {
                        $view[$key] = $this->app['presenter']->decorate($value);
                    }
                }
            }
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/presenter.php', 'presenter');

        $this->app->singleton('presenter', function ($app) {
            $autoPresenter = new AutoPresenter($app['config']->get('presenter.presenters', []));

            $autoPresenter->pushDecorator(new AtomDecorator($autoPresenter, $app));
            $autoPresenter->pushDecorator(new ArrayDecorator($autoPresenter));
            $autoPresenter->pushDecorator(new PaginatorDecorator($autoPresenter));

            return $autoPresenter;
        });

        $this->app->alias('presenter', AutoPresenter::class);
    }
}
