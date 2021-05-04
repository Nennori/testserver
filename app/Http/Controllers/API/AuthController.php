<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

/**
 *    @OA\SecurityScheme(
 *        securityScheme="bearerAuth",
 *        type="http",
 *        in="header",
 *        bearerFormat="JWT",
 *        scheme="bearer"
 *    )
 */
class AuthController extends Controller
{
    protected $userService;

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     operationId="register",
     *     summary="Register new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass user credentials",
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(
     *                 property="name",
     *                 description="name of the new user",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 description="email of the new user",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 description="password of the new user",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="password_confirmation",
     *                 description="confirmation of user's password",
     *                 type="string"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 title="data",
     *                 description="Response data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(
     *                         property="access_token",
     *                         title="access token",
     *                         description="Access token",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="refresh_token",
     *                         title="refresh token",
     *                         description="Refresh token",
     *                         type="string",
     *                     ),
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 title="message",
     *                 description="Response message",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 title="status",
     *                 description="Response status",
     *                 type="string",
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 title="data",
     *                 description="Response data",
     *                 type="array",
     *                 @OA\Items(
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 title="message",
     *                 description="Response message",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 title="status",
     *                 description="Response status",
     *                 type="string",
     *             ),
     *         ),
     *     ),
     * )
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $this->userService->createUser($request->all());
        return $this->login($request);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     operationId="login",
     *     summary="Login user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass user credentials",
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(
     *                 property="email",
     *                 description="email of the new user",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 description="password of the new user",
     *                 type="string"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="User logged in successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 title="data",
     *                 description="Response data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(
     *                         property="access_token",
     *                         title="access token",
     *                         description="Access token",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="refresh_token",
     *                         title="refresh token",
     *                         description="Refresh token",
     *                         type="string",
     *                     ),
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 title="message",
     *                 description="Response message",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 title="status",
     *                 description="Response status",
     *                 type="string",
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unathorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 title="data",
     *                 description="Response data",
     *                 type="array",
     *                 @OA\Items(
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 title="message",
     *                 description="Response message",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 title="status",
     *                 description="Response status",
     *                 type="string",
     *             ),
     *         ),
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        if (Auth::validate(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $tokens = $this->userService->generateTokens($credentials);
            if ($tokens) {
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
     *     path="/api/v1/logout",
     *     operationId="logout",
     *     summary="Logout the current user",
     *     tags={"Authentication"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass user credentials",
     *         @OA\JsonContent(
     *             required={"token"},
     *             @OA\Property(
     *                 property="token",
     *                 description="Access token of the user",
     *                 type="string"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="User successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 title="data",
     *                 description="Response data",
     *                 type="array",
     *                 @OA\Items(
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 title="message",
     *                 description="Response message",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 title="status",
     *                 description="Response status",
     *                 type="string",
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unathorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 title="data",
     *                 description="Response data",
     *                 type="array",
     *                 @OA\Items(
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 title="message",
     *                 description="Response message",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 title="status",
     *                 description="Response status",
     *                 type="string",
     *             ),
     *         ),
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
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
     *     path="/api/v1/refresh",
     *     operationId="refresh",
     *     summary="Refresh user's tokens",
     *     tags={"Authentication"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Response(
     *         response="200",
     *         description="Access and refresh tokens",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 title="data",
     *                 description="Response data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(
     *                         property="access_token",
     *                         title="access token",
     *                         description="Access token",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="refresh_token",
     *                         title="refresh token",
     *                         description="Refresh token",
     *                         type="string",
     *                     ),
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 title="message",
     *                 description="Response message",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 title="status",
     *                 description="Response status",
     *                 type="string",
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unathorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 title="data",
     *                 description="Response data",
     *                 type="array",
     *                 @OA\Items(
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 title="message",
     *                 description="Response message",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 title="status",
     *                 description="Response status",
     *                 type="string",
     *             ),
     *         ),
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = auth()->user();
        if ($user !== null && auth()->payload()->get('type') === 'refresh') {
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
     *     path="login/{driver}",
     *     operationId="loginThirdParty",
     *     summary="User autorization with social networks",
     *     tags={"Third-party authorization"},
     *     @OA\Response(response="200", description="User's changed personal info"),
     *     @OA\Response(
     *         response="401",
     *         description="Unathorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 title="data",
     *                 description="Response data",
     *                 type="array",
     *                 @OA\Items(
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 title="message",
     *                 description="Response message",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 title="status",
     *                 description="Response status",
     *                 type="string",
     *             ),
     *         ),
     *     )
     * )
     * @param string $driver
     * @return mixed
     */
    public function redirectToProvider(string $driver)
    {
        return Socialite::driver($driver)->stateless()->redirect();
    }

    public function handleProviderCallback(string $driver): JsonResponse
    {
        $socialiteUser = Socialite::driver($driver)->stateless()->user();
        $user = $this->userService->findOrCreateUser($driver, $socialiteUser);
        return login($user);
    }
}
