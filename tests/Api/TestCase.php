<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase as BaseTestCase;
use App\User;
//use JWTAuth;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class TestCase extends BaseTestCase
{
    protected $user;
    public function actingAs(UserContract $user, $driver=null)
    {
        $this->user = $user;
        return $this;
    }

    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        if($this->user) {
            $tokens = \JWTAuth::attempt($this->user);
            $server['HTTP_AUTHORIZATION'] = 'Bearer'.$tokens['access_token'];
        }
        $server['HTTP_ACCEPT'] = 'application/json';

        return parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);
    }
    /**
     * @return $this
     */
    protected function actingAsAdmin()
    {
        return $this->actingAs(factory(User::class, 'admin')->create());
    }
}
