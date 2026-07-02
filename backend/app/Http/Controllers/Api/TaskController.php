<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Task::with(['assignee:id,name,email']);

        if (! $user->isAdmin()) {
            $query->where('assigned_to', $user->id);
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        $task = Task::create([
            ...$data,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($task->load('assignee:id,name,email'), 201);
    }

    public function update(Request $request, Task $task)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $data = $request->validate([
                'title' => ['sometimes', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'due_date' => ['nullable', 'date'],
                'assigned_to' => ['sometimes', 'exists:users,id'],
                'status' => ['sometimes', 'in:pending,completed'],
            ]);

            $task->update($data);

            return response()->json($task->load('assignee:id,name,email'));
        }

        if ($task->assigned_to !== $user->id) {
            abort(403, 'Forbidden.');
        }

        $data = $request->validate([
            'status' => ['required', 'in:pending,completed'],
        ]);

        $task->update($data);

        return response()->json($task->load('assignee:id,name,email'));
    }
}
