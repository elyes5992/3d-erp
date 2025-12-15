@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="mb-6">
    <button onclick="openCreateModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> New Category
    </button>
</div>

<div class="bg-slate-800 rounded-lg shadow-lg border border-slate-700 overflow-hidden">
    <table class="w-full text-left text-sm text-slate-400">
        <thead class="bg-slate-900/50 text-slate-200 uppercase text-xs font-semibold">
            <tr>
                <th class="px-6 py-4">Name</th>
                <th class="px-6 py-4">Designs Count</th>
                <th class="px-6 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-700">
            @foreach($categories as $category)
            <tr class="hover:bg-slate-700/50 transition">
                <td class="px-6 py-4 font-medium text-white">{{ $category->name }}</td>
                <td class="px-6 py-4">
                    <span class="bg-slate-700 text-white px-2 py-1 rounded text-xs">{{ $category->products_count ?? 0 }} Designs</span>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <button onclick="openEditModal({{ $category }})" class="text-indigo-400 hover:text-indigo-300 transition"><i class="fa-solid fa-pen"></i></button>
                    <form action="{{ route('erp.categories.destroy', $category->id) }}" method="POST" onsubmit="confirmDelete(event)" class="inline">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-400 hover:text-red-300 transition"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- MODAL -->
<div id="categoryModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 items-center justify-center p-4">
    <div class="bg-slate-800 rounded-lg shadow-2xl w-full max-w-md border border-slate-700">
        <div class="p-6 border-b border-slate-700 flex justify-between items-center">
            <h3 id="modalTitle" class="text-lg font-bold text-white">Add Category</h3>
            <button onclick="closeModal('categoryModal')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <form id="categoryForm" action="{{ route('erp.categories.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Category Name</label>
                <input type="text" name="name" id="c_name" required class="w-full bg-slate-900 border border-slate-700 rounded p-2 text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <button type="button" onclick="closeModal('categoryModal')" class="px-4 py-2 text-slate-400 hover:text-white text-sm">Cancel</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium transition">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Add Category';
        document.getElementById('categoryForm').action = "{{ route('erp.categories.store') }}";
        document.getElementById('formMethod').value = "POST";
        document.getElementById('c_name').value = '';
        openModal('categoryModal');
    }

    function openEditModal(category) {
        document.getElementById('modalTitle').innerText = 'Edit Category';
        document.getElementById('categoryForm').action = "/categories/" + category.id;
        document.getElementById('formMethod').value = "PUT";
        document.getElementById('c_name').value = category.name;
        openModal('categoryModal');
    }
</script>
@endsection
