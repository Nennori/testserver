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

    /**
     * @param User $user
     * @param Board $board
     * @return bool
     */

    public function getTask(User $user, Board $board) {
        return ($user->boards()->find($board->id) != null) &&
            (Role::find($user->boards->find($board->id)->pivot->role_id)->edit_task === true);
    }

    public function storeTask(User $user, Board $board) {
        return ($user->boards()->find($board->id) != null) &&
            (Role::find($user->boards->find($board)->pivot->role_id)->edit_task === true);
    }

    public function getTasks(User $user, Board $board) {
        return ($user->boards()->find($board->id) != null) &&
            (Role::find($user->boards->find($board->id)->pivot->role_id)->edit_task === true);
    }

    public function changeStatus(User $user, Board $board, Task $task) {
        return ($user->boards()->find($board->id) != null) &&
            (Role::find($user->boards->find($board)->pivot->role_id)->edit_task === true) &&
            ($board->id === $task->board_id);
    }

    public function updateTask(User $user, Board $board, Task $task) {
        return ($user->boards()->find($board->id) != null) &&
            (Role::find($user->boards->find($board)->pivot->role_id)->edit_task === true) &&
            ($board->id === $task->board_id);
    }

    public function addTaskUser(User $user, Board $board, Task $task) {
        return ($user->boards()->find($board->id) != null) &&
            (Role::find($user->boards->find($board)->pivot->role_id)->edit_task === true) &&
            ($board->id === $task->board_id);
    }

    public function deleteTaskUser(User $user, Board $board, Task $task) {
        return ($user->boards()->find($board->id) != null) &&
            (Role::find($user->boards->find($board)->pivot->role_id)->edit_task === true) &&
            ($board->id === $task->board_id);
    }
}
