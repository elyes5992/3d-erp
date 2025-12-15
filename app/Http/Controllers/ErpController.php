<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Task;
use App\Models\Setting; 

use Illuminate\Support\Facades\DB;

class ErpController extends Controller
{
    // --- DASHBOARD ---
    public function dashboard()
    {
        // Stats for dashboard
        $products = Product::all();
        $stats = [
            'total' => $products->count(),
            'printing' => $products->where('status', 'Printing')->count(),
            'ideas' => $products->where('status', 'Idea')->count(),
            'done' => $products->where('status', 'Done')->count(),
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
        Product::create($request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'pinterest_url' => 'nullable|url',
            'description' => 'nullable',
            'remarks' => 'nullable',
            'status' => 'required'
        ]));

        return back()->with('success', 'Product Added!');
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'pinterest_url' => 'nullable|url',
            'description' => 'nullable',
            'remarks' => 'nullable',
            'status' => 'required'
        ]));

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
            'name' => 'required|unique:categories,name,'.$id,
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
        $tasks = Task::orderBy('is_done', 'asc')->latest()->get();
        return view('erp.planning.index', compact('tasks'));
    }

    public function storeTask(Request $request)
    {
        Task::create([
            'content' => $request->content,
            'due_date' => $request->due_date ?? now()
        ]);
        return back()->with('success', 'Task Added!');
    }

    public function updateTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->update([
            'content' => $request->content,
            'due_date' => $request->due_date
        ]);
        return back()->with('success', 'Task Updated!');
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