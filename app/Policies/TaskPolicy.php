<?php

namespace App\Policies;

use App\Board;
use App\Role;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
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

    public function store(User $user, Board $board){
        return ($user->boards()->find($board->id) != null) && (Role::find($user->boards->find($board)->pivot->role_id)->edit_task === true);
    }

    public function changeStatus(User $user, Board $board){
        return ($user->boards()->find($board->id) != null) && (Role::find($user->boards->find($board)->pivot->role_id)->edit_task === true);
    }
}
