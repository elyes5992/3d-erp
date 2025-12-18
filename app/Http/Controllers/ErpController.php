<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Task;
use App\Models\Setting;
use App\Models\Subtask;

use Illuminate\Support\Facades\DB;

class ErpController extends Controller
{
    // --- DASHBOARD ---
    public function dashboard()
    {
        $products = Product::all();

        // Exact counts for the 4 Kanban columns
        $stats = [
            'idea'         => $products->where('status', 'Idea')->count(),
            'approved'     => $products->where('status', 'Approved')->count(),
            'design_ready' => $products->where('status', 'Design Ready')->count(),
            'printed'      => $products->where('status', 'Printed')->count(),
        ];

        // Recent activities
        $recentProducts = Product::with('category')->latest()->take(5)->get();
        $recentTasks = Task::latest()->take(5)->get();

        $journal = Setting::firstOrCreate(
            ['key' => 'team_journal'],
            ['value' => '']
        );

        return view('erp.dashboard', compact('stats', 'recentProducts', 'recentTasks', 'journal'));
    }

    // --- PRODUCTS ---
    public function products()
    {
        $products = Product::with('category')->latest()->get();
        $categories = Category::all();
        return view('erp.products.index', compact('products', 'categories'));
    }

    public function storeProduct(Request $request)
    {
        // 1. Validate inputs (removed 'status' from validation)
        $validated = $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'pinterest_url' => 'nullable|url',
            'description' => 'nullable',
            'remarks' => 'nullable',
            'cost' => 'nullable|numeric'
        ]);

        // 2. Force default status
        $validated['status'] = 'Idea';

        Product::create($validated);

        return back()->with('success', 'Product Added!');
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // 1. Validate inputs (removed 'status' - we generally change status via Kanban now)
        $validated = $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'pinterest_url' => 'nullable|url',
            'description' => 'nullable',
            'remarks' => 'nullable',
            'cost' => 'nullable|numeric'
        ]);

        // Note: We do NOT update status here. 
        // If you want to allow status editing in the modal, add 'status' => 'nullable' 
        // to validation and check if $request->status exists. 
        // For now, we assume status is only moved via Kanban.

        $product->update($validated);

        return back()->with('success', 'Product Updated!');
    }

    public function destroyProduct($id)
    {
        Product::findOrFail($id)->delete();
        return back()->with('success', 'Product Deleted!');
    }

    // --- CATEGORIES ---
    public function categories()
    {
        $categories = Category::withCount('products')->get();
        return view('erp.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        Category::create($request->validate([
            'name' => 'required|unique:categories,name',
            'color' => 'nullable'
        ]));
        return back()->with('success', 'Category Created!');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->validate([
            'name' => 'required|unique:categories,name,' . $id,
            'color' => 'nullable'
        ]));
        return back()->with('success', 'Category Updated!');
    }

    public function destroyCategory($id)
    {
        Category::findOrFail($id)->delete();
        return back()->with('success', 'Category Deleted!');
    }

    // --- PLANNING / TASKS ---
    public function planning()
    {
        // 1. Overdue
        $overdueTasks = Task::with('subtasks') // <--- ADD THIS
            ->where('is_done', 'false')
            ->where('due_date', '<', now()->startOfDay())
            ->orderBy('priority', 'desc')
            ->get();

        // 2. Today
        $todayTasks = Task::with('subtasks') // <--- ADD THIS
            ->where('is_done', 'false')
            ->whereDate('due_date', now())
            ->orderBy('priority', 'desc')
            ->get();

        // 3. Upcoming
        $upcomingTasks = Task::with('subtasks') // <--- ADD THIS
            ->where('is_done', 'false')
            ->where('due_date', '>', now())
            ->orderBy('due_date', 'asc')
            ->get();

        // 4. Completed
        $completedTasks = Task::with('subtasks') // <--- ADD THIS
            ->where('is_done', 'true')
            ->latest()
            ->take(10)
            ->get();

        return view('erp.planning.index', compact('overdueTasks', 'todayTasks', 'upcomingTasks', 'completedTasks'));
    }

   public function storeTask(Request $request)
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
            // REMOVED 'is_done' => false
            // We let the database default (which is false) handle this.
        ]);

        return back()->with('success', 'Task Created!');
    }

    public function updateTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->update([
            'content' => $request->content,
            'due_date' => $request->due_date,
            'priority' => $request->priority // New field
        ]);
        return back()->with('success', 'Task Updated!');
    }

    

    public function destroySubtask($id)
    {
        Subtask::findOrFail($id)->delete();
        return back()->with('success', 'Subtask deleted');
    }

    public function updateJournal(Request $request)
    {
        Setting::updateOrCreate(
            ['key' => 'team_journal'],
            ['value' => $request->content]
        );

        return back()->with('success', 'Shared journal updated!');
    }

    public function toggleTask($id)
    {
        // 1. Direct SQL update to flip the boolean in Postgres
        // "NOT is_done" turns true->false and false->true
        DB::update('UPDATE tasks SET is_done = NOT is_done WHERE id = ?', [$id]);

        // 2. Return back with headers that force the browser to NOT use cache
        return back()->withHeaders([
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function kanban()
    {
        // Define your exact stages here
        $columns = ['Idea', 'Approved', 'Design Ready', 'Printed'];

        // Get all products
        $products = Product::all();

        return view('erp.kanban.index', compact('columns', 'products'));
    }

    public function updateKanbanStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'status' => 'required'
        ]);

        $product = Product::find($request->id);
        $product->status = $request->status;
        $product->save();

        return response()->json(['message' => 'Status Updated']);
    }

    public function destroyTask($id)
    {
        Task::findOrFail($id)->delete();
        return back()->with('success', 'Task Deleted!');
    }
}
