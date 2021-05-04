<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
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

    public function changeStatus(User $user,  Task $task, Board $board) {
        return (Role::find($user->boards->find($board->id)->pivot->role_id)->edit_task === true) &&
            ($board->id === $task->board_id);
    }

    public function updateTask(User $user, Task $task, Board $board) {
        return (Role::find($user->boards->find($board->id)->pivot->role_id)->edit_task === true) &&
            ($board->id === $task->board_id);
    }

    public function addTaskUser(User $user, Task $task, Board $board) {
        return (Role::find($user->boards->find($board->id)->pivot->role_id)->edit_task === true) &&
            ($board->id === $task->board_id);
    }

    public function deleteTaskUser(User $user, Task $task,Board $board) {
        return (Role::find($user->boards->find($board->id)->pivot->role_id)->edit_task === true) &&
            ($board->id === $task->board_id);
    }
}
