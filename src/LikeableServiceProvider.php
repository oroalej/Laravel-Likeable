<?php

namespace Oroalej\Likeable;

use Illuminate\Support\ServiceProvider;

class LikeableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__."/../database/migrations");
    }
}
