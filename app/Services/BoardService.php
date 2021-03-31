<?php


namespace App\Services;


use App\Board;
use App\Http\Requests\BoardRequest;
use App\Role;
use App\Status;
use App\User;
use http\Env\Response;
use \Illuminate\Http\JsonResponse;

class BoardService
{
    public function createBoard(BoardRequest $request, User $user) {
        $board = new Board;
        $board->fill(['name' => $request->name]);
        $roleId = Role::where('edit_board', '=', true)->first()->id;
        $board->user_id = $user->id;
        $user->boards()->save($board, ['role_id' => $roleId]);
        return $board;
    }

    public function createStatus(string $statusName, Board $board): Status {
        $status = new Status;
        $status->name = $statusName;
        $board->statuses()->save($status);
        return $status;
    }

    public function addUser($userId, string $role, Board $board): JsonResponse {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found', 400]);
        }
        $role = Role::where('name', $role)->first();
        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role not found', 400]);
        }
        $user->boards()->attach($board, ['role_id' => $role->id]);
        return response()->json(['success' => true], 200);
    }

    public function addStatus(string $status, Board $board): JsonResponse {
        $boardStatus = $board->statuses->where('name', $status)->first();
        if ($boardStatus) {
            return response()->json(['success'=>false, 'message'=>'Status is already exist'], 409);
        }
        else {
            $this->createStatus($status, $board);
            return response()->json(['success' => true], 200);
        }
    }

    public function deleteStatus(Board $board, string $status): JsonResponse {
        $boardStatus = $board->statuses->where('name', $status)->first();
        if (!$boardStatus) {
            return response()->json(['success'=>false, 'message'=>'No such status'], 400);
        }
        $tasks = $boardStatus->tasks->all();
        if ($tasks) {
            return response()->json(['success'=>false, 'message'=>'Cant delete status.Tasks are exists'], 409);
        }
        $boardStatus->delete();
        return response()->json(['success' => true], 200);
    }

}
