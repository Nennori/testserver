<?php

namespace App\Policies;

use App\Role;
use App\User;
use App\Board;
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

    public function update(User $user, Board $board) {
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

    public function delete(User $user, Board $board){
        return ($user->boards()->find($board->id) != null) && (Role::find($user->boards->find($board)->pivot->role_id)->edit_board === true);
    }

    public function index(User $user, Board $board){
        return $user->boards()->find($board->id) != null;
    }

}
