<?php

namespace MrProperter\Providers;

use Illuminate\Support\ServiceProvider;
use MrProperter\Console\Commands\MakeAll;
use MrProperter\Console\Commands\MakeMigration;
use MrProperter\Console\Commands\MakeDoc;
use MrProperter\Console\Commands\MakeModel;
use MrProperter\View\Components\MrpForm;

class MrProperterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {


    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeDoc::class,
                MakeModel::class,
                MakeMigration::class,
                MakeAll::class,
            ]);
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'mrproperter');

        $this->loadViewComponentsAs('', [
            MrpForm::class,
        ]);

        $migrations_path = __DIR__ . '/../copy/migrations';
        if (file_exists($migrations_path)) {
            $this->publishes([
                $migrations_path => database_path('migrations'),
            ], 'public');
        }

        $migrations_path = __DIR__ . '/../copy/Controllers';
        if (file_exists($migrations_path)) {
            $this->publishes([
                $migrations_path => app_path('Http/Controllers/MrProperter'),
            ], 'public');
        }

        $migrations_path = __DIR__ . '/../copy/views';
        if (file_exists($migrations_path)) {
            $this->publishes([
                $migrations_path => resource_path('views/mrproperter'),
            ], 'public');
        }

        $migrations_path = __DIR__ . '/../copy/Models';
        if (file_exists($migrations_path)) {
            $this->publishes([
                $migrations_path => app_path('Models'),
            ], 'public');
        }


        $js_path = __DIR__ . '/../copy/js';
        if (file_exists($js_path)) {
            $this->publishes([
                $js_path => public_path('js'),
            ], 'public');
        }

        /*
        $this->publishes([
            __DIR__ . '/../copy/Controllers/MrProperter' => app_path('Http/Controllers'),
        ], 'public');
*/

    }
}
