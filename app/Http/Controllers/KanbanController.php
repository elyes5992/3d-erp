<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class KanbanController extends Controller
{
    public function index()
    {
        $columns = ['Idea', 'Approved', 'Design Ready', 'Printed'];
        $products = Product::all();

        return view('erp.kanban.index', compact('columns', 'products'));
    }

    public function updateStatus(Request $request)
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
}