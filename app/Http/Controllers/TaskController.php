<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index()
    {
        $overdueTasks = Task::with('subtasks')
            ->where('is_done', 'false')
            ->where('due_date', '<', now()->startOfDay())
            ->orderBy('priority', 'desc')->get();

        $todayTasks = Task::with('subtasks')
            ->where('is_done', 'false')
            ->whereDate('due_date', now())
            ->orderBy('priority', 'desc')->get();

        $upcomingTasks = Task::with('subtasks')
            ->where('is_done', 'false')
            ->where('due_date', '>', now())
            ->orderBy('due_date', 'asc')->get();

        $completedTasks = Task::with('subtasks')
            ->where('is_done', 'true')
            ->latest()->take(10)->get();

        return view('erp.planning.index', compact('overdueTasks', 'todayTasks', 'upcomingTasks', 'completedTasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high'
        ]);

        Task::create([
            'content' => $validated['content'],
            'due_date' => $validated['due_date'],
            'priority' => $validated['priority'],
            'is_done' => false
        ]);

        return back()->with('success', 'Task Created!');
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->update([
            'content' => $request->content,
            'due_date' => $request->due_date,
            'priority' => $request->priority
        ]);
        return back()->with('success', 'Task Updated!');
    }

    public function destroy($id)
    {
        Task::findOrFail($id)->delete();
        return back()->with('success', 'Task Deleted!');
    }

    public function toggle($id)
    {
        DB::update('UPDATE tasks SET is_done = NOT is_done WHERE id = ?', [$id]);
        
        return back()->withHeaders([
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}