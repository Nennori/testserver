<?php


namespace App\Services;


use App\Exceptions\ControllerException;
use App\Models\Board;
use App\Http\Requests\BoardRequest;
use App\Models\User;
use App\Models\Role;
use App\Models\Status;
use http\Env\Response;
use \Illuminate\Http\JsonResponse;

class BoardService
{
    public function getBoards(User $user): array {
        return $user->boards->all();
    }

    public function createBoard(BoardRequest $request, User $user) {
        $board = new Board;
        $board->fill(['name' => $request->name]);
        $roleId = Role::where('edit_board', '=', true)->firstOrFail()->id;
        $board->user_id = $user->id;
        $user->boards()->save($board, ['role_id' => $roleId]);
        return $board;
    }

//    public function createStatus(string $statusName, Board $board): Status {
//        $status = new Status;
//        $status->name = $statusName;
//        $board->statuses()->save($status);
//        return $status;
//    }

    public function addUser($email, string $role, Board $board) {
        $user = User::where('email', $email)->firstOrFail();
//        if (!$user) {
//            return response()->json(['success' => false, 'message' => 'User not found', 400]);
//        }
        $role = Role::where('name', $role)->firstOrFail();
//        if (!$role) {
//            return response()->json(['success' => false, 'message' => 'Role not found', 400]);
//        }
        $user->boards()->attach($board, ['role_id' => $role->id]);
        return $board;
    }

    public function addStatus(string $newstatus, Board $board) {
        $status = $board->statuses->where('name', $newstatus)->first();
        if($status == null){
            $status=new Status();
            $status->name = $newstatus;
            $board->statuses()->save($status);
        }
        return $status;
//        if ($boardStatus) {
//            return response()->json(['success'=>false, 'message'=>'Status is already exist'], 409);
//        }
//        return $this->createStatus($status, $board);
//        else {
//            $this->createStatus($status, $board);
//            return response()->json(['success' => true], 200);
//        }
    }

    public function deleteStatus(Board $board, string $status) {
        $boardStatus = $board->statuses()->where('name', $status)->firstOrFail();
//        if (!$boardStatus) {
//            return response()->json(['success'=>false, 'message'=>'No such status'], 400);
//        }
        $tasks = $boardStatus->tasks->all();
        if ($tasks) {
            throw new ControllerException('Cant delete status.Tasks are exists', 400);
//            return response()->json(['success'=>false, 'message'=>'Cant delete status.Tasks are exists'], 409);
        }
        $boardStatus->delete();
    }

    public function updateBoard(Board $board, array $data) {
        $board->fill($data)->save();
        return $board;
    }

    public function deleteBoard(Board $board) {
        $board->delete();
    }

    public function deleteUser(User $user, Board $board) {
        $user->boards()->detach($board);
    }

    public function addMark(Board $board, $name, $color)
    {
        $mark = $board->marks()->firstOrNew(['name'=>$name]);
        if(!$mark->exists){
            $mark->name = $name;
            $mark->color = $color;
            $board->marks()->save($mark);
        }
        return $mark;
    }

    public function deleteMark(Board $board, $name)
    {
        try {
            $board->marks()->where('name', $name)->firstOrFail()->delete();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
