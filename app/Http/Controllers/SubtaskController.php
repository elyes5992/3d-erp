<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subtask;
use Illuminate\Support\Facades\DB;

class SubtaskController extends Controller
{
    public function store(Request $request, $taskId)
    {
        $request->validate(['content' => 'required|string|max:255']);

        Subtask::create([
            'task_id' => $taskId,
            'content' => $request->content
        ]);

        return back()->with('success', 'Subtask added');
    }

    public function toggle($id)
    {
        DB::update('UPDATE subtasks SET is_done = NOT is_done WHERE id = ?', [$id]);
        return back()->withHeaders(['Cache-Control' => 'no-cache, no-store']);
    }

    public function destroy($id)
    {
        Subtask::findOrFail($id)->delete();
        return back()->with('success', 'Subtask deleted');
    }
}