<?php

namespace Oroalej\Likeable;

use Illuminate\Support\ServiceProvider;

class LikeableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__."/../database/migrations");
    }

    public function getMigrationFileName($migrationFileName): string
    {
        $path              = database_path() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;
        $migrationFileName = date('Y_m_d_His') . '_' . $migrationFileName;

        return $path . $migrationFileName;
    }
}
