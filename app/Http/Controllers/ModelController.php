<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModelController extends Controller
{

    /**
     * Obtener modelos por id de marca.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request): JsonResponse
    {
        $brand = Brand::find($request->brandId);

        if (!$brand) {
            throw new Exception(
                "No se encuentra información de la marca con id {$request->brandId}.",
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json(['error' => false, 'data' => $brand->designs]);
    }
}
