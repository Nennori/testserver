<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\BoardRequest;
use App\Http\Requests\StatusRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Board;
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
     *     path="api/v1/boards",
     *     @OA\Parameter(
     *         name="Bearer Token",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="int")
     *     ),
     *     summary="Displays a listing of user's boards",
     *     @OA\Response(response="200", description="List of user's boards.")
     * )
     */

    public function index() {
        return $this->boardService->getBoards(auth()->user());
    }

    /**
     * @OA\Post(
     *     path="api/v1/boards",
     *     summary="Add new user board",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         description="name of the new board",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Bearer Token",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="201", description="Create board successfully"),
     *     @OA\Response(response="404", description="Validation error"),
     *     @OA\Response(response="404", description="Board not addes")
     * )
     * @param BoardRequest $request
     * @return JsonResponse
     */

    public function store(BoardRequest $request): JsonResponse
    {
        $board = $this->boardService->createBoard($request, auth()->user());
        if ($board) {
            return $this->sendSuccess($board->toArray(), 'Board created successfully', 201);
        }
        else {
            return $this->sendError('Board not added', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="api/v1/boards/{boardId}",
     *     summary="Update user board",
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         description="board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="new name of the board",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Bearer Token",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="400", description="Board not found"),
     *     @OA\Response(response="200", description="Board updated successfully"),
     *     @OA\Response(response="500", description="Board not updated"),
     *     @OA\Response(response="403", description="User have hot permission to update the board"),
     *     @OA\Response(response="404", description="Validation error")
     * )
     * @param BoardRequest $request
     * @param Board $board
     * @return Response
     * @throws AuthorizationException
     */

    public function update(BoardRequest $request, Board $board) {
        $this->authorize('update', $board);
        return $this->boardService->updateBoard($board, $request->all());
    }

    /**
     * @OA\Post(
     *     path="api/v1/boards/{boardId}/user",
     *     summary="Add user to board",
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="user id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Bearer Token",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="400", description="Board or user not found"),
     *     @OA\Response(response="200", description="User added to the board successfully"),
     *     @OA\Response(response="403", description="User have hot permission to add user to this board")
     *
     * )
     * @param Request $request
     * @param Board $board
     * @return mixed
     * @throws AuthorizationException
     */

    public function addUser(Request $request, Board $board) {
        $userId = $request->userId;
        $role = $request->role;
        $this->authorize('addUser', $board);
        return $this->boardService->addUser($userId, $role, $board);
    }

    /**
     * @OA\Post(
     *     path="api/v1/boards/{boardId}/status",
     *     summary="Add status to board",
     *     @OA\Parameter(
     *         name="board",
     *         in="path",
     *         required=true,
     *         description="board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="path",
     *         required=true,
     *         description="name of status",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Bearer Token",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="int")
     *     ),
     *     @OA\Response(response="400", description="Board not found"),
     *     @OA\Response(response="409", description="Status is already exist"),
     *     @OA\Response(response="200", description="Status added to the board successfully"),
     *     @OA\Response(response="403", description="User have hot permission to add status to this board"),
     *     @OA\Response(response="404", description="Validation error")
     * )
     * @param StatusRequest $request
     * @param $board
     * @return JsonResponse
     * @throws AuthorizationException
     */

    public function addStatus(StatusRequest $request, Board $board): JsonResponse {
        $status = $request->query('status');
        $this->authorize('addStatus', $board);
        return $this->boardService->addStatus($status, $board);
    }

    /**
     * @OA\Delete(
     *     path="api/v1/boards/{boardId}/status",
     *     summary="Delete status from user's board",
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=true,
     *         description="name of status",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Bearer Token",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="400", description="Board or status not found"),
     *     @OA\Response(response="409", description="Status cannot be deleted because there are tasks that have it"),
     *     @OA\Response(response="200", description="Status deleted from the board successfully"),
     *     @OA\Response(response="403", description="User have hot permission to add status to this board")
     *
     * )
     * @param Request $request
     * @param Board $board
     * @return mixed
     * @throws AuthorizationException
     */

    public function deleteStatus(Request $request, Board $board) {
        $status = $request->status;
        $this->authorize('deleteStatus', $board);
        return $this->boardService->deleteStatus($board, $status);
    }

    /**
     * @OA\Delete(
     *     path="api/v1/boards/{boardId}",
     *     summary="Delete user's board",
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Bearer Token",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="400", description="Board not found"),
     *     @OA\Response(response="200", description="Board deleted successfully"),
     *     @OA\Response(response="403", description="User have hot permission to delete board")
     * )
     * @param Board $board
     * @return JsonResponse|Response
     */

    public function destroy(Board $board) {
        $this->authorize('delete', $board);
        return $this->deleteBoard($board);
    }

    /**
     * @OA\Delete(
     *     path="api/v1/boards/{boardId}/user",
     *     summary="Exit from board",
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="board id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Bearer Token",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="400", description="Board not found"),
     *     @OA\Response(response="500", description="Board not deleted"),
     *     @OA\Response(response="200", description="User deleted from the board successfully")
     *
     * )
     */

    public function deleteUser(Board $board) {
        $this->authorize('deleteUser', $board);
        return $this->boardService->deleteUser(auth()->user(), $board);
    }
}
