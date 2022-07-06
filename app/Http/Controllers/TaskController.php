<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function store() {
        $attr = request()->validate([
            'title' => ['required', 'max:255']
        ]);

        $attr['user_id'] = request()->user()->id;
        $task = Task::create($attr);
        return [
            'task' => new TaskResource($task)
        ];
    }

    public function index() {
        $tasks = Task::where(['user_id' => request()->user()->id]);
        if((request('q') == '0' || request('q') == '1') ?? false) {
            $tasks->where(function ($query) {
                $query->orWhere('completed', request('q'));
            });
        }
        return [
            'tasks' => new TaskCollection($tasks->paginate())
        ];
    }

    public function show(Task $task) {
        $this->authorize('view', $task);
        return [
            'task' => new TaskResource($task)
        ];
    }

    public function update(Task $task) {
        $attr = request()->validate([
            'title' => ['sometimes','required', 'max:255'],
            'completed' => ['sometimes','required', Rule::in(['0', '1'])]
        ]);
        $task->update($attr);
        return [
            'task' => new TaskResource($task)
        ];
    }

    public function destroy(Task $task) {
        $task->delete();
        return [
            'msg' => 'Task got deleted.'
        ];
    }

    public function destroyAll() {
        $taskIds = request()->user()->tasks->map(function($task) {
            return $task['id'];
        });
        Task::destroy($taskIds);
        return [
            'msg' => 'Your tasks got deleted.'
        ];
    }
}
