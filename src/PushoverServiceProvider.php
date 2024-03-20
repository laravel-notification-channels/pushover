<?php

namespace NotificationChannels\Pushover;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\ServiceProvider;

class PushoverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->app->when(PushoverChannel::class)
            ->needs(Pushover::class)
            ->give(function () {
                return new Pushover(new HttpClient(), config('services.pushover.token'));
            });
    }
}
