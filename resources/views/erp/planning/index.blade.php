@extends('layouts.app')

@section('title', 'Production Planning')

@section('content')
<div class="mb-6">
    <button onclick="openCreateModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> New Task
    </button>
</div>

<div class="bg-slate-800 rounded-lg shadow-lg border border-slate-700 p-6">
    <div class="space-y-4">
        @forelse($tasks as $task)
        <div class="flex items-center justify-between group p-3 rounded hover:bg-slate-700/50 transition border border-transparent hover:border-slate-700">
            <div class="flex items-center gap-4">
                <a href="{{ route('erp.planning.toggle', $task->id) }}" class="text-xl transition {{ $task->is_done ? 'text-green-500 hover:text-green-400' : 'text-slate-500 hover:text-indigo-400' }}">
                    <i class="fa-regular {{ $task->is_done ? 'fa-circle-check' : 'fa-circle' }}"></i>
                </a>
                <div>
                     <div class="{{ $task->is_done ? 'line-through text-slate-500' : 'text-white' }} font-medium">{{ $task->content }}</div>
                     <div class="text-xs text-slate-500">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'No due date' }}</div>
                </div>
            </div>
            
            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                <button onclick="openEditModal({{ $task }})" class="text-slate-400 hover:text-white"><i class="fa-solid fa-pen"></i></button>
                <form action="{{ route('erp.planning.destroy', $task->id) }}" method="POST" onsubmit="confirmDelete(event)" class="inline">
                    @csrf
                    @method('DELETE')
                    <button class="text-slate-400 hover:text-red-400"><i class="fa-solid fa-trash"></i></button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center text-slate-500 py-10">
            <i class="fa-solid fa-clipboard-check text-4xl mb-3 opacity-20"></i>
            <p>All caught up! No planning tasks.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- MODAL -->
<div id="taskModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 items-center justify-center p-4">
    <div class="bg-slate-800 rounded-lg shadow-2xl w-full max-w-md border border-slate-700">
        <div class="p-6 border-b border-slate-700 flex justify-between items-center">
            <h3 id="modalTitle" class="text-lg font-bold text-white">Add Task</h3>
            <button onclick="closeModal('taskModal')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <form id="taskForm" action="{{ route('erp.planning.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Task Description</label>
                <input type="text" name="content" id="t_content" required class="w-full bg-slate-900 border border-slate-700 rounded p-2 text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Due Date</label>
                <input type="date" name="due_date" id="t_due_date" class="w-full bg-slate-900 border border-slate-700 rounded p-2 text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <button type="button" onclick="closeModal('taskModal')" class="px-4 py-2 text-slate-400 hover:text-white text-sm">Cancel</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium transition">Save Task</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Add Task';
        document.getElementById('taskForm').action = "{{ route('erp.planning.store') }}";
        document.getElementById('formMethod').value = "POST";
        document.getElementById('t_content').value = '';
        document.getElementById('t_due_date').value = '';
        openModal('taskModal');
    }

    function openEditModal(task) {
        document.getElementById('modalTitle').innerText = 'Edit Task';
        document.getElementById('taskForm').action = "/planning/" + task.id;
        document.getElementById('formMethod').value = "PUT";
        document.getElementById('t_content').value = task.content;
        
        // Format date for input type=date
        if(task.due_date) {
            const date = new Date(task.due_date);
            const iso = date.toISOString().split('T')[0];
            document.getElementById('t_due_date').value = iso;
        } else {
            document.getElementById('t_due_date').value = '';
        }
        
        openModal('taskModal');
    }
</script>
@endsection
