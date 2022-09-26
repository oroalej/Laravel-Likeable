<?php

namespace Oroalej\Likeable;

use Illuminate\Support\ServiceProvider;

class LikeableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->offerPublishing();
    }

    public function offerPublishing(): void
    {
        $this->publishes([
            __DIR__ . '/../database/migrations/create_likeable_tables.php' => $this->getMigrationFileName('create_likeable_tables.php'),
        ], 'migrations');
    }

    public function getMigrationFileName($migrationFileName): string
    {
        $path              = database_path() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;
        $migrationFileName = date('Y_m_d_His') . '_' . $migrationFileName;

        return $path . $migrationFileName;
    }
}
