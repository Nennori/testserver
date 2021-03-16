<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Board;
use App\User;
use App\Status;
use App\Role;
use Validator;

class BoardController extends BaseController
{
    /**
 * @OA\Get(
 *     path="api/v1/boards",
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="int")
 *     ),
 *     summary="Displays a listing of user's boards.",
 *     @OA\Response(response="200", description="List of user's boards.")
 * 
 * )
 */
    public function index()
    {
        $boards = auth()->user()->boards->toArray();
        // $boards = Board::where('user_id', auth()->user()->id)->get();
        return $this->sendResponse($boards, 'Boards retrieved successfully');
    }
 /**
 * @OA\Post(
 *     path="api/v1/boards",
 *     summary="Add new user board.",
 *     @OA\Parameter(
 *     name="name",
 *     in="query",
 *     required=true,
 *     description="name of the new board",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="200", description="Create board successfully"),
 *     @OA\Response(response="404", description="Validation error")
 * 
 * )
 */
    public function store(Request $request){
        $validator = Validator::make($request->all(), ['name'=>'required|min:2|max:40']);
        if($validator->fails()){
            return $this->sendError('Validation Error', 404);
        }
        $board = new Board;
        $board->name = $request->name;
        $board->user_id = auth()->user()->id;
        if($board->save()){
            auth()->user()->boards()->attach($board);
            return $this->sendResponse($board->toArray(), 'Board created successfully'); 
        }
        else{
            return $this->sendError('Board not added', 500);
        }
    }
/**
 * @OA\Put(
 *     path="api/v1/boards/{boardId}",
 *     summary="Update user board.",
 *     @OA\Parameter(
 *      name="boardId",
 *      in="path",
 *      description="board id",
 *      @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *      name="name",
 *      in="query",
 *      description="new name of the board",
 *      @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="400", description="Board not found"),
 *     @OA\Response(response="200", description="Board updated successfully"),
 *     @OA\Response(response="500", description="Board not updated"),
 *     @OA\Response(response="403", description="User have hot permission to update the board"),
 *     @OA\Response(response="404", description="Validation error")
 * )
 */
    public function update(Request $request, string $boardId){
        $board = auth()->user()->boards->find($boardId);
        if(!$board){
            return $this->sendError('Board not found', 400);
        }
        $validator = Validator::make($request->all(), ['name'=>'required|min:2|max:40']);
        if($validator->fails()){
            return $this->sendError('Validation Error', 404);
        }
        if($board->user_id === auth()->user()->id){
            $board->fill($request->all());
            if($board->save()){
                return $this->sendResponse($board->toArray(), 'Board updated successfully');
            }
            else{
                return $this->sendError('Board not updated', 500);
            }
        }
        else{
            return $this->sendError('Forbidden', 403);
        }
        
    }
/**
 * @OA\Post(
 *     path="api/v1/boards/{boardId}/add-user",
 *     summary="Add user to board.",
 *     @OA\Parameter(
 *     name="boardId",
 *     in="path",
 *     required=true,
 *     description="board id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="userId",
 *     in="path",
 *     required=true,
 *     description="user id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="400", description="Board or user not found"),
 *     @OA\Response(response="200", description="User added to the board successfully"),
 *     @OA\Response(response="403", description="User have hot permission to add user to this board")
 * 
 * )
 */
    public function addUser(Request $request, string $boardId){
        $userId = $request->userId;
        $role = $request->role;
        $board = auth()->user()->boards->find($boardId);
        if(!$board){
            return $this->sendError('Board not found', 400);
        }
        if($board->user_id === auth()->user()->id){
            $user = User::find($userId);
            if(!$user){
                return $this->sendError('User not found', 400);
            }
            $role = Role::where('name', $role)->first();
            if(!$role){
                return $this->sendError('Role not found', 400);
            }
            $user->boards()->attach($board, ['role_id' => $role->id]);
            return response()->json(['success'=>true], 200);
        }
        else{
            return $this->sendError('Forbidden', 403);
        }
    }
/**
 * @OA\Post(
 *     path="api/v1/boards/{boardId}/add-status",
 *     summary="Add status to board.",
 *     @OA\Parameter(
 *     name="boardId",
 *     in="path",
 *     required=true,
 *     description="board id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="status",
 *     in="path",
 *     required=true,
 *     description="name of status",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="int")
 *     ),
 *     @OA\Response(response="400", description="Board not found"),
 *     @OA\Response(response="409", description="Status is already exist"),
 *     @OA\Response(response="200", description="Status added to the board successfully"),
 *     @OA\Response(response="403", description="User have hot permission to add status to this board"),
 *     @OA\Response(response="404", description="Validation error")
 * 
 * )
 */
    public function addStatus(Request $request, string $boardId){
        $status = $request->query('status');
        $validator = Validator::make($request->all(), ['status'=>'required|min:2|max:30']);
        if($validator->fails()){
            return $this->sendError('Validation Error', 404);
        }
        $board = auth()->user()->boards->find($boardId);
        if(!$board){
            return $this->sendError('Board not found', 400);
        }
        if($board->user_id === auth()->user()->id){
            $boardStatus = $board->statuses->where('name', $status)->first();
        if($boardStatus){
            return $this->sendError('Status is already exist', 409);
        }
        else{
            $newStatus = new Status;
            $newStatus->name = $status;
            $board->statuses()->save($newStatus);
            return response()->json([
                'success' => true
            ], 200);
        }    
        }
        else{
            return $this->sendError('Forbidden', 403);
        }
    }
/**
 * @OA\Delete(
 *     path="api/v1/boards/{boardId}/delete-status",
 *     summary="Delete status from user's board.",
 *     @OA\Parameter(
 *     name="boardId",
 *     in="path",
 *     required=true,
 *     description="board id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="status",
 *     in="query",
 *     required=true,
 *     description="name of status",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="400", description="Board or status not found"),
 *     @OA\Response(response="409", description="Status cannot be deleted because there are tasks that have it"),
 *     @OA\Response(response="200", description="Status deleted from the board successfully"),
 *     @OA\Response(response="403", description="User have hot permission to add status to this board")
 * 
 * )
 */
    public function deleteStatus(Request $request, string $boardId){
        $status = $request->status;
        $board = auth()->user()->boards->find($boardId);
        if(!$board){
            return $this->sendError('Board not found', 400);
        }
        if($board->user_id === auth()->user()->id){
            $boardStatus = $board->statuses->where('name', $status)->first();
            if(!$boardStatus){
                return $this->sendError('No such status', 400);
            }
            $tasks = $boardStatus->tasks->all();
            if($tasks){
                return $this->sendError('Cant delete status.Tasks are exists', 409);
            }
            $boardStatus->delete();
            return response()->json([
                'success' => true
            ], 200);
        }
        else{
            return $this->sendError('Forbidden', 403);
        }
    }
/**
 * @OA\Delete(
 *     path="api/v1/boards/{boardId}",
 *     summary="Delete user's board.",
 *     @OA\Parameter(
 *     name="boardId",
 *     in="path",
 *     required=true,
 *     description="board id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="400", description="Board not found"),
 *     @OA\Response(response="200", description="Board deleted successfully"),
 *     @OA\Response(response="403", description="User have hot permission to delete board") 
 * )
 */
    public function destroy(string $boardId){
        $board = auth()->user()->boards->find($boardId);
        if(!$board){
            return $this->sendError('Board not found', 400);
        }
        if($board->user_id === auth()->user()->id){
            $board->delete();
            return response()->json([
                'success' => true
            ]);
        }
        else{
            return $this->sendError('Forbidden', 403);
        }
    }
/**
 * @OA\Delete(
 *     path="api/v1/boards/{boardId}/delete-user",
 *     summary="Exit from board.",
 *     @OA\Parameter(
 *     name="boardId",
 *     in="path",
 *     required=true,
 *     description="board id",
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *     name="Bearer Token",
 *     in="header",
 *     required=true,
 *     @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="400", description="Board not found"),
 *     @OA\Response(response="500", description="Board not deleted"),
 *     @OA\Response(response="200", description="User deleted from the board successfully")
 * 
 * )
 */
    public function deleteUser(string $boardId){
        $board = auth()->user()->boards->find($boardId);
        if(!$board){
            return $this->sendError('Board not found', 400);
        }
        if(auth()->user()->boards()->detach($board)){
            return response()->json(['success'=>true], 200);
        }
        else{
            return $this->sendError('Board not deleted', 500);
        }
    }
}
