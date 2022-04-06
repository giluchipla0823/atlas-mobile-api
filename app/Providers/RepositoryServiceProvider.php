<?php

namespace App\Providers;

use App\Repositories\Device\DeviceRepository;
use App\Repositories\Device\DeviceRepositoryInterface;
use App\Repositories\Rule\RuleRepository;
use App\Repositories\Rule\RuleRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Vehicle\VehicleRepository;
use App\Repositories\Vehicle\VehicleRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    public function boot() {

    }

    public function register(): void
    {
        $this->app->bind(
            VehicleRepositoryInterface::class,
            VehicleRepository::class
        );

        $this->app->bind(
            DeviceRepositoryInterface::class,
            DeviceRepository::class
        );

        $this->app->bind(
            RuleRepositoryInterface::class,
            RuleRepository::class
        );
    }
}
