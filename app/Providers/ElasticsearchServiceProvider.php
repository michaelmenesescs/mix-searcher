<?php

declare(strict_types=1);

namespace App\Providers;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;

class ElasticsearchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function ($app) {
            $config = config('elasticsearch');
            
            return ClientBuilder::create()
                ->setHosts($config['hosts'])
                ->build();
        });

        // Also bind the old namespace for backward compatibility
        $this->app->singleton(\Elasticsearch\Client::class, function ($app) {
            return $app->make(Client::class);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
