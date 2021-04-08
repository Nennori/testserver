<?php


namespace App\Services;
use App\SocialAccount;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserService
{
    protected $apiRequest;

    public function __construct(APIRequest $apiRequest) {
        $this->apiRequest = $apiRequest;
    }

    public function createUser($credentials): User {
        $user = new User;
        $user->fill([
            'name'=>$credentials['name'],
            'email'=>$credentials['email'],
            'about'=>$this->addDescription()
        ]);
        $user->password = bcrypt($credentials['password']);
        $user->save();
        return $user;
    }

    public function addDescription(): string
    {
        $random_number = rand(1, 30);
        $pokemonApiAnswer = json_decode($this->apiRequest->sendAPIRequest('https://pokeapi.co/api/v2/characteristic/', $random_number.'/'), true);
        if($pokemonApiAnswer == null) return 'you are stupid';
        return $pokemonApiAnswer['descriptions']['2']['description'];
    }

    public function findOrCreateUser($driver, $socialiteUser):User {
        if ($user = $this->findUserBySocialId($driver, $socialiteUser->getId())) {
            return $user;
        }
        if ($user = $this->findUserByEmail($socialiteUser->getEmail())) {
            $this->addSocialAccount($driver, $user, $socialiteUser);
            return $user;
        }
        $user = $this->createUser([
            'name' => $socialiteUser->getName(),
            'email' => $socialiteUser->getEmail(),
            'password' => str_random(25)
        ]);
        $this->addSocialAccount($driver, $user, $socialiteUser);
        return $user;
    }

    public function findUserBySocialId($driver, $id) {
        $socialAccount = SocialAccount::where('driver', $driver)
            ->where('driver_id', $id)->first();
        return $socialAccount ? $socialAccount->user : false;
    }

    public function addSocialAccount($driver, $user, $socialiteUser) {
        SocialAccount::create([
            'user_id' => $user->id,
            'driver' => $driver,
            'driver_id' => $socialiteUser->getId(),
            'token' => $socialiteUser->token,
        ]);
    }

    public function findUserByEmail($email)
    {
        return !$email ? null : User::where('email', $email)->first();
    }

    public function generateTokens($credentials){
        $user = User::where('email', $credentials['email'])->first();
        if($user){
            $tokens['access']=auth()->claims(['type'=>'access'])->setTTL(config('jwt.ttl'))->tokenById($user->id);
            $tokens['refresh']=auth()->claims(['type'=>'refresh'])->setTTL(config('jwt.refresh_ttl'))->tokenById($user->id);
            return $tokens;
        }
        else{
            return null;
        }
    }

}
