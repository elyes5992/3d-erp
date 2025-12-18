<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Task;
use App\Models\Setting;

class DashboardController extends Controller
{
    public function index()
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

    public function updateJournal(\Illuminate\Http\Request $request)
    {
        Setting::updateOrCreate(
            ['key' => 'team_journal'],
            ['value' => $request->content]
        );

        return back()->with('success', 'Shared journal updated!');
    }
}