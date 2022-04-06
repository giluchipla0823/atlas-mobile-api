<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Design;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ColorController extends Controller
{

    /**
     * Obtener colores de un modelo específico.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request): JsonResponse
    {
        $model = Design::find($request->modelId);

        if (!$model) {
            throw new Exception(
                "No se encuentra información del modelo con id {$request->modelId}.",
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json(['error' => false, 'data' => $model->colors]);
    }
}
