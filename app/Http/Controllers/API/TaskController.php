<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StatusRequest;
use App\Http\Requests\TaskRequest;
use App\Services\TaskService;
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
    protected $taskService;

    public function __construct(TaskService $taskService){
        $this->taskService = $taskService;
    }

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

    public function index(Board $board) {
        $this->authorize('index', $board);
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

    public function show(Board $board, Task $taskId) {
        $this->authorize('index', $board);
        $task = $board->tasks->find($taskId);
        if (!$task) {
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
     * @param TaskRequest $request
     * @param string $board
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */

    public function store(TaskRequest $request, Board $board) {
        $this->authorize('store', $board);
        $task = $this->taskService()->createTask($request, $board);
        if ($task) {
            return $this->sendResponse($task->toArray(), 'Task created successfully');
        } else {
            return $this->sendError('Task not added', 500);
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
     * @param StatusRequest $request
     * @param Board $board
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */

    public function changeStatus(StatusRequest $request, Board $board, Task $task)
    {
        $this->authorize('index', $board);
        $task = $board->tasks->find($task->id);
        if (!$task) {
            return $this->sendError('Task not found', 400);
        }
        $boardStatus = $this->taskService->findStatus($board, $request->status);
        if (!$boardStatus) {
            return response()->json(['success' => false, 'message' => 'No such status', 400]);
        }
        $this->taskService->changeStatus($boardStatus, $task);
        $this->taskService->sendEmails(auth()->user(), $boardStatus, $task, $board);
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
     * @param TaskRequest $request
     * @param string $board
     * @param string $task
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */

    public function update(TaskRequest $request, Board $board, Task $task)
    {
        $this->authorize('store', $board);
        $task = $this->taskService->changeTask($request, $board, $task);
        if ($task) {
            return $this->sendResponse($task->toArray(), 'Task updated successfully');
        } else {
            return $this->sendError('Task not updated', 500);
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

    public function addUser(Request $request, Board $board, Task $task)
    {
        $userId = $request->query('userId');
        $this->authorize('store', $board);
        $user = User::find($userId);
        $task = $board->tasks->find($task->id);
        if (!$user || !$user->boards->find($board->id)) {
            return $this->sendError('User not found', 400);
        }
        if (!$task) {
            return $this->sendError('Task not found', 400);
        }
        $task->users()->attach($user);
        return response()->json(['success' => true], 200);
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

    public function deleteUser(Request $request, Board $board, Task $task)
    {
        $board = auth()->user()->boards->find($board);
        if (!$board) {
            return $this->sendError('Board not found', 400);
        }
        $this->authorize('store', $board);
        $task = $board->tasks->find($task);
        $user = $task->users->find($request->userId);
        if (!$user) {
            return $this->sendError('User not found', 400);
        }
        if (!$task) {
            return $this->sendError('Task not found', 400);
        }
        $task->users()->detach($user);
        return response()->json(['success' => true], 200);
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

    public function destroy(Board $board, Task $task)
    {
        $this->authorize('store', $board);
        $task = $board->tasks->find($taskId);
        if (!$task) {
            return $this->sendError('Task not found', 400);
        }
        $task->delete();
        return response()->json([
            'success' => true
        ]);
    }
}
