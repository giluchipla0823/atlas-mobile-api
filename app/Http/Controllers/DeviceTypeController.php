<?php

namespace App\Http\Controllers;

use App\Models\CompoundConfig;
use App\Models\DeviceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceTypeController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $types = CompoundConfig::where('compound_id', $request->compoundId)
            ->where('parameter','device_type')
            ->get()->pluck('value')->all();

        $devices = DeviceType::whereIn('id', $types)->get();

        return response()->json($devices);
    }
}
