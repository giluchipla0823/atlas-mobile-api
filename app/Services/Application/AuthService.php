<?php

namespace App\Services\Application;

use App\Models\Device;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
    private const TOKEN_SECRET = 'access';

    /**
     * Login.
     *
     * @param Request $request
     * @return User
     * @throws Exception
     */
    public function login(Request $request): User {
        $device = Device::where('uuid', $request->uuid)->first();

        if(!$device){
            throw new Exception('', Response::HTTP_FORBIDDEN);
        }

        $user = User::where('username', $request->name)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new AuthenticationException('Incorrect access credentials.');
        };

        // $check = Carbon::now()->greaterThan(Carbon::parse(new \Datetime($user->change_pass)));

        $check = false;

        if($check){
            throw new Exception('', Response::HTTP_PRECONDITION_REQUIRED);
        }

        /*
         * TODO: Comprobación para verificar que un usuario tiene
         * permisos para acceder a la campa seleccionada.
         * Como sugerencia se propone poder permitir que el usuario
         * haga login y al entrar se le muestre las campas a las que
         * tiene permiso.
         */

        $user->login($request->compoundId);

        $token = $user->createToken(self::TOKEN_SECRET);
        $user->auth_token = $token->plainTextToken;
        $user->device_id = $device->id;
        $user->save();
        $user->fresh();

        $user->token = $token->plainTextToken;
        $user->error =  false;
        $user->device = $device;

        return $user;
    }

    /**
     * Cambiar contraseña.
     *
     * @param Request $request
     * @throws Exception
     */
    public function resetPassword(Request $request): void
    {
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->originalPass, $user->password)) {
            throw new Exception('Password is incorrect.', Response::HTTP_BAD_REQUEST);
        }

        $user->password = Hash::make($request->newPass, ['rounds'=> 4]);
        $user->change_pass = Carbon::now()->addMonths(3);
        $user->save();
    }
}
