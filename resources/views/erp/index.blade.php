<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Print Master ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 text-slate-800">

    <!-- Navbar -->
    <nav class="bg-indigo-600 text-white p-4 shadow-lg">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold"><i class="fa-solid fa-cube mr-2"></i>3D PrintHub</h1>
            <div class="text-sm opacity-80">Connected to Cloud DB</div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-6 grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        <!-- LEFT COLUMN: Planning & Quick Stats -->
        <div class="space-y-6">
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 gap-2">
                <div class="bg-white p-4 rounded-lg shadow border-l-4 border-indigo-500">
                    <div class="text-gray-500 text-xs uppercase">In Production</div>
                    <div class="text-2xl font-bold">{{ $stats['printing'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
                    <div class="text-gray-500 text-xs uppercase">Concepts</div>
                    <div class="text-2xl font-bold">{{ $stats['ideas'] }}</div>
                </div>
            </div>

            <!-- To-Do List -->
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="font-bold text-lg mb-4 text-slate-700">Production Planning</h2>
                
                <!-- Add Task -->
                <form action="{{ route('erp.task.store') }}" method="POST" class="flex gap-2 mb-4">
                    @csrf
                    <input type="text" name="content" placeholder="New task..." class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:border-indigo-500">
                    <button class="bg-indigo-600 text-white px-3 py-1 rounded text-sm hover:bg-indigo-700">+</button>
                </form>

                <!-- Task List -->
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($tasks as $task)
                    <div class="flex items-center justify-between group">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('erp.task.toggle', $task->id) }}" class="text-lg {{ $task->is_done ? 'text-green-500' : 'text-gray-300 hover:text-indigo-500' }}">
                                <i class="fa-regular {{ $task->is_done ? 'fa-circle-check' : 'fa-circle' }}"></i>
                            </a>
                            <span class="{{ $task->is_done ? 'line-through text-gray-400' : '' }} text-sm">{{ $task->content }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Category Add -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-bold text-sm mb-2">Categories</h3>
                <div class="flex flex-wrap gap-2 mb-3">
                    @foreach($categories as $cat)
                        <span class="px-2 py-1 bg-gray-100 text-xs rounded-full border">{{ $cat->name }}</span>
                    @endforeach
                </div>
                <form action="{{ route('erp.category.store') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="name" placeholder="New Category" class="w-full border rounded px-2 py-1 text-xs">
                    <button class="bg-gray-800 text-white px-2 rounded text-xs">Add</button>
                </form>
            </div>
        </div>

        <!-- RIGHT COLUMN: Products Grid -->
        <div class="lg:col-span-3 space-y-6">
            
            <!-- Add Product Form -->
            <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-indigo-600">
                <h2 class="text-xl font-bold mb-4">Add New 3D Design</h2>
                <form action="{{ route('erp.product.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase">Product Name</label>
                        <input type="text" name="name" required class="w-full border p-2 rounded mt-1 focus:ring-2 ring-indigo-200 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase">Category</label>
                        <select name="category_id" class="w-full border p-2 rounded mt-1 bg-white">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase">Pinterest / STL URL</label>
                        <input type="url" name="pinterest_url" class="w-full border p-2 rounded mt-1 text-blue-600" placeholder="https://pinterest.com/...">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase">Status</label>
                        <select name="status" class="w-full border p-2 rounded mt-1 bg-white">
                            <option value="Idea">💡 Idea Phase</option>
                            <option value="Slicing">⚙️ Slicing</option>
                            <option value="Printing">🖨️ Printing</option>
                            <option value="Post-Processing">🎨 Post-Processing</option>
                            <option value="Done">✅ Done / For Sale</option>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase">Tech Remarks (Filament, nozzle, supports)</label>
                        <textarea name="remarks" rows="2" class="w-full border p-2 rounded mt-1" placeholder="e.g. PLA Matte, 210°C, Needs supports"></textarea>
                    </div>

                    <button class="col-span-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded transition">
                        Add to Inventory
                    </button>
                </form>
            </div>

            <!-- Product List -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($products as $product)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition duration-300 overflow-hidden flex flex-col">
                    <div class="p-4 flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">{{ $product->category->name ?? 'Uncategorized' }}</span>
                            <!-- Status Badge -->
                            <span class="text-xs border px-2 py-1 rounded 
                                {{ $product->status == 'Printing' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-gray-100 text-gray-600' }}">
                                {{ $product->status }}
                            </span>
                        </div>
                        
                        <h3 class="font-bold text-lg text-slate-800">{{ $product->name }}</h3>
                        
                        @if($product->pinterest_url)
                            <a href="{{ $product->pinterest_url }}" target="_blank" class="text-xs text-blue-500 hover:underline flex items-center gap-1 mt-1 mb-2">
                                <i class="fa-brands fa-pinterest"></i> View Design Source
                            </a>
                        @endif

                        @if($product->remarks)
                            <div class="bg-yellow-50 p-2 rounded mt-2 text-xs text-yellow-800 border border-yellow-100">
                                <strong>Note:</strong> {{ $product->remarks }}
                            </div>
                        @endif
                    </div>
                    <div class="bg-gray-50 px-4 py-2 border-t text-xs text-gray-400 flex justify-between">
                        <span>Added {{ $product->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </div>

</body>
</html>