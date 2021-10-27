<?php

namespace App\Providers;

use App\Contracts\Services\ImageServiceInterface;
use App\Services\ImageService;
use Illuminate\Support\ServiceProvider;

class ImageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(ImageServiceInterface::class, ImageService::class);
    }
}
