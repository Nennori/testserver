<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Models\Board;
use Illuminate\Auth\Access\HandlesAuthorization;

class BoardPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function storeTask(User $user, Board $board) {
        return (Role::find($user->boards->find($board->id)->pivot->role_id)->edit_task === true);
    }

    public function getTask(User $user, Board $board) {
           return (Role::find($user->boards->find($board->id)->pivot->role_id)->edit_task === true);
    }

    public function updateBoard(User $user, Board $board) {
        return ($user->boards->find($board->id)!==null) && (Role::find($user->boards->find($board)->pivot->role_id)->edit_board === true);;
    }

    public function addUser(User $user, Board $board) {
        return ($user->boards()->find($board->id) != null) && (Role::find($user->boards->find($board)->pivot->role_id)->edit_board === true);;
    }

    public function addStatus(User $user, Board $board) {
        return ($user->boards->find($board->id) != null) && (Role::find($user->boards->find($board)->pivot->role_id)->edit_board === true);;
    }

    public function deleteStatus(User $user, Board $board) {
        return ($user->boards()->find($board->id) != null) && (Role::find($user->boards->find($board)->pivot->role_id)->edit_board === true);;
    }

    public function deleteUser(User $user, Board $board) {
        return ($user->boards()->find($board->id) != null);
    }

    public function deleteBoard(User $user, Board $board){
        return ($user->boards()->find($board->id) != null) && (Role::find($user->boards->find($board)->pivot->role_id)->edit_board === true);
    }

    public function getBoard(User $user, Board $board){
        return $user->boards()->find($board->id) != null;
    }

}
