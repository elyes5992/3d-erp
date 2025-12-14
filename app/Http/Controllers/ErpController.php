<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Task;

class ErpController extends Controller
{
    // 1. Show the Main Dashboard
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        $categories = Category::all();
        $tasks = Task::orderBy('is_done', 'asc')->latest()->get();
        
        // Simple stats
        $stats = [
            'total' => $products->count(),
            'printing' => $products->where('status', 'Printing')->count(),
            'ideas' => $products->where('status', 'Idea')->count(),
        ];

        return view('erp.index', compact('products', 'categories', 'tasks', 'stats'));
    }

    // 2. Store a New Product
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

    // 3. Store a New Category
    public function storeCategory(Request $request)
    {
        Category::create($request->validate(['name' => 'required']));
        return back();
    }

    // 4. Store a Task
    public function storeTask(Request $request)
    {
        Task::create([
            'content' => $request->content,
            'due_date' => now()
        ]);
        return back();
    }
    
    // 5. Toggle Task Complete
    public function toggleTask($id)
    {
        $task = Task::find($id);
        $task->update(['is_done' => !$task->is_done]);
        return back();
    }
}