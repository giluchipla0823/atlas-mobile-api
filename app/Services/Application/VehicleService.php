<?php

namespace App\Services\Application;

use App\Models\State;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Vehicle\VehicleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VehicleService
{
    /**
     * @var VehicleRepositoryInterface
     */
    private $repository;

    public function __construct(VehicleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @return Collection|LengthAwarePaginator
     */
    public function findAll(Request $request)
    {
        return $this->repository->all($request);
    }

    /**
     * Registrar vehículo.
     *
     * @param array $params
     * @return Vehicle
     */
    public function store(array $params): Vehicle
    {
        $data = [
            'vin' => $params['vin'],
            'vin_short' => substr($params['vin'], -6),
            'country_id' => $params['countryId'],
            'design_id' => $params['modelId'],
            'color_id' => $params['colorId'],
            'position_id' => $params['positionId'],
            'parent_compound' => $params['compoundId'],
            'state_id' => State::STATE_ID_ON_TERMINAL,
            'eoc' => '',
            'last_rule_id' => 7,
            'shipping_rule_id' => 7,
        ];

        return $this->repository->create($data);
    }
}
