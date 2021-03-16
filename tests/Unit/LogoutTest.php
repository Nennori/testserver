<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use JWTAuth;

class LogoutTest extends TestCase
{
    public function testLogout()
    {
        $user = factory(User::class)->create();
        $data = [
            'email' => $user->email,
            'password' =>$user->password,
        ];
        $tokens = auth()->login($user);
        //if ($tokens===false) return true;
        // $this->info($tokens);
        $access_token = $tokens['access_token'];
        $headers = ['Authorization' => "Bearer $access_token"];
        $this->json('delete', 'api/v1/logout', ['refresh_token' => $tokens['refresh_token']], $headers)->assertJson(
            ['success' => true,
            'message' => 'User logged out successfully']);
    }

}
