<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditUserRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Http\Middleware\Check;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\RegisterRequest;
use \Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Token;

class AuthController extends Controller
{
    protected $userService;

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    /**
     * @OA\Post(
     *     path="register",
     *     summary="Register new user.",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         description="name of the new user",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         description="email of the new user",
     *         (type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         description="password of the new user",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="c_password",
     *         in="query",
     *         required=true,
     *         description="confirmation of user's password",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="User registered successfully"),
     *     @OA\Response(response="404", description="Validation error")
     *
     * )
     * @param RegisterRequest $request
     * @return JsonResponse
     */

    public function register(RegisterRequest $request): JsonResponse {
        $this->userService->createUser($request->all());
        return $this->login($request);
    }

    /**
     * @OA\Post(
     *     path="login",
     *     summary="Login user.",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         description="email of the user",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         description="password of the user",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Access token"),
     *     @OA\Response(response="401", description="Unathorized")
     * )
     * @param Request $request
     * @return JsonResponse
     */

    public function login(Request $request): JsonResponse {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        if (Auth::validate(['email'=>$credentials['email'], 'password'=>$credentials['password']])) {
            $tokens = $this->userService->generateTokens($credentials);
            if($tokens){
                return response()->json([
                    'success' => true,
                    'access_token' => $tokens['access'],
                    'refresh_token' => $tokens['refresh'],
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
        ], 401);
    }

    /**
     * @OA\Delete(
     *     path="logout",
     *     summary="Logout the current user.",
     *     @OA\Parameter(
     *         name="Bearer Token",
     *         in="header",
     *         required=true,
     *         description="refresh token of the user",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name='token',
     *         in="body",
     *         required=true,
     *         description="access token of the user",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="User successfully logged out"),
     *     @OA\Response(response="401", description="Unathorized")
     * )
     * @param Request $request
     * @return JsonResponse
     */

    public function logout(Request $request): JsonResponse {
        try {
            auth()->logout();
            $accessToken = $request->token;
            auth()->setToken($accessToken)->logout();
            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="refresh",
     *     summary="Refresh user's tokens.",
     *     @OA\Parameter(
     *         name="Bearer Token",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Access and refresh tokens"),
     *     @OA\Response(response="401", description="Unathorized")
     * )
     * @param Request $request
     * @return JsonResponse
     */

    public function refresh(Request $request): JsonResponse {
        $user = auth()->user();
        if($user !== null && auth()->payload()->get('type') === 'refresh') {
            $data = [
                'email' => $user->email
            ];
            auth()->invalidate(true);
            $tokens = $this->userService->generateTokens($data);
            if ($tokens) {
                return response()->json([
                    'success' => true,
                    'access_token' => $tokens['access'],
                    'refresh_token' => $tokens['refresh']
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 401);

    }

    /**
     * @OA\Get(
     *     path="user",
     *     summary="Get current user's personal informaiton.",
     *     @OA\Parameter(
     *         name="Bearer Token",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         description="name of the user",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="about",
     *         in="query",
     *         required=false,
     *         description="description of the user",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="User personal info"),
     *     @OA\Response(response="401", description="Unathorized")
     * )
     * @param Request $request
     * @return JsonResponse
     */

    public function getUser(Request $request): JsonResponse {
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'name' => $user->name,
            'about' => $user->about
        ]);
    }

    /**
     * @OA\Post(
     *     path="user",
     *     summary="Change user's personal information.",
     *     @OA\Parameter(
     *         name="Bearer Token",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         description="name of the user",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="about",
     *         in="query",
     *         required=false,
     *         description="description of the user",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="User's changed personal info"),
     *     @OA\Response(response="401", description="Unathorized")
     *
     * )
     * @param EditUserRequest $request
     * @return JsonResponse
     */

    public function editUser(EditUserRequest $request): JsonResponse {
        $user = auth()->user();
        $user->fill($request->all());
        return response()->json([
            'success' => true,
            'name' => $user->name,
            'about' => $user->about
        ]);
    }

    /**
     * @OA\Get(
     *     path="login/{driver}",
     *     summary="User autorization with social networks",
     *     @OA\Response(response="200", description="User's changed personal info"),
     *     @OA\Response(response="401", description="Unathorized")
     * )
     * @param string $driver
     * @return mixed
     */

    public function redirectToProvider(string $driver) {
        return Socialite::driver($driver)->stateless()->redirect();
    }

    public function handleProviderCallback(string $driver): JsonResponse {
        $socialiteUser = Socialite::driver($driver)->stateless()->user();
        $user = $this->userService->findOrCreateUser($driver, $socialiteUser);
        return login($user);
    }
}
