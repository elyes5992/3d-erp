<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        $categories = Category::all();
        return view('erp.products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'pinterest_url' => 'nullable|url',
            'description' => 'nullable',
            'remarks' => 'nullable',
            'cost' => 'nullable|numeric'
        ]);

        $validated['status'] = 'Idea';
        Product::create($validated);

        return back()->with('success', 'Product Added!');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'pinterest_url' => 'nullable|url',
            'description' => 'nullable',
            'remarks' => 'nullable',
            'cost' => 'nullable|numeric'
        ]);

        $product->update($validated);

        return back()->with('success', 'Product Updated!');
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return back()->with('success', 'Product Deleted!');
    }
}