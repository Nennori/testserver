<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use App\Task;
use App\Board;
use App\User;
use App\Role;
use App\Jobs\SendEmailJob;
use Validator;

class TaskController extends BaseController
{
    /**
 * @OA\Get(
 *     path="api/v1/boards/{boardId}/tasks",
 *     summary="Displays a listing of user's tasks.",
 *     @OA\Parameter(
 *     name="boardId",
 *     in="path",
 *     description="board id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="int")
 *     ),
 *     @OA\Response(response="200", description="List of user's tasks"),
 *     @OA\Response(response="400", description="Board not found")
 * )
 */
    public function index(string $boardId)
    {
        $board = auth()->user()->boards->find($boardId);
        if(!$board){
            return $this->sendError('Board not found', 400);
        }
        $tasks = $board->tasks->toArray();
        return $this->sendResponse($tasks, 'Tasks retrieved successfully');
    }
 /**
 * @OA\Get(
 *     path="api/v1/boards/{boardId}/tasks/{taskId}",
 *     summary="Shows task.",
 *     @OA\Parameter(
 *     name="boardId",
 *     in="path",
 *     required=true,
 *     description="board id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="taskId",
 *     in="path",
 *     required=true,
 *     description="task id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="200", description="Task retrieved successfully"),
 *     @OA\Response(response="400", description="Board or task not found")
 * 
 * )
 */
    public function show(string $boardId, string $taskId){
        $board = auth()->user()->boards->find($boardId);
        if(!$board){
            return $this->sendError('Board not found', 400);
        }
        $task = $board->tasks->find($taskId);
        if(!$task){
            return $this->sendError('Task not found', 400);
        }
        return $this->sendResponse($task->toArray(), 'Task retrieved successfully');
    }
/**
 * @OA\Post(
 *     path="api/v1/boards/{boardId}/tasks",
 *     summary="Creates task.",
 *     @OA\Parameter(
 *     name="boardId",
 *     in="path",
 *     required=true,
 *     description="board id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="name",
 *     in="query",
 *     required=true,
 *     description="task name",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="description",
 *     in="query",
 *     required=true,
 *     description="task description",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="status",
 *     in="query",
 *     required=true,
 *     description="task status",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="expired_at",
 *     in="query",
 *     required=true,
 *     description="tdeadline of the task",
 *     @OA\Schema(type="datetime")
 *     ),
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="200", description="Task created successfully"),
 *     @OA\Response(response="400", description="Board or status not found"),
 *     @OA\Response(response="404", description="Validation error"),
 *     @OA\Response(response="500", description="Task not added"),
 *     @OA\Response(response="403", description="User have not permission to create new task")
 * )
 */
    public function store(Request $request, string $boardId){
        $validator = Validator::make($request->all(), [
            'name'=>'required|min:2|max:30', 
            'description'=>'required|max:255', 
            'status'=>'required|min:2|max:30', 
            'expired_at'=>'required'
            ]);
        if($validator->fails()){
            return $this->sendError('Validation Error', 404);
        }
        $board = auth()->user()->boards->find($boardId);
        if(!$board){
            return $this->sendError('Board not found', 400);
        }
        if($board->user_id === auth()->user()->id || $board->pivot->role_id->edit_task === true){
            $status = $request->status;
            $boardStatus = $board->statuses->where('name', $status)->first();
            if(!$boardStatus){
                return $this->sendError('No such status', 400);
            }
            $taskCount = count($board->tasks)+1;
            $taskNumber = $this->sendAPIRequest('http://numbersapi.com/', (string)$taskCount.'/trivia');
            // $client = new Client(['base_uri' => 'http://numbersapi.com/', 'timeout'  => 2.0]);
            // try {
            //     $response = $client->request('GET', (string)$taskCount.'/trivia');
            //     $taskNumber = (string)$response->getBody();
            // } catch (BadResponseException $e) {
            //     echo Psr7\Message::toString($e->getRequest());
            //     echo Psr7\Message::toString($e->getResponse());
            //     $taskNumber = (string)$taskCount;
            // }
            // $response = $client->request('GET', $taskCount.'?json');
            $task = new Task;
            $task->fill(['task_number'=>(string)$taskNumber, 'name'=>$request->name, 'description'=>$request->description, 'expired_at'=>$request->expired_at]);
            $task->board()->associate($board);
            $task->status()->associate($boardStatus);
            if($task->save()){
                return $this->sendResponse($task->toArray(), 'Task created successfully');
            }
            else{
                return $this->sendError('Task not added', 500);
            }
        }
        else{
            return $this->sendError('Forbidden', 403);
        }
        
    }
/**
 * @OA\Put(
 *     path="api/v1/boards/{boardId}/tasks/{taskId}/change-status",
 *     summary="Change status of the task.",
 *     @OA\Parameter(
 *     name="boardId",
 *     in="path",
 *     required=true,
 *     description="board id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="taskId",
 *     in="path",
 *     required=true,
 *     description="task id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="status",
 *     in="query",
 *     required=true,
 *     description="new status",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="200", description="Status created successfully"),
 *     @OA\Response(response="400", description="Board, task or status not found")
 * )
 */
    public function changeStatus(Request $request, string $boardId, string $taskId){
        $board = auth()->user()->boards->find($boardId);
        if(!$board){
            return $this->sendError('Board not found', 400);
        }
        $task = $board->tasks->find($taskId);
        if(!$task){
            return $this->sendError('Task not found', 400);
        }
        $boardStatus = $board->statuses->where('name', $request->status)->first();
        if(!$boardStatus){
            return $this->sendError('No such status', 400);
        }
        $task->status()->associate($boardStatus);
        $task->save();
        $data = ['newStatus'=>$boardStatus->name, 'owner'=>auth()->user()->name, 'task'=>$task->name];
        $taskMembers = $board->users->toArray();
        $key = array_search(auth()->user()->id, array_column($taskMembers, 'id'));
        unset($taskMembers[$key]);
        foreach($taskMembers as $member){
            $data['email']=$member['email'];
            dispatch(new SendEmailJob($data));
        }
        return $this->sendResponse($boardStatus->toArray(), 'Status changed successfully');
    }
/**
 * @OA\Put(
 *     path="api/v1/boards/{boardId}/tasks/{taskId}",
 *     summary="Updates task.",
 *     @OA\Parameter(
 *     name="boardId",
 *     in="path",
 *     required=true,
 *     description="board id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="taskId",
 *     in="path",
 *     required=true,
 *     description="task id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="name",
 *     in="query",
 *     description="new task name",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="description",
 *     in="query",
 *     description="new task description",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="status",
 *     in="query",
 *     description="new task status",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="200", description="Status updated successfully"),
 *     @OA\Response(response="400", description="Board, task or status not found"),
 *     @OA\Response(response="500", description="Task not updated"),
 * )
 */
    public function update(Request $request, string $boardId, string $taskId){
        $board = auth()->user()->boards->find($boardId);
        if(!$board){
            return $this->sendError('Board not found', 400);
        }
        $role_id = $board->pivot->role_id;
        if($board->user_id === auth()->user()->id || Role::find($role_id)->edit_task === true){
        $task = $board->tasks->find($taskId);
        $task->fill($request->all());
        if($request->has('status')){
            $this->changeStatus($request, $boardId, $taskId);
        }
        if($task->save()){
            return $this->sendResponse($task->toArray(), 'Task updated successfully');
        }
        else{
            return $this->sendError('Task not updated', 500);
        }
    }
    else{
        return $this->sendError('Forbidden', 403);
    }
    }
/**
 * @OA\Post(
 *     path="api/v1/boards/{boardId}/tasks/{taskId}/add-user",
 *     summary="Add user to the task.",
 *     @OA\Parameter(
 *     name="boardId",
 *     in="path",
 *     required=true,
 *     description="board id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="taskId",
 *     in="path",
 *     required=true,
 *     description="task id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="userId",
 *     in="path",
 *     description="user id",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="200", description="User added successfully"),
 *     @OA\Response(response="400", description="Board, task or user not found"),
 * )
 */
    public function addUser(Request $request, string $boardId, string $taskId){
        $userId = $request->query('userId');
        $board = auth()->user()->boards->find($boardId);
        if(!$board){
            return $this->sendError('Board not found', 400);
        }
        $role_id = $board->pivot->role_id;
        if($board->user_id === auth()->user()->id || Role::find($role_id)->edit_task === true){
        $user = User::find($userId);
        $task = $board->tasks->find($taskId);
        if(!$user || !$user->boards->find($boardId)){
            return $this->sendError('User not found', 400);
        }
        if(!$task){
            return $this->sendError('Task not found', 400);
        }
        $task->users()->attach($user);
        return response()->json(['success'=>true], 200);
    }
    else{
        return $this->sendError('Forbidden', 403);
    }
    }
/**
 * @OA\Delete(
 *     path="api/v1/boards/{boardId}/tasks/{taskId}/delete-user",
 *     summary="Delete user from the task.",
 *     @OA\Parameter(
 *     name="boardId",
 *     in="path",
 *     required=true,
 *     description="board id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="taskId",
 *     in="path",
 *     required=true,
 *     description="task id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="userId",
 *     in="path",
 *     description="user id",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="200", description="User deleted successfully"),
 *     @OA\Response(response="400", description="Board, task or user not found"),
 * )
 */
    public function deleteUser(Request $request, string $boardId, string $taskId){
        $board = auth()->user()->boards->find($boardId);
        if(!$board){
            return $this->sendError('Board not found', 400);
        }
        $role_id = $board->pivot->role_id;
        if($board->user_id === auth()->user()->id || Role::find($role_id)->edit_task === true){
        //$user = User::find($request->userId);
        $task = $board->tasks->find($taskId);
        $user = $task->users->find($request->userId);
        if(!$user){
            return $this->sendError('User not found', 400);
        }
        if(!$task){
            return $this->sendError('Task not found', 400);
        }
        $task->users()->detach($user);
        //$user->tasks()->attach($task);
        return response()->json(['success'=>true], 200);
    }
    else{
        return $this->sendError('Forbidden', 403);
    }
    }
/**
 * @OA\Delete(
 *     path="api/v1/boards/{boardId}/tasks/{taskId}",
 *     summary="Delete task.",
 *     @OA\Parameter(
 *     name="boardId",
 *     in="path",
 *     required=true,
 *     description="board id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="taskId",
 *     in="path",
 *     required=true,
 *     description="task id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="200", description="User deleted successfully"),
 *     @OA\Response(response="400", description="Board, task or user not found"),
 * )
 */
    public function destroy($boardId, $taskId){
        $board = auth()->user()->boards->find($boardId);
        if(!$board){
            return $this->sendError('Board not found', 400);
        }
        $role_id = $board->pivot->role_id;
        if($board->user_id === auth()->user()->id || Role::find($role_id)->edit_task === true){
        $task = $board->tasks->find($taskId);
        if(!$task){
            return $this->sendError('Task not found', 400);
        }
        $task->delete();
        return response()->json([
            'success' => true
        ]);
        }
        else{
            return $this->sendError('Forbidden', 403);
        }
    }
}
