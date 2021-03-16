<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use JWTAuth;

class LoginTest extends TestCase
{
    public function testLogin()
    {
        $user = factory(User::class)->create([
            'email' => 'testlogin@user.com',
            'password' => bcrypt('12345poi'),
        ]);
        $payload = ['email' => 'testlogin@user.com', 'password' => '12345poi'];
        $this->json('POST', 'api/v1/login', $payload)->assertJson(['success' => true]);
    }
}
