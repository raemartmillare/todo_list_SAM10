<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kanban To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/todo.css') }}">
</head>
<body>
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="board-header">
        <div class="board-title">
            📁 <span>My Task Board</span>
        </div>
    </div>

    {{-- Add Task Form --}}
    <div class="text-center mb-4">
        <form action="{{ route('tasks.store') }}" method="POST" class="d-inline-flex gap-2 flex-wrap justify-content-center">
            @csrf
            <input type="text" name="title" placeholder="Task title" required class="form-control w-auto">
            <select name="priority" class="form-select w-auto">
                <option>Low</option>
                <option>Medium</option>
                <option>High</option>
            </select>
            <input type="date" name="due_date" class="form-control w-auto">
            <select name="category" class="form-select w-auto">
                <option>New task</option>
                <option>Scheduled</option>
                <option>In progress</option>
                <option>Completed</option>
            </select>
            <button class="btn btn-primary">Add</button>
        </form>
    </div>

    {{-- Kanban Board --}}
    <div class="kanban-board">
        @php
            $columns = ['New task', 'Scheduled', 'In progress', 'Completed'];
        @endphp

        @foreach ($columns as $column)
            <div class="kanban-column" id="{{ Str::slug($column) }}">
                <h5 class="kanban-header">{{ $column }}</h5>

                @foreach ($tasks->where('category', $column) as $task)
                    <div class="kanban-card
                        @if($task->priority=='High') priority-high
                        @elseif($task->priority=='Medium') priority-medium
                        @else priority-low
                        @endif"
                        data-task-id="{{ $task->id }}">
                        
                        {{-- Task Info (No labels) --}}
                        <div class="task-info">
                            <p class="task-value fw-semibold">{{ $task->title }}</p>

                            @if($task->due_date)
                            <p class="task-value text-muted">
                                <i class="bi bi-calendar-date me-1"></i>
                                {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                            </p>
                            @endif
                        </div>

                        {{-- Buttons beside each other --}}
                        <div class="task-btns">
                            {{-- Edit --}}
                            <button class="btn btn-sm btn-outline-secondary edit-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $task->id }}"
                                title="Edit">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </button>

                            {{-- Delete --}}
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger delete-btn" title="Delete">
                                    <i class="bi bi-x-lg me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Edit Modal --}}
                    <div class="modal fade" id="editModal{{ $task->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('tasks.updateDetails', $task) }}" method="POST" class="modal-content">
                                @csrf @method('PATCH')
                                <div class="modal-header">
                                    <h5 class="modal-title fw-semibold">Edit Task</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="title{{ $task->id }}" class="form-label">Task Title</label>
                                        <input type="text" id="title{{ $task->id }}" name="title" class="form-control" value="{{ $task->title }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="due_date{{ $task->id }}" class="form-label">Due Date</label>
                                        <input type="date" id="due_date{{ $task->id }}" name="due_date" class="form-control" value="{{ $task->due_date }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="priority{{ $task->id }}" class="form-label">Priority</label>
                                        <select id="priority{{ $task->id }}" name="priority" class="form-select">
                                            <option {{ $task->priority=='Low'?'selected':'' }}>Low</option>
                                            <option {{ $task->priority=='Medium'?'selected':'' }}>Medium</option>
                                            <option {{ $task->priority=='High'?'selected':'' }}>High</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="category{{ $task->id }}" class="form-label">Category</label>
                                        <select id="category{{ $task->id }}" name="category" class="form-select">
                                            <option {{ $task->category=='New task'?'selected':'' }}>New task</option>
                                            <option {{ $task->category=='Scheduled'?'selected':'' }}>Scheduled</option>
                                            <option {{ $task->category=='In progress'?'selected':'' }}>In progress</option>
                                            <option {{ $task->category=='Completed'?'selected':'' }}>Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-success px-4">Save</button>
                                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const columns = document.querySelectorAll('.kanban-column');

    columns.forEach(column => {
        new Sortable(column, {
            group: 'shared',
            animation: 150,
            onAdd: function (evt) {
                const card = evt.item;
                const taskId = card.getAttribute('data-task-id');
                const newCategory = column.querySelector('.kanban-header').textContent.trim();

                fetch(`/tasks/${taskId}/category`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ category: newCategory })
            })
            .then(res => res.json())
            .then(data => console.log('Category updated:', data))
            .catch(err => console.error('Error:', err));

            }
        });
    });
});
</script>
</body>
</html>
