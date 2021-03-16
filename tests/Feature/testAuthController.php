<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use JWTAuth;

class testAuthController extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testRegister()
    {
        $this->json('POST', 'api/v1/register', ['name' => 'test1', 'email' => 'test1@mail.ru', 'password' => '12345poi', 'c_password' => '12345poi'])->assertJson(['success' => true]);
    }
    public function testLogin()
    {
       
        $payload = ['email' => 'testlogin@user.com', 'password' => 'toptal123'];
        $this->json('POST', 'api/v1/login', $payload)->assertJson(['success' => true]);
    }
    public function testLogout()
    {
        $user = factory(User::class)->create();
        $data = [
            'email' => $user->email,
            'password' =>$user->password
        ];
        $tokens = auth()->attempt($data);
        $headers = ['Authorization' => "Bearer $tokens[0]"];
        $this->json('delete', 'api/v1/logout', [], $headers)->assertJson(
            ['success' => true,
            'message' => 'User logged out successfully']);
    }
}
