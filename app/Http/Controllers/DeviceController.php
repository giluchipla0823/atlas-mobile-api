<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use App\Services\Application\DeviceService;
use App\Http\Requests\Device\DeviceStoreRequest;
use Symfony\Component\HttpFoundation\Response;

class DeviceController extends Controller
{
    /**
     * @var DeviceService
     */
    private $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    /**
     * Registrar dispositivo.
     *
     * @param DeviceStoreRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Exception
     */
    public function store(DeviceStoreRequest $request): JsonResponse
    {
        if (Device::where('uuid', $request->get('uuid'))->first()) {
            throw new Exception(
                'El dispositivo ya se encuentra registrado.',
                Response::HTTP_BAD_REQUEST
            );
        }

        $device = $this->deviceService->store($request->all());

        return response()->json([
            'error' => false,
            'message' => 'El dispositivo se ha registrado correctamente.',
            'data' => $device
        ]);
    }

    /**
     * @param string $uuid
     * @return JsonResponse
     */
    public function exists(string $uuid): JsonResponse
    {
        $status = $this->deviceService->exists($uuid);

        return response()->json(['status' => $status]);
    }
}
