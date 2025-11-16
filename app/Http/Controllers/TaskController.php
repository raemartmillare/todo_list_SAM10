<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Show all tasks
    public function index()
    {
        $tasks = Task::orderBy('created_at', 'desc')->get();
        $categories = ['New task', 'Scheduled', 'In progress', 'Completed'];
        return view('tasks.index', compact('tasks', 'categories'));
    }

    // Store new task
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        Task::create($request->all());
        return redirect()->back()->with('success', 'Task added successfully!');
    }

    // Update completion
    // Handle category update via drag-and-drop AJAX
public function updateCategory(Request $request, Task $task)
{
    $task->update([
        'category' => $request->category,
    ]);

    return response()->json([
        'message' => 'Task category updated successfully',
        'task' => $task,
    ]);
}


    // Update details
    public function updateDetails(Request $request, Task $task)
    {
        $task->update($request->all());
        return redirect()->back()->with('success', 'Task updated successfully!');
    }

    // Delete
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back()->with('success', 'Task deleted successfully!');
    }
}
