<?php

namespace App\Repositories\Vehicle;

use App\Models\State;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class VehicleRepository implements VehicleRepositoryInterface
{
    /**
     * @var Vehicle
     */
    private $vehicle;

    public function __construct(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function all(Request $request)
    {
        return $this->vehicle->whereBetween('state_id', [State::STATE_ID_ON_TERMINAL, 4])
            ->where('vin', 'LIKE', "%". $request->vin ."%")
            ->orderByDesc('dt_onterminal')
            ->paginate(20);
    }

    public function find(int $id): Model
    {
        // TODO: Implement find() method.
    }

    public function create(array $params): Model
    {
        return $this->vehicle->create($params);
    }

    public function update(array $params, int $id): Model
    {
        // TODO: Implement update() method.
    }

    public function delete(int $id): void
    {
        // TODO: Implement delete() method.
    }
}
