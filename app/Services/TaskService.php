<?php


namespace App\Services;


use App\Http\Requests\TaskRequest;
use App\Jobs\SendEmailJob;
use App\Task;

class TaskService
{
    public function createTask(TaskRequest $request, string $board){
        $boardStatus = $this->findStatus($board, $request->status);
        if (!$boardStatus) {
            return response()->json(['success' => false, 'message' => 'No such status', 400]);
        }
        $taskCount = count($board->tasks) + 1;
        $taskNumber = $this->APIRequest->sendAPIRequest('http://numbersapi.com/', (string)$taskCount . '/trivia');
        $task = new Task;
        $task->fill([
            'task_number' => (string)$taskNumber,
            'name' => $request->name,
            'description' => $request->description,
            'expired_at' => $request->expired_at
        ])->save();
        $task->board()->associate($board);
        $task->status()->associate($boardStatus);
    }

    public function changeStatus(string $boardStatus, Task $task){
        $task->status()->associate($boardStatus);
        $task->save();
    }

    public function findStatus(Board $board, string $status){
        return $board->statuses->where('name', $status)->first();
    }

    public function sendEmails(User $user, string $boardStatus, Task $task, Board $board){
        $data = [
            'newStatus' => $boardStatus->name,
            'owner' => $user->name,
            'task' => $task->name
        ];
        $taskMembers = $board->users->toArray();
        $key = array_search($user->id, array_column($taskMembers, 'id'));
        unset($taskMembers[$key]);
        foreach ($taskMembers as $member) {
            $data['email'] = $member['email'];
            dispatch(new SendEmailJob($data));
        }
    }

    public function changeTask(Request $request, Board $board, Task $task){
        $task = $board->tasks->find($task->id);
        $task->fill($request->all())->save();
        if ($request->has('status')) {
            $boardStatus=findStatus($board, $request->status);
            $this->changeStatus($boardStatus, $task);
        }
        return $task;

    }
}
