@extends('layouts.app')

@section('title', 'Product Kanban')

@section('content')

<!-- Header with View Switcher -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-white">Project Board</h2>
    <div class="flex gap-2 bg-slate-800 p-1 rounded-lg">
        <a href="{{ route('erp.products.index') }}" class="px-3 py-1 text-slate-400 hover:text-white text-sm rounded"><i class="fa-solid fa-list"></i> List</a>
        <span class="px-3 py-1 bg-slate-700 text-white text-sm rounded shadow"><i class="fa-solid fa-columns"></i> Board</span>
    </div>
</div>

<!-- KANBAN CONTAINER -->
<!-- overflow-x-auto allows side scrolling on small screens -->
<div class="flex gap-6 overflow-x-auto pb-10 items-start h-[calc(100vh-200px)]">

    @foreach($columns as $status)
    <!-- COLUMN -->
    <div class="w-80 flex-shrink-0 flex flex-col h-full">
        
        <!-- Column Header -->
        <div class="flex items-center justify-between mb-3 px-1">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                    @if($status == 'Idea') bg-slate-700 text-slate-300
                    @elseif($status == 'Approved') bg-blue-900/50 text-blue-300
                    @elseif($status == 'Design Ready') bg-purple-900/50 text-purple-300
                    @else bg-green-900/50 text-green-300 @endif">
                    {{ $status }}
                </span>
                <span class="text-xs text-slate-500 font-mono">{{ $products->where('status', $status)->count() }}</span>
            </div>
            <button class="text-slate-600 hover:text-white transition"><i class="fa-solid fa-plus"></i></button>
        </div>

        <!-- DROP ZONE (The list that SortableJS attaches to) -->
        <div class="kanban-col flex-1 bg-slate-900/30 rounded-lg p-2 space-y-3 overflow-y-auto custom-scrollbar border border-transparent hover:border-slate-800 transition" 
             data-status="{{ $status }}">
            
            @foreach($products->where('status', $status) as $product)
                <!-- CARD -->
                <div class="kanban-card group bg-slate-800 hover:bg-slate-750 p-3 rounded shadow-sm border border-slate-700 cursor-grab active:cursor-grabbing relative" 
                     data-id="{{ $product->id }}">
                    
                    <!-- Card Title -->
                    <div class="text-slate-200 font-medium text-sm mb-2">{{ $product->name }}</div>
                    
                    <!-- Card Footer (Category & Actions) -->
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-[10px] text-slate-500 bg-slate-900 px-1.5 py-0.5 rounded border border-slate-700">
                            {{ $product->category->name ?? 'No Category' }}
                        </span>
                        
                        @if($product->pinterest_url)
                        <a href="{{ $product->pinterest_url }}" target="_blank" class="text-slate-500 hover:text-[#E60023]">
                            <i class="fa-brands fa-pinterest"></i>
                        </a>
                        @endif
                    </div>
                </div>
            @endforeach

        </div>
    </div>
    @endforeach

</div>

<!-- Load SortableJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<script>
    // 1. Initialize Sortable for every column
    const columns = document.querySelectorAll('.kanban-col');

    columns.forEach((col) => {
        new Sortable(col, {
            group: 'kanban', // Allow dragging between lists named 'kanban'
            animation: 150,
            ghostClass: 'bg-indigo-500/20', // Class applied to the placeholder while dragging
            
            // When an item is dropped
            onEnd: function (evt) {
                const itemEl = evt.item;  // The dragged HTML element
                const newStatus = evt.to.getAttribute('data-status'); // The column status dropped into
                const productId = itemEl.getAttribute('data-id'); // The product ID

                // If dropped in the same place, do nothing
                if(evt.from === evt.to && evt.oldIndex === evt.newIndex) return;

                // Send AJAX request
                fetch("{{ route('erp.kanban.move') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        id: productId,
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Moved successfully");
                    // Optional: Add a toast notification here
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Failed to update status");
                });
            }
        });
    });
</script>

<style>
    /* Custom scrollbar to keep it looking clean */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #475569;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
</style>
@endsection