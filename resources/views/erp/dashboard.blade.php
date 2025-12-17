@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Top Stats Grid: Matches Kanban Columns -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- 1. IDEA -->
    <div class="bg-slate-800 p-6 rounded-lg shadow-lg border-l-4 border-slate-500">
        <div class="text-slate-400 text-xs uppercase tracking-wider font-semibold">Idea</div>
        <div class="text-3xl font-bold text-white mt-2">{{ $stats['idea'] }}</div>
    </div>

    <!-- 2. APPROVED -->
    <div class="bg-slate-800 p-6 rounded-lg shadow-lg border-l-4 border-blue-500">
        <div class="text-slate-400 text-xs uppercase tracking-wider font-semibold">Approved</div>
        <div class="text-3xl font-bold text-white mt-2">{{ $stats['approved'] }}</div>
    </div>

    <!-- 3. DESIGN READY -->
    <div class="bg-slate-800 p-6 rounded-lg shadow-lg border-l-4 border-purple-500">
        <div class="text-slate-400 text-xs uppercase tracking-wider font-semibold">Design Ready</div>
        <div class="text-3xl font-bold text-white mt-2">{{ $stats['design_ready'] }}</div>
    </div>

    <!-- 4. PRINTED -->
    <div class="bg-slate-800 p-6 rounded-lg shadow-lg border-l-4 border-green-500">
        <div class="text-slate-400 text-xs uppercase tracking-wider font-semibold">Printed</div>
        <div class="text-3xl font-bold text-white mt-2">{{ $stats['printed'] }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Products -->
    <div class="bg-slate-800 rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-bold text-white mb-4">Recent Designs</h3>
        <div class="space-y-4">
            @foreach($recentProducts as $product)
            <div class="flex items-center justify-between border-b border-slate-700 pb-2 last:border-0 last:pb-0">
                <div>
                    <div class="font-medium text-slate-200">{{ $product->name }}</div>
                    <div class="text-xs text-slate-500">{{ $product->category->name ?? 'Uncategorized' }}</div>
                </div>
                
                <!-- Status Badge Logic -->
                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase border 
                    @if($product->status == 'Approved') bg-blue-500/10 text-blue-400 border-blue-500/20
                    @elseif($product->status == 'Design Ready') bg-purple-500/10 text-purple-400 border-purple-500/20
                    @elseif($product->status == 'Printed') bg-green-500/10 text-green-400 border-green-500/20
                    @else bg-slate-700 text-slate-400 border-slate-600 @endif">
                    {{ $product->status }}
                </span>
            </div>
            @endforeach
        </div>
        <div class="mt-4 text-right">
            <a href="{{ route('erp.products.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300">View All &rarr;</a>
        </div>
    </div>

    <!-- Recent Tasks -->
    <div class="bg-slate-800 rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-bold text-white mb-4">Pending Tasks</h3>
        <div class="space-y-3">
            @forelse($recentTasks as $task)
            <div class="flex items-start gap-3">
                <a href="{{ route('erp.planning.toggle', $task->id) }}"
                    class="text-lg mt-0.5 transition-colors duration-200 {{ $task->is_done ? 'text-green-500 hover:text-green-600' : 'text-slate-500 hover:text-indigo-500' }}">
                    <i class="fa-regular {{ $task->is_done ? 'fa-circle-check' : 'fa-circle' }}"></i>
                </a>
                <span class="{{ $task->is_done ? 'line-through text-slate-600' : 'text-slate-300' }} text-sm">{{ $task->content }}</span>
            </div>
            @empty
            <div class="text-slate-500 text-sm">No tasks pending.</div>
            @endforelse
        </div>
        <div class="mt-4 text-right">
            <a href="{{ route('erp.planning.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300">View Planner &rarr;</a>
        </div>
    </div>

    <!-- SHARED TEAM BOARD -->
    <div class="col-span-1 lg:col-span-2 mt-2">
        <form action="{{ route('erp.journal.update') }}" method="POST">
            @csrf
            
            <div class="bg-slate-800 rounded-lg shadow-lg border border-slate-600">
                <!-- Header -->
                <div class="px-6 py-3 border-b border-slate-600 flex justify-between items-center bg-gradient-to-r from-slate-800 to-slate-700 rounded-t-lg">
                    <div class="flex items-center gap-3">
                        <div class="bg-indigo-500/20 p-2 rounded-full text-indigo-400">
                            <i class="fa-solid fa-users-viewfinder"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-white text-sm uppercase tracking-wider">Team Whiteboard</h3>
                            <p class="text-[10px] text-slate-400">Visible to everyone • Auto-expanding</p>
                        </div>
                    </div>
                    
                    <button type="submit" class="text-xs font-bold text-indigo-400 hover:text-white transition uppercase tracking-wide flex items-center gap-2 px-3 py-1 rounded hover:bg-indigo-600">
                        <i class="fa-solid fa-save"></i> Save Changes
                    </button>
                </div>

                <!-- Text Area -->
                <div class="relative">
                    <div class="absolute inset-0 opacity-5 pointer-events-none" 
                         style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;">
                    </div>

                    <textarea 
                        name="content" 
                        rows="8" 
                        class="w-full bg-slate-800/50 text-slate-300 font-mono text-sm p-6 focus:outline-none focus:bg-slate-900/50 transition resize-y placeholder-slate-600 leading-relaxed"
                        placeholder="Drop unorganized thoughts, links, hex codes, or project ideas here..."
                        spellcheck="false"
                    >{{ $journal->value }}</textarea>
                </div>
                
                <div class="px-6 py-2 bg-slate-900/30 border-t border-slate-700 text-right">
                    <span class="text-[10px] text-slate-500 italic">
                        Updates visible to team immediately
                    </span>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection