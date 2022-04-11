<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Exception;
use Carbon\Carbon;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Application\VehicleService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Vehicle\VehicleStoreRequest;

class VehicleController extends Controller
{
    /**
     * @var VehicleService
     */
    private $vehicleService;

    public function __construct(
        VehicleService $vehicleService
    ){
        $this->vehicleService = $vehicleService;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $vehicles = $this->vehicleService->findAll($request);

        return response()->json($vehicles);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function datatables(Request $request): JsonResponse
    {
        $vehicles = $this->vehicleService->datatables($request);

        return response()->json($vehicles);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function findAll(Request $request): JsonResponse
    {
        $vehicles = $this->vehicleService->findAll($request);

        return response()->json($vehicles);
    }

    /**
     * Registrar vehículo.
     *
     * @param VehicleStoreRequest $request
     * @return JsonResponse
     */
    public function store(VehicleStoreRequest $request): JsonResponse
    {
        $vehicle = $this->vehicleService->store($request->all());

        return response()->json([
            'error' => false,
            'message' => 'Vehículo registrado correctamente.',
            'vehicle' => $vehicle
        ]);
    }

    /**
     * Información completa del vehículo.
     *
     * @param string $vin
     * @return JsonResponse
     * @throws Exception
     */
    public function vinInfo(string $vin): JsonResponse
    {
        $vehicle = Vehicle::with(['position', 'position.parent'])
                ->where('vin', $vin)
                ->orWhere('vin_short', $vin)->first();

        if (!$vehicle) {
            throw new Exception(
                'No se encontró información del vehículo con el VIN especificado.',
                Response::HTTP_NOT_FOUND
            );
        }

        $response = [
            'error' => false,
            'vehicle' => $vehicle,
            'parsed' => $vehicle->info()
        ];

        if($vehicle->dt_onterminal){
            // Dwell time
            $dtOTCarbon = new Carbon(new \DateTime($vehicle->dt_onterminal));
            $dwell = $dtOTCarbon->diffInDays(Carbon::now());

            $response['dtButtons'] = [
                [
                    'text' => "On Terminal $vehicle->dt_onterminal",
                    'icon' => 'calendar-outline'
                ],
                [
                    'text' => "Dwell Time $dwell",
                    'icon' => 'hourglass-outline'
                ]
            ];
        }

        if(count($vehicle->holds) > 0) {
            $response['holdButtons'] = [];

            foreach ($vehicle->holds as $hold) {
                $response['holdButtons'][] = [
                    'text' => $hold->name,
                    'icon'=>'lock-closed-outline'
                ];
            }
        }

        if ($vehicle->position->type === Position::TYPE_SLOT) {
            $rowPosition = $vehicle->position->parent()->first();

             /* @var Collection $slots */
            $slots = $rowPosition->children()->with(['vehicle'])->get();

            $usedSlots = $slots->filter(function($slot) {
                 return !is_null($slot->vehicle);
            });

            $lastSlotUsed = $usedSlots->last();

            $lastVehicleInRow = $lastSlotUsed->vehicle()->with(['color', 'design'])->first();
            $lastVehicleInRow->row_position = $rowPosition;
            $lastVehicleInRow->slot_number = count($usedSlots);

            $response['last_vehicle_in_row'] = $lastVehicleInRow;
        }

        return response()->json($response);
    }
}
