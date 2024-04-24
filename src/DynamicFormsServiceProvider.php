<?php

namespace Vediansoft\LaravelDynamicForms;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class DynamicFormsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(__DIR__ . "/config/dynamicforms.php", 'dynamicforms');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Path of directory where this file is located within
        $dir = __DIR__;
        // Load views
        $this->loadViewsFrom("{$dir}/views", 'dynamicforms');
        // Add functionality to publish said items for custom configuration
        $this->publishes([
            "{$dir}/views" => resource_path('views/vediansoft/dynamicforms'),
            "{$dir}/config/dynamicforms.php" => config_path('dynamicforms.php'),
        ], 'dynamic-forms/all');
        // Add functionality to publish said items for custom configuration
        $this->publishes([
            "{$dir}/views" => resource_path('views/vediansoft/dynamicforms'),
        ], 'dynamic-forms/view');
        // Add functionality to publish said items for custom configuration
        $this->publishes([
            "{$dir}/config/dynamicforms.php" => config_path('dynamicforms.php'),
        ], 'dynamic-forms/config');

        // Setup casses variable 
        View::composer('dynamicforms::*', function($view) {
            // Return view with dynmicforms
            return  $view->with('dynamicforms', [
                // css classes
                'css' => config('dynamicforms.css')
            ]);
        });
    }
}
