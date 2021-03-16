<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use JWTAuth;

class RegisterTest extends TestCase
{
    public function testRegister()
    {
        $payload =['name' => 'test1',
        'email' => 'test1@mail.ru',
        'about' => 'hello',
        'password' => '12345poi', 
        'c_password' => '12345poi'];
        $this->json('POST', 'api/v1/register', $payload)->assertJson(['success' => true]);
    }
}
