<?php

namespace App\Traits;

use Exception;
use App\Helpers\ApiHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\Helpers\QueryParamsHelper;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    use TransformResourceResponse;

    /**
     * Crear formato de respuesta JSON para escenarios de éxito.
     *
     * @param array|object $data
     * @param string $message
     * @param int $code
     * @param array $extras
     * @return JsonResponse
     */
    protected function successResponse(
        $data,
        string $message = ApiHelper::MSG_SUCCESSFUL_OPERATION,
        int $code = Response::HTTP_OK,
        array $extras = []
    ): JsonResponse
    {
        return $this->makeResponse($data, $message, $code, $extras);
    }

    /**
     * Crear formato de respuesta JSON para escenarios de error.
     *
     * @param string $message
     * @param int $code
     * @param array $extras
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $code, array $extras = []): JsonResponse
    {
        return $this->makeResponse(null, $message, $code, $extras);
    }

    /**
     * Crear respuesta JSON para mostrar solo la propiedad "message".
     *
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function showMessage(string $message, int $code = Response::HTTP_OK): JsonResponse
    {
        return $this->makeResponse(null, $message, $code);
    }

    /**
     * Crear formato de respuesta JSON para cuando se usa "Collections".
     *
     * @param Collection $data
     * @return JsonResponse
     * @throws Exception
     */
    protected function showAll(Collection $data): JsonResponse
    {
        if ($data->isEmpty()) {
            return $this->successResponse([]);
        }

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            return $this->successResponse(
                null,
                ApiHelper::MSG_SUCCESSFUL_OPERATION,
                Response::HTTP_OK,
                $data->toArray()
            );
        }

        $data = $this->transformCollection($data);

        return $this->successResponse($data);
    }

    /**
     * Crear formato de respuesta JSON para cuando se recibe un "Eloquent Model".
     *
     * @param Model|null $instance
     * @return JsonResponse
     */
    protected function showOne(?Model $instance): JsonResponse
    {
        $result = $this->transformInstance($instance);

        return $this->successResponse($result);
    }

    /**
     * Retorna en formato JSON la estructura de respuestas definida para la API.
     *
     * @param array|object|null $data
     * @param string $message
     * @param int $code
     * @param array $extras
     * @return JsonResponse
     */
    protected function makeResponse($data, string $message, int $code, array $extras = []): JsonResponse
    {
        $response = ApiHelper::response($data, $message, $code, $extras);

        return response()->json($response, $code);
    }
}
