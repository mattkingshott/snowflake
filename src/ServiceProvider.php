<?php declare(strict_types=1);

namespace Snowflake;

use Godruoyi\Snowflake\Snowflake;
use Godruoyi\Snowflake\RandomSequenceResolver;
use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{
    /**
     * Bootstrap any package services.
     *
     */
    public function boot() : void
    {
        $this->publishes([__DIR__ . '/../config/snowflake.php' => config_path('snowflake.php')]);
    }

    /**
     * Register any package services.
     *
     */
    public function register() : void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/snowflake.php', 'snowflake');

        $this->app->singleton('snowflake', fn() => $this->singleton());
    }

    /**
     * Register the Snowflake singleton service.
     *
     */
    protected function singleton() : Snowflake
    {
        $service = new Snowflake(
            config('snowflake.data_center', 1),
            config('snowflake.worker_node', 1)
        );

        $timestamp = strtotime(config('snowflake.start_timestamp', '2022-01-01')) * 1000;
        $resolver  = config('snowflake.sequence_resolver', RandomSequenceResolver::class);

        return $service
            ->setStartTimeStamp($timestamp)
            ->setSequenceResolver(new $resolver());
    }
}