<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

trait CreatesApplication
{

    protected static $setUpHasRunOnce = false;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->loadEnvironmentFrom('.env.testing');

        $app->make(Kernel::class)->bootstrap();

        $conn = Storage::disk('database');
        if (!$conn->exists('database.sqlite')) {
            $conn->put('database.sqlite', null);
        }

        if (!static::$setUpHasRunOnce) {
            Artisan::call('migrate:fresh --seed');
            static::$setUpHasRunOnce = true;
        }

        return $app;
    }
}
