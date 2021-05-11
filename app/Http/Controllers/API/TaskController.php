<?php

namespace App\Http\Controllers\API;

use App\Exceptions\ControllerException;
use App\Http\Requests\StatusRequest;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\MarkResource;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Task;
use App\Models\Board;
use Illuminate\Http\Response;
use Validator;

class TaskController extends BaseController
{
    protected $taskService;

    public function __construct(TaskService $taskService){
        $this->taskService = $taskService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/boards/{boardId}/tasks",
     *     operationId="getTasks",
     *     summary="Displays a listing of user's tasks",
     *     tags={"Task"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         description="Board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="List of user's boards",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/TaskCollection"
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
     *         )
     *     ),
     * )
     * @param Board $board
     * @return mixed
     * @throws AuthorizationException
     */
    public function index(Board $board) {
        $this->authorize('getTask', $board);
        $response = new TaskCollection($this->taskService->getTasks($board));
        return response($response, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/boards/{boardId}/tasks/{taskId}",
     *     operationId="getTask",
     *     summary="Show task",
     *     tags={"Task"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="taskId",
     *         in="path",
     *         required=true,
     *         description="Task id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Task retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 title="data",
     *                 description="Response data",
     *                 type="array",
     *                 @OA\Items(
     *                     ref="#/components/schemas/TaskResource"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 title="message",
     *                 description="Response message",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 title="status",
     *                 description="Response status",
     *                 type="string",
     *             ),
     *         ),
     *     ),
     *    @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
     *         )
     *     ),
     * )
     * @param Board $board
     * @param Task $task
     * @return mixed
     * @throws AuthorizationException
     */
    public function show(Board $board, Task $task) {
        $this->authorize('getTask', $board);
//        if (!$task) {
//            return $this->sendError('Task not found', 400);
//        }
        $response = new TaskResource($task);
        return response()->success($response, 'Task retrieved successfully', 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/boards/{boardId}/tasks",
     *     operationId="createTask",
     *     summary="Create task",
     *     tags={"Task"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="Board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         description="Task name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         description="task description",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=true,
     *         description="task status",
     *     @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="expired_at",
     *         in="query",
     *         required=true,
     *         description="tdeadline of the task",
     *         @OA\Schema(type="datetime")
     *     ),
     *     @OA\Parameter(
     *         name="Bearer Token",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Task created successfully"),
     *     @OA\Response(response="400", description="Board or status not found"),
     *     @OA\Response(response="404", description="Validation error"),
     *     @OA\Response(response="500", description="Task not added"),
     *     @OA\Response(response="403", description="User have not permission to create new task")
     * )
     * @param TaskRequest $request
     * @param Board $board
     * @return Response
     * @throws AuthorizationException
     * @throws ControllerException
     */
    public function store(TaskRequest $request, Board $board) {
        $this->authorize('storeTask', $board);
        $task = $this->taskService->createTask($request, $board);
        if ($task) {
            $response = new TaskResource($task);
            return response()->success($response, 'Task created successfully', 200);
        } else {
            throw new ControllerException('Server error', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/boards/{boardId}/tasks/{taskId}/status",
     *     operationId="changeStatus",
     *     summary="Change status of the task",
     *     tags={"Task"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="taskId",
     *         in="path",
     *         required=true,
     *         description="task id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=true,
     *         description="new status",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Status created successfully"),
     *     @OA\Response(response="400", description="Board, task or status not found")
     * )
     * @param StatusRequest $request
     * @param Board $board
     * @param Task $task
     * @return JsonResponse|Response
     * @throws AuthorizationException
     */

    public function changeStatus(StatusRequest $request, Board $board, Task $task) {
        $this->authorize('changeStatus', [$task, $board]);
        $boardStatus = $this->taskService->findStatus($board, $request->status);
//        if (!$boardStatus) {
//            return $this->sendError('No such status', 400);
//        }
        $response = new TaskResource($this->taskService->changeStatus($boardStatus, $task));
        $this->taskService->sendEmails(auth()->user(), $boardStatus, $task, $board);
        return $this->success($response, 'Status changed successfully', 200);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/boards/{boardId}/tasks/{taskId}",
     *     operationId="updateTask",
     *     summary="Update task",
     *     tags={"Task"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="Board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="taskId",
     *         in="path",
     *         required=true,
     *         description="Task id",
     *     @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="New task name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         description="New task description",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="New task status",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Status updated successfully"),
     *     @OA\Response(response="400", description="Board, task or status not found"),
     *     @OA\Response(response="500", description="Task not updated"),
     * )
     * @param TaskRequest $request
     * @param Board $board
     * @param Task $task
     * @return Response
     * @throws AuthorizationException
     * @throws ControllerException
     */

    public function update(TaskRequest $request, Board $board, Task $task) {
        $this->authorize('updateTask', $board);
        $task = $this->taskService->changeTask($request, $board, $task);
        if ($task) {
            $response = new TaskResource($task);
            return $this->success($response, 'Task updated successfully', 200);
        } else {
            throw new ControllerException('Task not updated', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/boards/{boardId}/tasks/{taskId}/user",
     *     operationId="addTaskUser",
     *     summary="Add user to the task",
     *     tags={"Task"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="Board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="taskId",
     *         in="path",
     *         required=true,
     *         description="Task id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="User added successfully"),
     *     @OA\Response(response="400", description="Board, task or user not found"),
     * )
     */
    public function addUser(Request $request, Board $board, Task $task) {
        $this->authorize('addTaskUser', [$board, $task]);
        $userId = $request->query('userId');
        $response = new TaskResource($this->taskService->addUser($userId, $task));
        return response()->success($response, 'User added successfully', 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/boards/{boardId}/tasks/{taskId}/user",
     *     operationId="deleteTaskUser",
     *     summary="Delete user from the task",
     *     tags={"Task"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="Board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="taskId",
     *         in="path",
     *         required=true,
     *         description="Task id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="User deleted successfully"),
     *     @OA\Response(response="400", description="Board, task or user not found"),
     * )
     * @param Request $request
     * @param Board $board
     * @param Task $task
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ControllerException
     */
    public function deleteUser(Request $request, Board $board, Task $task) {
        $this->authorize('deleteTaskUser', [$board, $task]);
        $userId = $request->query('userId');
        $response = new TaskResource($this->taskService->deleteTaskUser($userId, $task));
        return response()->success($response, 'User deleted successfully', 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/boards/{boardId}/tasks/{taskId}",
     *     operationId="deleteTask",
     *     summary="Delete task",
     *     tags={"Task"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="taskId",
     *         in="path",
     *         required=true,
     *         description="task id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="User deleted successfully"),
     *     @OA\Response(response="400", description="Board, task or user not found"),
     * )
     */
    public function destroy(Board $board, Task $task) {
        $this->authorize('destroyTask', [$board, $task]);
        $this->taskService->destroyTask($task);
        return response()->success([], 'Task is deleted', 200);
    }
    /**
     * @OA\Post(
     *     path="/api/v1/boards/{boardId}/tasks/{taskId}/mark",
     *     operationId="addTaskMark",
     *     summary="Add mark to the task",
     *     tags={"Task"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="taskId",
     *         in="path",
     *         required=true,
     *         description="task id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         description="mark name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Mark added successfully",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/TaskCollection"
     *         )
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
     *         )
     *     ),
     * )
     * @param Board $board
     * @return JsonResponse|Response
     * @throws ControllerException
     */
    public function addMark(Request $request, Board $board, Task $task) {
        $name = $request->query('name');
        $color = $request->query('color');
        $response = new TaskResource($this->taskService->addMark($board, $task, $name));
        return response()->success($response, 'Mark added successfully', 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/boards/{boardId}/tasks/{taskId}/mark",
     *     operationId="deleteTaskMark",
     *     summary="Delete mark from the task",
     *     tags={"Task"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="Board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="taskId",
     *         in="path",
     *         required=true,
     *         description="task id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         description="Mark name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="User deleted from the board successfully",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
     *         )
     *     ),
     * )
     * @param Request $request
     * @param Board $board
     * @param Task $task
     * @return mixed
     */
    public function deleteMark(Request $request, Board $board, Task $task) {
//        $this->authorize('deleteTask', $board);
        $name= $request->query('name');
        $this->taskService->deleteMark($board, $task, $name);
        return response()->success([], 'Mark was deleted from the task', 200);
    }
}
