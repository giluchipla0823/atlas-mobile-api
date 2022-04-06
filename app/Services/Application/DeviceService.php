<?php

namespace App\Services\Application;

use Exception;
use App\Models\User;
use App\Models\Device;
use App\Models\CompoundConfig;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use App\Repositories\Device\DeviceRepositoryInterface;

class DeviceService
{
    /**
     * @var DeviceRepositoryInterface
     */
    private $repository;

    public function __construct(DeviceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Registrar dispositivo.
     *
     * @param array $params
     * @return Device
     * @throws AuthorizationException
     * @throws Exception
     */
    public function store(array $params): Device
    {
        $user = User::where('admin_pin', $params['pin'])->first();

        if (!$user) {
            throw new Exception('El usuario no se encuentra registrado.', Response::HTTP_NOT_FOUND);
        }

        if($user->role_id !== 3) {
            throw new AuthorizationException('Unauthorized.');
        }

        $type = $params['type'];

        $prefix = $this->getPrefixDeviceByType($type, $user);

        $lastDeviceRegistered = Device::latest()->first()->id;

        $nextId = str_pad($lastDeviceRegistered + 1, 4, '0', STR_PAD_LEFT);

        return $this->repository->create([
            'name' => $prefix . $nextId,
            'uuid' => $params['uuid'],
            'type_id' => $type,
            'version' => '1.0.0'
        ]);
    }

    /**
     * @param string $uuid
     * @return bool
     */
    public function exists(string $uuid): bool {
        return $this->repository->exits($uuid);
    }

    /**
     * @param int $type
     * @param User $user
     * @return string|null
     */
    private function getPrefixDeviceByType(int $type, User $user): ?string
    {
        $parameter = null;

        switch ($type) {
            case 1:
                $parameter = 'prefix_pda';
                break;
            case 2:
                $parameter = 'prefix_mobile';
                break;
            case 3:
                $parameter = 'prefix_tablet';
                break;
        }

        if (!$parameter) {
            return 'DEF';
        }

        $config = CompoundConfig::where('compound_id', $user->parent_compound)
            ->where('parameter', $parameter)->first();

        return $config ? $config->value : 'DEF';
    }
}
