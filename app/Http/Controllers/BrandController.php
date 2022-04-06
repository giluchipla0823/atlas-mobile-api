<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{

    /**
     * Obtener las marcas disponibles mediante el id de campa.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $brands = Brand::where('parent_compound', $request->compoundId)->get();

        return response()->json(['error' => false, 'data' => $brands]);
    }
}
