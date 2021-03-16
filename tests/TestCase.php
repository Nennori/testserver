<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Database\Seeds\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Board;
// use Mail;

abstract class TestCase extends BaseTestCase
{
    
    use CreatesApplication, DatabaseMigrations, RefreshDatabase;
    
    public function setUp():void
    {
        parent::setUp();
        $this->seed('DatabaseSeeder');
    }
}
