<?php

namespace App\Http\Controllers\API;

use JWTAuth;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use App\User;
use App\SocialAccount;
use Illuminate\Support\Facades\Auth;
use Validator;
use Exeption;
use Tymon\JWTAuth\Claims\Expiration;
use Tymon\JWTAuth\Http\Parser\AuthHeaders as headers;
use Tymon\JWTAuth\Http\Parser;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends BaseController
{
    public $loginAfterSignUp = true;
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
/**
 * @OA\Post(
 *     path="register",
 *     summary="Register new user.",
 *     @OA\Parameter(
 *     name="name",
 *     in="query",
 *     required=true,
 *     description="name of the new user",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="email",
 *     in="query",
 *     required=true,
 *     description="email of the new user",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="password",
 *     in="query",
 *     required=true,
 *     description="password of the new user",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="c_password",
 *     in="query",
 *     required=true,
 *     description="confirmation of user's password",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="200", description="User registered successfully"),
 *     @OA\Response(response="404", description="Validation error")
 * 
 * )
 */
    public function register (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:30',
            'email' => 'required|email|max:40',
            'password' => 'required|min:8|max:40',
            'c_password' => 'required|same:password',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error',  $validator->errors());
        }
        $user = new User();
        $user->fill(['name'=>$request->name, 'email'=>$request->email]);
        $random_number = rand(1, 30);
        $pokemon_api_answer = json_decode($this->sendAPIRequest('https://pokeapi.co/api/v2/characteristic/', $random_number.'/'), true);
        $user->about = $pokemon_api_answer['descriptions']['2']['description'];
        $user->password = bcrypt($request->password);
        
        $user->save();
        if ($this->loginAfterSignUp) {
            return $this->login($request);
        }

        return response()->json([
            'success'   =>  true,
            'data'      =>  $user
        ], 200);
        
    }
/**
 * @OA\Post(
 *     path="login",
 *     summary="Login user.",
 *     @OA\Parameter(
 *     name="email",
 *     in="query",
 *     required=true,
 *     description="email of the user",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="password",
 *     in="query",
 *     required=true,
 *     description="password of the user",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="200", description="Access token"),
 *     @OA\Response(response="401", description="Unathorized")
 * 
 * )
 */
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' =>$request->password
        ];
        $tokens = auth()->attempt($data);
        if (!$tokens) { 
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }
        return response()->json([
            'success' => true,
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
        ]);

    }
/**
 * @OA\Delete(
 *     path="logout",
 *     summary="Logout the current user.",
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="refresh_token",
 *     in="query",
 *     required=true,
 *     description="refresh token of the user",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="200", description="Access token"),
 *     @OA\Response(response="401", description="Unathorized")
 * 
 * )
 */
    public function logout(Request $request){
        $this->validate($request, [
            'refresh_token' => 'required'
        ]);

        try {
            auth()->logout();
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
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="200", description="Access and refresh tokens"),
 *     @OA\Response(response="401", description="Unathorized")
 * 
 * )
 */
    public function refresh(Request $request){
            $tokens = auth()->refresh();
            return response()->json([
                'success' => true,
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
            ]);
    }
   /**
 * @OA\Get(
 *     path="user",
 *     summary="Get current user's personal informaiton.",
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="name",
 *     in="query",
 *     required=false,
 *     description="name of the user",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="about",
 *     in="query",
 *     required=false,
 *     description="description of the user",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="200", description="User personal info"),
 *     @OA\Response(response="401", description="Unathorized")
 * 
 * )
 */
    public function getUser(Request $request){
        $user= auth()->user();
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
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="name",
 *     in="query",
 *     required=false,
 *     description="name of the user",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="about",
 *     in="query",
 *     required=false,
 *     description="description of the user",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="200", description="User's changed personal info"),
 *     @OA\Response(response="401", description="Unathorized")
 * 
 * )
 */
    public function editUser(Request $request)
    {
        $user=auth()->user();
        $validator = Validator::make($request->all(), [
            'name' => 'min:2|max:30',
            'about' => 'max:512',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error',  $validator->errors());
        }
        $user->fill($request->all());
        return response()->json([
            'success' =>true,
            'name' =>$user->name, 
            'about' =>$user->about
        ]);
    }
/**
 * @OA\Get(
 *     path="login/{driver}",
 *     summary="User autorization with social networks",
 *     @OA\Response(response="200", description="User's changed personal info"),
 *     @OA\Response(response="401", description="Unathorized")
 * 
 * )
 */
    public function redirectToProvider(string $driver)
    {
        return Socialite::driver($driver)->stateless()->redirect();
    }
    public function handleProviderCallback(string $driver)
    {
        $socialiteUser=Socialite::driver($driver)->stateless()->user();
        //dd($user);
        $user = $this->findOrCreateUser($driver, $socialiteUser);
        //dd($user);
        if ($this->loginAfterSignUp) {
            $data = [
                'email' => $user->email,
                'password' => $user->password,
            ];
            
            $tokens = auth()->login($user);
            // dd(decrypt($user->password));
            if (!$tokens) { 
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }
            return response()->json([
                'success' => true,
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
            ]);
        }
        // return redirect('/');
    }
    public function findOrCreateUser($driver, $socialiteUser){
        if($user = $this->findUserBySocialId($driver, $socialiteUser->getId())){
            return $user;
        }
        if($user = $this->findUserByEmail($driver, $socialiteUser->getEmail())){
            $this->addSocialAccount($driver, $user, $socialiteUser);
            return $user;
        }
        $user = User::create([
            'name'=>$socialiteUser->getName(),
            'email'=>$socialiteUser->getEmail(),
            'password'=>bcrypt(str_random(25)),
        ]);
        $this->addSocialAccount($driver, $user, $socialiteUser);
        return $user;
    }
    public function findUserBySocialId($driver, $id){
        $socialAccount = SocialAccount::where('driver', $driver)
        ->where('driver_id', $id)->first();
        return $socialAccount?$socialAccount->user:false;
    }
    public function addSocialAccount($driver, $user, $socialiteUser){
        SocialAccount::create([
            'user_id'=>$user->id, 
            'driver'=>$driver, 
            'driver_id'=>$socialiteUser->getId(), 
            'token' =>$socialiteUser->token, 
        ]);
    }
    public function findUserByEmail($driver, $email){
        return !$email?null:User::where('email', $email)->first();
    }
}
