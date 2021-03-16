<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Board;
use App\Status;

class BoardTest extends TestCase
{
    public $user, $headers, $board, $task;
    public function setUp():void
    {
        parent::setUp();
        
        $this->createFactoryModels();
    }


    public function createFactoryModels()
    {
        global  $user, $headers, $board;
        $user = factory(User::class)->create();
        $board = factory(Board::class)->create(['user_id' => $user->id]);
        $user->boards()->attach($board,  ['role_id' => 2]);
        $tokens = auth()->login($user);
        $access_token = $tokens['access_token'];
        $headers = ['Authorization' => "Bearer $access_token"];
    }
    public function testListBoards()
    {
        global $user, $headers, $board;
        // $user = factory(User::class)->create();
        // $first_board = factory(Board::class)->create(['user_id' => $user->id]);
        $second_board = factory(Board::class)->create(['user_id' => $user->id]);
        // $user->boards()->attach($first_board);
        $user->boards()->attach($second_board);
        // $tokens = auth()->login($user);
        // $access_token = $tokens['access_token'];
        // $headers = ['Authorization' => "Bearer $access_token"];
        $this->json('GET', 'api/v1/boards', [], $headers)->assertStatus(200)->assertJson(['success' => true, 'message' => 'Boards retrieved successfully']);
    }
    public function testStoreBoard()
    {
        global $user, $headers, $board;
        // $user = factory(User::class)->create();
        $payload = [
            'name' => 'testBoard'
        ];
        // $tokens = auth()->login($user);
        // $access_token = $tokens['access_token'];
        // $headers = ['Authorization' => "Bearer $access_token"];
        $this->json('POST', 'api/v1/boards', $payload, $headers)->assertStatus(200)->assertJson(['success' => true, 'message' => 'Board created successfully']);
    }
    public function testUpdateBoard()
    {
        global $user, $headers, $board;
        // $user = factory(User::class)->create();
        // $first_board = factory(Board::class)->create(['user_id' => $user->id]);
        $second_board = factory(Board::class)->create(['user_id' => $user->id]);
        $payload = [
            'name' => 'testBoard'
        ];
        // $user->boards()->attach($first_board);
        $user->boards()->attach($second_board);
        // $tokens = auth()->login($user);
        // $access_token = $tokens['access_token'];
        // $headers = ['Authorization' => "Bearer $access_token"];
        $this->json('PUT', 'api/v1/boards/'.$second_board->id, $payload, $headers)->assertStatus(200)->assertJson(['success' => true, 'message' => 'Board updated successfully']);
    }
    public function testAddUser()
    {
        global $user, $headers, $board;
        // $first_user = factory(User::class)->create();
        $second_user = factory(User::class)->create();
        // $board = factory(Board::class)->create(['user_id' => $first_user->id]);
        // // $first_user->boards()->attach($board);
        // $tokens = auth()->login($first_user);
        // $access_token = $tokens['access_token'];
        // $headers = ['Authorization' => "Bearer $access_token"];
        $this->json('POST', 'api/v1/boards/'.$board->id.'/add-user?userId='.$second_user->id.'&role=user', [], $headers)->assertStatus(200)->assertJson(['success' => true]);
    }
    public function testAddStatus()
    {
        global $user, $headers, $board;
        // $user = factory(User::class)->create();
        // $board = factory(Board::class)->create(['user_id' => $user->id]);
        // $user->boards()->attach($board);
        // $tokens = auth()->login($user);
        // $access_token = $tokens['access_token'];
        // $headers = ['Authorization' => "Bearer $access_token"];
        $status = 'finished';
        $this->json('POST', 'api/v1/boards/'.$board->id.'/add-status?status='.$status, [], $headers)->assertStatus(200)->assertJson(['success' => true]);
    }
    public function testDeleteStatus()
    {
        global $user, $headers, $board;
        // $user = factory(User::class)->create();
        // $board = factory(Board::class)->create(['user_id' => $user->id]);
        // $user->boards()->attach($board);
        // $tokens = auth()->login($user);
        // $access_token = $tokens['access_token'];
        // $headers = ['Authorization' => "Bearer $access_token"];
        $status = 'finished';
        $newStatus = new Status;
        $newStatus->name = $status;
        $board->statuses()->save($newStatus);
        $this->json('delete', 'api/v1/boards/'.$board->id.'/delete-status?status='.$status, [], $headers)->assertStatus(200)->assertJson(['success' => true]);
    }
    public function testDeleteBoard()
    {
        global $user, $headers, $board;
        // $user = factory(User::class)->create();
        // $board = factory(Board::class)->create(['user_id' => $user->id]);
        // $user->boards()->attach($board);
        // $tokens = auth()->login($user);
        // $access_token = $tokens['access_token'];
        // $headers = ['Authorization' => "Bearer $access_token"];
        $status = 'finished';
        $newStatus = new Status;
        $newStatus->name = $status;
        $board->statuses()->save($newStatus);
        $this->json('delete', 'api/v1/boards/'.$board->id, [], $headers)->assertStatus(200)->assertJson(['success' => true]);
    }
    public function testDeleteUser()
    {
        global $user, $headers, $board;
        // $first_user = factory(User::class)->create();
        // $board = factory(Board::class)->create(['user_id' => $first_user->id]);
        // $first_user->boards()->attach($board, ['role_id' => 1]);
        // $tokens = auth()->login($first_user);
        // $access_token = $tokens['access_token'];
        // $headers = ['Authorization' => "Bearer $access_token"];
        $this->json('delete', 'api/v1/boards/'.$board->id.'/delete-user', [], $headers)->assertStatus(200)->assertJson(['success' => true]);
    }
}