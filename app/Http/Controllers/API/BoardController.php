<?php

namespace App\Http\Controllers\API;

use App\Exceptions\ControllerException;
use App\Http\Requests\BoardRequest;
use App\Http\Requests\StatusRequest;
use App\Http\Resources\BoardCollection;
use App\Http\Resources\BoardResource;
use App\Http\Resources\MarkResource;
use App\Http\Resources\StatusCollection;
use App\Http\Resources\StatusResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Board;
use Validator;
use \Illuminate\Http\Response;
use App\Services\BoardService;

class BoardController extends BaseController
{
    protected $boardService;

    public function __construct(BoardService $boardService) {
        $this->boardService = $boardService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/boards",
     *     operationId="getBoards",
     *     summary="Displays a listing of user's boards",
     *     tags={"Board"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Response(
     *         response="200",
     *         description="List of user's boards",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/BoardCollection"
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
     */
    public function index() {
        $response = new BoardCollection($this->boardService->getBoards(auth()->user()));
        return response($response, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/boards",
     *     operationId="addBoard",
     *     summary="Add new board",
     *     tags={"Board"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         description="Name of the new board",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Board created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 title="data",
     *                 description="Response data",
     *                 type="array",
     *                 @OA\Items(
     *                     ref="#/components/schemas/BoardResource"
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
     * @param BoardRequest $request
     * @return JsonResponse
     */
    public function store(BoardRequest $request) {
        return response()->success(new BoardResource($this->boardService->createBoard($request, auth()->user())), "Board created successfully", 200);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/boards/{boardId}",
     *     operationId="updateBoard",
     *     summary="Update board",
     *     tags={"Board"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         description="Board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="New name of the board",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Board updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 title="data",
     *                 description="Response data",
     *                 type="array",
     *                 @OA\Items(
     *                     ref="#/components/schemas/BoardResource"
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
     *         response="403",
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Validation error",
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
     * @param BoardRequest $request
     * @param Board $board
     * @return Response
     * @throws AuthorizationException
     */
    public function update(BoardRequest $request, Board $board) {
        $this->authorize('updateBoard', $board);
        return response()->success(new BoardResource($this->boardService->updateBoard($board, $request->all())), 'Board updated successfully', 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/boards/{boardId}/user",
     *     operationId="addBoardUser",
     *     summary="Add user to the board",
     *     tags={"Board"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="Board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass user and role",
     *         @OA\JsonContent(
     *             required={"email", "role"},
     *             @OA\Property(
     *                 property="email",
     *                 description="User email",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="role",
     *                 description="User role at the board",
     *                 type="string"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="User added to the board successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 title="data",
     *                 description="Response data",
     *                 type="array",
     *                 @OA\Items(
     *                     ref="#/components/schemas/BoardResource"
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
     *     @OA\Response(
     *         response="404",
     *         description="Not found",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
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
     * @param Request $request
     * @param Board $board
     * @return mixed
     * @throws AuthorizationException
     */
    public function addUser(Request $request, Board $board) {
        $email = $request->email;
        $role = $request->role;
        $this->authorize('addUser', $board);
        return  response()->success(new BoardResource($this->boardService->addUser($email, $role, $board)), 'User added to the board successfully', 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/boards/{boardId}/status",
     *     operationId="addStatus",
     *     summary="Add status to the board",
     *     tags={"Board"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="Board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=true,
     *         description="Name of status",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Status added to the board successfully",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/StatusCollection"
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Validation error",
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
     * @param StatusRequest $request
     * @param $board
     * @return Application|ResponseFactory|Response
     * @throws ControllerException
     */
    public function addStatus(StatusRequest $request, Board $board) {
        $status = $request->query('status');
        $this->authorize('addStatus', $board);
        return response()->success(new StatusResource($this->boardService->addStatus($status, $board)), 'New status added to the board successfully', 200);
//        return response($response, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/boards/{boardId}/status",
     *     operationId="deleteStatus",
     *     summary="Delete status from user's board",
     *     tags={"Board"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="Board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=true,
     *         description="Name of status",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Status deleted from the board successfully",
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
     *         response="403",
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Status cannot be deleted because there are tasks",
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
     * @return mixed
     * @throws AuthorizationException|ControllerException
     */
    public function deleteStatus(Request $request, Board $board) {
        $status = $request->status;
        $this->authorize('deleteStatus', $board);
        $this->boardService->deleteStatus($board, $status);
        return response()->success([], 'Status deleted successfully', 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/boards/{boardId}",
     *     operationId="deleteBoard",
     *     summary="Delete user's board",
     *     tags={"Board"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="Board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Board deleted successfully",
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
     * @throws AuthorizationException
     */
    public function destroy(Board $board) {
        $this->authorize('deleteBoard', $board);
        $this->boardService->deleteBoard($board);
        return response()->success([], 'Board deleted successfully', 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/boards/{boardId}/user",
     *     operationId="deleteUserFromBoard",
     *     summary="Exit from board",
     *     tags={"Board"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="Board id",
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
     */
    public function deleteUser(Board $board) {
        $this->authorize('deleteUser', $board);
        $this->boardService->deleteUser(auth()->user(), $board);
        return response()->success([], 'User was deleted from the board', 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/boards/{boardId}/status",
     *     operationId="getStatuses",
     *     summary="Get list of statuses",
     *     tags={"Board"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Board deleted successfully",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/StatusCollection"
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Not found",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
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
    public function getStatuses(Board $board) {
        $response = new StatusCollection($board->statuses->all());
        return response()->success($response, 'List of statuses retrieved', 200);
//        return $this->sendSuccess($board->statuses->all(), 'List of statuses received', 200);
    }
    /**
     * @OA\Post(
     *     path="/api/v1/boards/{boardId}/mark",
     *     operationId="addMark",
     *     summary="Add mark",
     *     tags={"Board"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         description="mark name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="color",
     *         in="query",
     *         required=true,
     *         description="mark color",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Mark added successfully",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/MarkCollection"
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
    public function addMark(Request $request, Board $board) {
        $name = $request->query('name');
        $color = $request->query('color');
        $response = new MarkResource($this->boardService->addMark($board, $name, $color));
        return response()->success($response, 'Mark added successfully', 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/boards/{boardId}/mark",
     *     operationId="deleteMark",
     *     summary="Delete board mark",
     *     tags={"Board"},
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
     */
    public function deleteMark(Request $request, Board $board) {
        $this->authorize('deleteUser', $board);
        $name= $request->query('name');
        $this->boardService->deleteMark($board, $name);
        return response()->success([], 'Mark was deleted from the board', 200);
    }
}
