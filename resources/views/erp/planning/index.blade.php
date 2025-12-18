@extends('layouts.app')

@section('title', 'Production Planning')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h2 class="text-2xl font-bold text-white">Production Schedule</h2>
    <button onclick="openCreateModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-lg shadow-indigo-500/20">
        <i class="fa-solid fa-plus"></i> New Task
    </button>
</div>

<div class="space-y-8 pb-10">

    <!-- 1. OVERDUE SECTION -->
    @if($overdueTasks->count() > 0)
    <div>
        <h3 class="text-red-400 font-bold uppercase tracking-wider text-xs mb-3 flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation"></i> Overdue / Urgent
        </h3>
        <div class="space-y-3">
            @foreach($overdueTasks as $task)
                @include('erp.planning.partials.task-card', ['task' => $task, 'status' => 'overdue'])
            @endforeach
        </div>
    </div>
    @endif

    <!-- 2. DUE TODAY -->
    <div>
        <h3 class="text-white font-bold uppercase tracking-wider text-xs mb-3 flex items-center gap-2">
            <i class="fa-solid fa-calendar-day text-indigo-400"></i> Due Today
        </h3>
        <div class="space-y-3">
            @forelse($todayTasks as $task)
                @include('erp.planning.partials.task-card', ['task' => $task, 'status' => 'today'])
            @empty
                <div class="bg-slate-800/50 border border-slate-700/50 rounded-lg p-6 text-center text-slate-500 text-sm italic">
                    All caught up for today!
                </div>
            @endforelse
        </div>
    </div>

    <!-- 3. UPCOMING -->
    @if($upcomingTasks->count() > 0)
    <div>
        <h3 class="text-slate-400 font-bold uppercase tracking-wider text-xs mb-3 flex items-center gap-2">
            <i class="fa-solid fa-calendar-days"></i> Upcoming
        </h3>
        <div class="space-y-3 opacity-90">
            @foreach($upcomingTasks as $task)
                @include('erp.planning.partials.task-card', ['task' => $task, 'status' => 'upcoming'])
            @endforeach
        </div>
    </div>
    @endif

    <!-- 4. COMPLETED -->
    @if($completedTasks->count() > 0)
    <div>
        <h3 class="text-slate-600 font-bold uppercase tracking-wider text-xs mb-3 mt-8">Recently Completed</h3>
        <div class="space-y-2 opacity-60 hover:opacity-100 transition duration-300">
            @foreach($completedTasks as $task)
                @include('erp.planning.partials.task-card', ['task' => $task, 'status' => 'done'])
            @endforeach
        </div>
    </div>
    @endif

</div>

<!-- CREATE/EDIT MODAL -->
<div id="taskModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 items-center justify-center p-4">
    <div class="bg-slate-800 rounded-xl shadow-2xl w-full max-w-md border border-slate-700">
        <div class="p-6 border-b border-slate-700 flex justify-between items-center">
            <h3 id="modalTitle" class="text-lg font-bold text-white">Add Task</h3>
            <button onclick="closeModal('taskModal')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="taskForm" action="{{ route('erp.planning.store') }}" method="POST" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <!-- Content -->
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Task Description</label>
                <input type="text" name="content" id="t_content" required placeholder="e.g. Order Filament, Fix Extruder..." 
                       class="w-full bg-slate-900 border border-slate-600 rounded-lg p-2.5 text-white focus:outline-none focus:border-indigo-500">
            </div>

            <!-- Date & Priority Row -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Due Date</label>
                    <input type="date" name="due_date" id="t_due_date"
                           class="w-full bg-slate-900 border border-slate-600 rounded-lg p-2.5 text-white focus:outline-none focus:border-indigo-500 [color-scheme:dark]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Priority</label>
                    <select name="priority" id="t_priority" class="w-full bg-slate-900 border border-slate-600 rounded-lg p-2.5 text-white focus:outline-none focus:border-indigo-500">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High 🔥</option>
                    </select>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-lg font-bold transition shadow-lg">Save Task</button>
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
        // Default to today
        document.getElementById('t_due_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('t_priority').value = 'medium';
        
        openModal('taskModal');
    }

    function openEditModal(task) {
        document.getElementById('modalTitle').innerText = 'Edit Task';
        document.getElementById('taskForm').action = "/planning/" + task.id;
        document.getElementById('formMethod').value = "PUT";
        
        document.getElementById('t_content').value = task.content;
        document.getElementById('t_priority').value = task.priority || 'medium';

        if (task.due_date) {
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