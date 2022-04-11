<?php

namespace App\Repositories\Vehicle;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;

interface VehicleRepositoryInterface extends BaseRepositoryInterface
{
    public function datatables(Request $request);
}
