<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Board;
use App\Task;
use App\Status;

class TaskTest extends TestCase
{
    public $user, $headers, $board, $task, $status;
    public function setUp():void
    {
        parent::setUp();
        
        $this->createFactoryModels();
    }


    public function createFactoryModels()
    {
        global $user, $headers, $board, $task, $status;
        $user = factory(User::class)->create();
        $board = factory(Board::class)->create(['user_id' => $user->id]);
        $user->boards()->attach($board,  ['role_id' => 2]);
        $tokens = auth()->login($user);
        $access_token = $tokens['access_token'];
        $headers = ['Authorization' => "Bearer $access_token"];
        $status = new Status;
        $status->name = 'finished';
        $board->statuses()->save($status);
        $task = factory(Task::class)->create(['board_id'=>$board->id, 'status_id'=>$status->id]);
        $task->board()->associate($board);
        $task->status()->associate($status);
    }
    public function testListTasks()
    {
        global $user, $headers, $board, $task, $status;
        // $user = factory(User::class)->create();
        // $first_board = factory(Board::class)->create(['user_id' => $user->id]);
        // $user->boards()->attach($first_board);
        // $task = factory(Task::class)->create(['status_id']);
        // $tokens = auth()->login($user);
        // $access_token = $tokens['access_token'];
        // $headers = ['Authorization' => "Bearer $access_token"];
        $this->json('GET', 'api/v1/boards/'.$board->id.'/tasks', [], $headers)->assertStatus(200)->assertJson(['success' => true, 'message' => 'Tasks retrieved successfully']);
    }
    public function testShowTask()
    {
        global $user, $headers, $board, $task, $status;
        $this->json('GET', 'api/v1/boards/'.$board->id.'/tasks/'.$task->id, [], $headers)->assertStatus(200)->assertJson(['success' => true, 'message' => 'Task retrieved successfully']);
    }
    public function testStoreTask()
    {
        global $user, $headers, $board, $task, $status;
        $payload = [
            'name' => 'test task', 
            'description' => 'test creating task', 
            'status' => 'finished', 
            'expired_at' => '2021-02-10'
        ];
        $this->json('POST', 'api/v1/boards/'.$board->id.'/tasks', $payload, $headers)->assertStatus(200)->assertJson(['success' => true, 'message' => 'Task created successfully']);
    }
    // public function testChangeStatus()
    // {
    //     global $user, $headers, $board, $task, $status;
    //     $taskUser = factory(User::class)->create();
    //     $taskUser->boards()->attach($board, ['role_id'=>3]);
    //     $newStatus = new Status;
    //     $task->users()->attach($taskUser);
    //     $newStatus->name = 'active';
    //     $board->statuses()->save($newStatus);
    //     $payload =[
    //         'status' => 'active'
    //     ];
    //     $this->json('PUT', 'api/v1/boards/'.$board->id.'/tasks/'.$task->id.'/change-status', $payload, $headers)->assertStatus(200)->assertJson(['success' => true, 'message' => 'Status changed successfully']);
    // }
    public function testUpdateTask()
    {
        global $user, $headers, $board, $task;
        $payload=[
            'name'=>'new test name', 
            'description'=>'new description'
        ];
        $this->json('PUT', 'api/v1/boards/'.$board->id.'/tasks/'.$task->id, $payload, $headers)->assertStatus(200)->assertJson(['success' => true, 'message' => 'Task updated successfully']);
    }
    public function testAddUserTask()
    {
        global $user, $headers, $board, $task;
        $newUser = factory(User::class)->create();
        $newUser->boards()->attach($board, ['role_id'=>3]);
        $this->json('POST', 'api/v1/boards/'.$board->id.'/tasks/'.$task->id.'/add-user?userId='.$newUser->id, [], $headers)->assertStatus(200)->assertJson(['success' => true]);
    }
    public function testDeleteUser()
    {
        global $user, $headers, $board, $task;
        $newUser = factory(User::class)->create();
        $newUser->boards()->attach($board, ['role_id'=>3]);
        $task->users()->attach($newUser);
        $this->json('delete', 'api/v1/boards/'.$board->id.'/tasks/'.$task->id.'/delete-user?userId='.$newUser->id, [], $headers)->assertStatus(200)->assertJson(['success' => true]);
    }
    public function testDeleteTask()
    {
        global $user, $headers, $board, $task;
        $this->json('delete', 'api/v1/boards/'.$board->id.'/tasks/'.$task->id, [], $headers)->assertStatus(200)->assertJson(['success' => true]);
    }
}
