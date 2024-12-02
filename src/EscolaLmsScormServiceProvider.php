<?php

namespace EscolaLms\Scorm;

use EscolaLms\Scorm\Repositories\Contracts\ScormRepositoryContract;
use EscolaLms\Scorm\Repositories\ScormRepository;
use EscolaLms\Scorm\Services\Contracts\ScormQueryServiceContract;
use EscolaLms\Scorm\Services\Contracts\ScormServiceContract;
use EscolaLms\Scorm\Services\Contracts\ScormTrackServiceContract;
use EscolaLms\Scorm\Services\ScormQueryService;
use EscolaLms\Scorm\Services\ScormService;

use EscolaLms\Scorm\Services\ScormTrackService;
use Illuminate\Support\ServiceProvider;

/**
 * SWAGGER_VERSION
 */
class EscolaLmsScormServiceProvider extends ServiceProvider
{
    public $singletons = [
        ScormServiceContract::class => ScormService::class,
        ScormQueryServiceContract::class => ScormQueryService::class,
        ScormTrackServiceContract::class => ScormTrackService::class,
        ScormRepositoryContract::class => ScormRepository::class,
    ];

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'scorm');
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/scorm'),
        ]);
        $this->loadConfig();
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'scorm');
    }

    private function loadConfig(): void
    {
        $this->publishes([
            __DIR__ . '/config.php' => config_path('escolalms/core.php'),
        ], 'escolalms');

        $this->mergeConfigFrom(
            __DIR__ . '/config.php',
            'escolalms.core'
        );

        $config = $this->app->make('config');
        $config->set('auth.guards', array_merge(
            [
                'api' => [
                    'driver' => 'passport',
                    'provider' => 'users',
                ],
            ],
            $config->get('auth.guards', [])
        ));
    }

    public function register(): void
    {
        $this->app->register(AuthServiceProvider::class);
    }
}
