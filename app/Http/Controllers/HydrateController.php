<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vehicle\VehicleVinInfoRequest;
use Exception;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Compound;
use App\Models\Position;
use App\Models\Rule;
use App\Models\CompoundConfig;
use App\Models\DeviceType;
use App\Models\Brand;
use App\Models\Design;
use App\Models\Country;

class HydrateController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listCompounds(Request $request): JsonResponse
    {
        return response()->json(Compound::get());
    }

    /**
     * List Pairs.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listPairs(Request $request): JsonResponse
    {
        $response = [];

        $pages = Compound::find($request->compoundId)->pages;

        foreach ($pages as $page) {
            $pairs = [];
            $tiles = $page->tiles->sortBy('order');
            $chunks = $tiles->chunk(2);

            foreach ($chunks as $chunk) {
                $pairs[] = $chunk->values();
            }

            $response[$page->name] = $pairs;
        }

        return response()->json($response);
    }

    /**
     * Información completa del vehículo.
     *
     * @param VehicleVinInfoRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function vinInfo(VehicleVinInfoRequest $request): JsonResponse
    {
        // $vehicle = Vehicle::where('vin', $request->vin)->orWhere('vin_short', $request->vin)->first();
        $vin = $request->vin;

        $vehicle = Vehicle::with(['position', 'position.parent'])
            ->where('vin', $vin)
            ->orWhere('vin_short', $vin)->first();

        if (!$vehicle) {
            return response()->json([
                'error' => true,
                'message' => 'No se encontró información del vehículo con el VIN especificado.'
            ]);
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

        return response()->json($response);
    }

//    /**
//     * Obtener información de la fila.
//     *
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function rowInfo(Request $request): JsonResponse {
//        $rowLp = $request->rowLp;
//
//        $position = Position::where(function ($query) use ($rowLp) {
//            $query->where('lp', '=' , $rowLp)
//                ->orWhere('alt_qr', '=', $rowLp);
//        })->first();
//
//        if(!$position){
//            return response()->json(['error' => true, 'message' => 'This position does not exist']);
//        }
//
//        $position->state = (int) $position->active === 1;
//
//        return response()->json(['error' => false, 'data' => $position]);
//    }

//    /**
//     * Búsqueda de Vins o categorías.
//     *
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function searchList(Request $request): JsonResponse
//    {
////        $categories = Rule::where([
////            'active' => 1,
////            'parent_compound' => $request->compoundId
////        ])->orderBy('name')->get();
//
////        $vehicles = Vehicle::whereBetween('state_id', [State::STATE_ID_ON_TERMINAL, 4])
////            ->orderByDesc('dt_onterminal')->get();
//
//        // return response()->json(['list' => $categories->concat($vehicles)]);
//        // return response()->json(['list' => $vehicles]);
//
//
//        $vehicles = Vehicle::whereBetween('state_id', [State::STATE_ID_ON_TERMINAL, 4])
//            ->where('vin', 'LIKE', "%". $request->term ."%")
//            ->orderByDesc('dt_onterminal')
//            ->paginate(20);
//
//        return response()->json($vehicles);
//    }

    /**
     * Obtener filas disponibles por el nombre de la categoría (regla).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function categoryRows(Request $request): JsonResponse
    {
        $list = Position::where(['category' => $request->category, 'type' => 2])->get();

        $list = $list->map(function($row) {

            $split = explode('.', $row->lpname);
            $name = "$split[1].$split[2]";

            $text = "$name - $row->fill / $row->capacity";

            return [
                'row' => $row,
                'text' => $text,
                'icon' => 'custom-location'
            ];
        });

        return response()->json($list);
    }

//    /**
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function deviceTypes(Request $request): JsonResponse
//    {
//        $types = CompoundConfig::where('compound_id', $request->compoundId)
//                    ->where('parameter','device_type')
//                    ->get()->pluck('value')->all();
//
//        $devices = DeviceType::whereIn('id', $types)->get();
//
//        return response()->json($devices);
//    }

    //Basic Vehicle attributes

    /*
    public function brandsList(Request $request){
        try {
            return array('error'=>false, 'data'=>Brand::where('parent_compound',$request->compoundId)->get());
        } catch (\Throwable $th) {
            return array('error'=>true,'message'=>$th);
        }
    }
    */

    /*
    public function modelsList(Request $request){
        try {
            return array('error'=>false, 'data'=>Brand::find($request->brandId)->designs);
        } catch (\Throwable $th) {
            return array('error'=>true,'message'=>$th);
        }
    }
    */

    /*
    public function colorsList(Request $request){
        try {
            return array('error'=>false, 'data'=>Design::find($request->modelId)->colors);
        } catch (\Throwable $th) {
            return array('error'=>true,'message'=>$th);
        }
    }
    */

    /*
    public function countriesList(Request $request){

        try {
            return array('error'=>false, 'data'=>Country::get());
        } catch (\Throwable $th) {
            return array('error'=>true,'message'=>$th);
        }
    }
    */

    public function dcodeList(Request $request){
        try {
            return array('error'=>false, 'data'=>Country::find($request->countryId)->destinationCodes);
        } catch (\Throwable $th) {
            return array('error'=>true,'message'=>$th);
        }
    }

    public function designSvg(Request $request){
        return array('error'=>false, 'data'=>Design::find($request->designId)->svg);
    }

    public function getPosition(Request $request){
        return array('error'=>false, 'data'=>Position::find($request->positionId));
    }


    public function getDirectPositions(Request $request): JsonResponse
    {
        $positions = Position::where('parent_compound', $request->compoundId)->where('direct_position', 1)->get();
        $pair = [];
        $pairs = [];
        $countPositions = count($positions);

        for ($i = 1; $i <= $countPositions ; $i++) {

            $pair[] = $positions[$i - 1];

            if($i % 3 == 0 || ($i % 3 != 0 && $i == $countPositions)){
                $pairs[] = $pair;
                $pair = [];
            }
        }

        return response()->json(['error' => false, 'data' => $pairs]);
    }
}
