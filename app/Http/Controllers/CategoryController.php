<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('erp.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        Category::create($request->validate([
            'name' => 'required|unique:categories,name',
            'color' => 'nullable'
        ]));
        return back()->with('success', 'Category Created!');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->validate([
            'name' => 'required|unique:categories,name,' . $id,
            'color' => 'nullable'
        ]));
        return back()->with('success', 'Category Updated!');
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return back()->with('success', 'Category Deleted!');
    }
}