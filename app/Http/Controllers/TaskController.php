<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display all tasks with filters, search, and sorting.
     */
    public function index(Request $request)
    {
        $query = Task::query();

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Sorting
        switch ($request->get('sort')) {
            case 'priority':
                $query->orderByRaw("FIELD(priority, 'High', 'Medium', 'Low')");
                break;
            case 'due_date':
                $query->orderBy('due_date', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $tasks = $query->get();
        $categories = Task::select('category')->whereNotNull('category')->distinct()->pluck('category');

        return view('tasks.index', compact('tasks', 'categories'));
    }

    /**
     * Store a new task.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:Low,Medium,High',
            'category' => 'nullable|string|max:255'
        ]);

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
            'category' => $request->category,
        ]);

        return redirect()->back()->with('success', 'Task added successfully!');
    }

    /**
     * Update the completion status of a task (checkbox toggle).
     */
    public function update(Request $request, Task $task)
    {
        // Update completion or category
        $task->update([
            'is_completed' => $request->has('is_completed') ? true : false,
            'category' => $request->category ?? $task->category,
        ]);

        // If it’s an AJAX request (from drag-drop), return JSON
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'category' => $task->category]);
        }

        return redirect()->back()->with('success', 'Task updated!');
    }

    /**
     * Update task details (title, description, due date, etc.).
     */
    public function updateDetails(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:Low,Medium,High',
            'category' => 'nullable|string|max:255'
        ]);

        $task->update($request->only('title', 'description', 'due_date', 'priority', 'category'));

        return redirect('/')->with('success', 'Task updated successfully!');
    }

    /**
     * Delete a task.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back()->with('success', 'Task deleted successfully!');
    }
}
