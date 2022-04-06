<?php

namespace App\Repositories\Device;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class DeviceRepository implements DeviceRepositoryInterface
{

    /**
     * @var Device
     */
    private $device;

    public function __construct(Device $device)
    {
        $this->device = $device;
    }

    public function all(Request $request)
    {
        // TODO: Implement all() method.
    }

    public function find(int $id): Model
    {
        // TODO: Implement find() method.
    }

    public function create(array $params): Model
    {
        return $this->device->create($params);
    }

    public function update(array $params, int $id): Model
    {
        // TODO: Implement update() method.
    }

    public function delete(int $id): void
    {
        // TODO: Implement delete() method.
    }

    public function exits(string $uuid): bool
    {
        return $this->device->where('uuid', $uuid)->exists();
    }
}
