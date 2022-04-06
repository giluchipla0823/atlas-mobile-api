<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Exception;
use App\Services\Application\AuthService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Device;
use App\Models\Message;
use App\Models\CompoundConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * @var AuthService
     */
    private $authService;

    public function __construct(
        AuthService $authService
    ) {
        $this->authService = $authService;
    }

    public function test() {
        return response()->json([
            'environment' => env('APP_ENVIRONMENT'),
            'app_name' => env('APP_NAME'),
            'url' => env('APP_URL'),
            'db_host' => env('DB_HOST'),
        ]);
    }

    /**
     * Login.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    // public function login(Request $request): JsonResponse {
    public function login(LoginRequest $request): JsonResponse {


        $user = $this->authService->login($request);

        return response()->json($user);
    }

    public function logout(Request $request){
        User::find($request->id)->logout();
        return array('result'=>true);
    }

    public function register(Request $request){
        $user = new User;

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        $user->save();

        return response()->createUser();
    }

    /**
     * Cambiar contraseña.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function resetPassword(Request $request): JsonResponse
    {

        /*
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->originalPass, $user->password)) {
            // array('error' => true, 'message' => 'Password is incorrect', 'code' => 401);
            throw new Exception('Password is incorrect.', Response::HTTP_BAD_REQUEST);
        }

        $user->password = Hash::make($request->newPass, ['rounds'=> 4]);
        $user->change_pass = Carbon::now()->addMonths(3);
        $user->save();

        // return array('error' => false);
        */

        $this->authService->resetPassword($request);

        return response()->json(['error' => true]);


        /*
        if($user && Hash::check($request->originalPass, $user->password)){
            $user->password = Hash::make($request->newPass,['rounds'=> 4]);
            $user->change_pass = Carbon::now()->addMonths(3);
            return array('error' => !$user->save());
        }
        else
            return array('error'=>true, 'message' => 'Password is incorrect', 'code' => 401);

        */

    }

    public function readMessage(Request $request){

        $messageIn = $request->message;

        $dt = new Carbon($messageIn->dt);

        $message = Message::where(['emitter'=>$messageIn->from,'dt'=>$dt])->first();

        $message->seen = 1;

        $message->save();
    }
}
