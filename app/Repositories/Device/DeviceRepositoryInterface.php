<?php

namespace App\Repositories\Device;

use App\Repositories\BaseRepositoryInterface;

interface DeviceRepositoryInterface extends BaseRepositoryInterface
{
    public function exits(string $uuid): bool;
}
