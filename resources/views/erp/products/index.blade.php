@extends('layouts.app')

@section('title', 'Products & Designs')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div class="flex gap-2">
        <!-- Filter buttons could go here -->
    </div>
    <button onclick="openCreateModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 sm:px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
        <i class="fa-solid fa-plus"></i><span class="hidden sm:inline">Add Design</span>
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @foreach($products as $product)
    <div class="bg-slate-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition group border border-slate-700 flex flex-col h-full">

        <!-- CARD HEADER: IMAGE OR COLOR BAR -->
        <div class="w-full relative">
            @if($product->image_path)
                <!-- 1. Show Image if available -->
                <div class="h-32 w-full overflow-hidden relative cursor-pointer" onclick="openImageModal('{{ $product->image_path }}')">
                    <img src="{{ $product->image_path }}" alt="{{ $product->name }}" class="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition duration-500">
                    <!-- Status Overlay -->
                    <div class="absolute top-2 right-2 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-black/70 text-white backdrop-blur-sm border border-white/10">
                        {{ $product->status }}
                    </div>
                </div>
            @else
                <!-- 2. Fallback to Color Bar -->
                <div class="h-2 w-full flex-shrink-0 
                    @if($product->status == 'Approved') bg-blue-500
                    @elseif($product->status == 'Design Ready') bg-purple-500
                    @elseif($product->status == 'Printed') bg-green-500
                    @else bg-slate-600 @endif">
                </div>
            @endif
        </div>

        <div class="p-5 flex flex-col flex-grow">
            <div class="flex justify-between items-start mb-3">
                <span class="text-xs font-bold text-indigo-400 bg-indigo-400/10 px-2 py-1 rounded">{{ $product->category->name ?? 'Uncategorized' }}</span>
                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                    
                    <!-- --- NEW: SEE IMAGE BUTTON --- -->
                    @if($product->image_path)
                    <button onclick="openImageModal('{{ $product->image_path }}')" class="text-slate-400 hover:text-indigo-400" title="View Full Image">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    @endif

                    <button onclick="openEditModal({{ $product }})" class="text-slate-400 hover:text-white"><i class="fa-solid fa-pen"></i></button>
                    
                    <form action="{{ route('erp.products.destroy', $product->id) }}" method="POST" onsubmit="confirmDelete(event)" class="inline">
                        @csrf
                        @method('DELETE')
                        <button class="text-slate-400 hover:text-red-400"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </div>
            </div>

            <h3 class="font-bold text-lg text-white mb-2 truncate" title="{{ $product->name }}">{{ $product->name }}</h3>

            <div class="text-xs text-slate-400 mb-4 h-10 overflow-hidden flex-grow">
                {{ $product->remarks ? Str::limit($product->remarks, 60) : 'No remarks provided.' }}
            </div>

            <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-700/50">
                <span class="text-[10px] uppercase font-bold px-2 py-1 rounded 
                    @if($product->status == 'Approved') bg-blue-500/10 text-blue-400
                    @elseif($product->status == 'Design Ready') bg-purple-500/10 text-purple-400
                    @elseif($product->status == 'Printed') bg-green-500/10 text-green-400
                    @else bg-slate-700 text-slate-400 @endif">
                    {{ $product->status }}
                </span>
            
                <div class="flex items-center gap-3">
                    @if($product->cost)
                        <span class="text-xs text-slate-400 font-mono bg-slate-900 px-2 py-1 rounded border border-slate-700">
                            {{ number_format($product->cost, 2) }} dt
                        </span>
                    @endif
            
                    @if($product->pinterest_url)
                        <a href="{{ $product->pinterest_url }}" target="_blank" class="text-slate-500 hover:text-[#E60023] transition">
                            <i class="fa-brands fa-pinterest text-lg"></i>
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div>
    @endforeach
</div>

<!-- ======================= -->
<!-- NEW: FULL IMAGE MODAL -->
<!-- ======================= -->
<div id="imageModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="closeModal('imageModal')">
    <div class="relative max-w-4xl w-full max-h-screen flex flex-col items-center">
        <!-- Close Button -->
        <button onclick="closeModal('imageModal')" class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl">
            <i class="fa-solid fa-xmark"></i>
        </button>
        
        <!-- Image Container -->
        <img id="modalFullImage" src="" alt="Full Preview" class="rounded-lg shadow-2xl max-h-[85vh] object-contain border border-slate-600">
    </div>
</div>

<!-- CREATE/EDIT MODAL -->
<div id="productModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 items-center justify-center p-4">
    <!-- ... (Your existing create/edit modal code remains unchanged) ... -->
    <div class="bg-slate-800 rounded-lg shadow-2xl w-full max-w-lg border border-slate-700">
        <div class="p-6 border-b border-slate-700 flex justify-between items-center">
            <h3 id="modalTitle" class="text-lg font-bold text-white">Add New Design</h3>
            <button onclick="closeModal('productModal')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="productForm" action="{{ route('erp.products.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Product Name</label>
                <input type="text" name="name" id="p_name" required class="w-full bg-slate-900 border border-slate-700 rounded p-2 text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Category</label>
                <select name="category_id" id="p_category" class="w-full bg-slate-900 border border-slate-700 rounded p-2 text-white focus:outline-none focus:border-indigo-500">
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Upload Image</label>
                <input type="file" name="image" id="p_image" accept="image/*" 
                       class="block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer bg-slate-900 border border-slate-700 rounded">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Pinterest / Source URL</label>
                <input type="url" name="pinterest_url" id="p_url" class="w-full bg-slate-900 border border-slate-700 rounded p-2 text-white focus:outline-none focus:border-indigo-500" placeholder="https://...">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Est. Cost (dt)</label>
                <input type="number" step="0.01" name="cost" id="p_cost" class="w-full bg-slate-900 border border-slate-700 rounded p-2 text-white focus:outline-none focus:border-indigo-500" placeholder="0.00">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Remarks</label>
                <textarea name="remarks" id="p_remarks" rows="3" class="w-full bg-slate-900 border border-slate-700 rounded p-2 text-white focus:outline-none focus:border-indigo-500" placeholder="Filament settings etc..."></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <button type="button" onclick="closeModal('productModal')" class="px-4 py-2 text-slate-400 hover:text-white text-sm">Cancel</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium transition">Save Design</button>
            </div>
        </form>
    </div>
</div>

<script>
    // --- NEW: FUNCTION TO OPEN IMAGE MODAL ---
    function openImageModal(imageUrl) {
        const modal = document.getElementById('imageModal');
        const img = document.getElementById('modalFullImage');
        img.src = imageUrl;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    // Existing functions...
    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Add New Design';
        document.getElementById('productForm').action = "{{ route('erp.products.store') }}";
        document.getElementById('formMethod').value = "POST";

        document.getElementById('p_name').value = '';
        document.getElementById('p_url').value = '';
        document.getElementById('p_remarks').value = '';
        document.getElementById('p_cost').value = ''; 
        document.getElementById('p_image').value = ''; 

        openModal('productModal');
    }

    function openEditModal(product) {
        document.getElementById('modalTitle').innerText = 'Edit Design';
        
        // Fixed URL generation for edit
        let baseUrl = "{{ url('/products') }}"; 
        document.getElementById('productForm').action = baseUrl + "/" + product.id;
        
        document.getElementById('formMethod').value = "PUT";

        document.getElementById('p_name').value = product.name;
        document.getElementById('p_category').value = product.category_id;
        document.getElementById('p_url').value = product.pinterest_url || '';
        document.getElementById('p_remarks').value = product.remarks || '';
        document.getElementById('p_cost').value = product.cost || '';
        document.getElementById('p_image').value = ''; 

        openModal('productModal');
    }

    // Helper to close any modal by ID
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        
        // Clear image source when closing image modal to prevent flicker
        if(modalId === 'imageModal') {
            document.getElementById('modalFullImage').src = '';
        }
    }

    // Helper to open generic modal
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
</script>
@endsection