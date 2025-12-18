<div class="group relative flex flex-col p-4 bg-slate-800 rounded-xl border 
    {{ $task->is_done ? 'border-slate-700' : ($status == 'overdue' ? 'border-red-500/30 bg-red-500/5' : 'border-slate-700 hover:border-slate-600') }} 
    shadow-sm transition-all duration-200">

    <!-- MAIN PARENT TASK -->
    <div class="flex items-start justify-between">
        <div class="flex items-start gap-4">
            <!-- Parent Checkbox -->
            <a href="{{ route('erp.planning.toggle', $task->id) }}" 
               class="flex-shrink-0 w-6 h-6 mt-0.5 rounded-full border-2 flex items-center justify-center transition-all
               {{ $task->is_done ? 'bg-green-500 border-green-500 text-slate-900' : 'border-slate-500 hover:border-indigo-400 text-transparent' }}">
               <i class="fa-solid fa-check text-xs"></i>
            </a>

            <div>
                <div class="text-sm font-medium {{ $task->is_done ? 'text-slate-500 line-through' : 'text-slate-200' }}">
                    {{ $task->content }}
                </div>
                
                <!-- Metadata -->
                <div class="flex items-center gap-3 mt-1.5">
                    @if(!$task->is_done)
                        @if($task->priority == 'high') <span class="text-[10px] font-bold text-red-400 bg-red-400/10 px-1.5 py-0.5 rounded">HIGH</span>
                        @elseif($task->priority == 'medium') <span class="text-[10px] font-bold text-yellow-400 bg-yellow-400/10 px-1.5 py-0.5 rounded">MED</span>
                        @endif
                    @endif
                    <span class="text-[10px] {{ $status == 'overdue' ? 'text-red-400 font-bold' : 'text-slate-500' }}">
                        {{ $task->due_date ? $task->due_date->format('M d') : '' }}
                    </span>
                </div>
            </div>
        </div>

       <!-- Actions (Edit & Delete) -->
    <div class="flex items-center">
        <!-- Edit Button -->
        <button onclick="openEditModal({{ json_encode($task) }})" 
                class="text-slate-600 hover:text-indigo-400 opacity-0 group-hover:opacity-100 transition px-2">
            <i class="fa-solid fa-pen-to-square"></i>
        </button>

        <!-- Delete Button -->
        <form action="{{ route('erp.planning.destroy', $task->id) }}" method="POST" onsubmit="confirmDelete(event)">
            @csrf @method('DELETE')
            <button class="text-slate-600 hover:text-red-400 opacity-0 group-hover:opacity-100 transition px-2">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>
</div>
    </div>

    <!-- SUBTASKS SECTION -->
    <div class="mt-3 pl-10">
        <!-- List Existing Subtasks -->
        @foreach($task->subtasks as $subtask)
        <div class="flex items-center justify-between group/sub mb-2">
            <div class="flex items-center gap-2">
                <!-- Small Checkbox -->
                <a href="{{ route('erp.subtasks.toggle', $subtask->id) }}" 
                   class="w-4 h-4 rounded border flex items-center justify-center transition
                   {{ $subtask->is_done ? 'bg-slate-600 border-slate-600 text-slate-300' : 'border-slate-600 hover:border-indigo-400 text-transparent' }}">
                   <i class="fa-solid fa-check text-[10px]"></i>
                </a>
                <span class="text-xs {{ $subtask->is_done ? 'text-slate-600 line-through' : 'text-slate-400' }}">
                    {{ $subtask->content }}
                </span>
            </div>
            
            <!-- Delete Subtask -->
            <form action="{{ route('erp.subtasks.destroy', $subtask->id) }}" method="POST">
                @csrf @method('DELETE')
                <button class="text-slate-700 hover:text-red-400 text-xs opacity-0 group-hover/sub:opacity-100 px-2">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </form>
        </div>
        @endforeach

        <!-- Add Subtask Form -->
        @if(!$task->is_done)
        <form action="{{ route('erp.subtasks.store', $task->id) }}" method="POST" class="mt-2 opacity-40 focus-within:opacity-100 hover:opacity-100 transition">
            @csrf
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-turn-up rotate-90 text-slate-600 text-xs ml-1"></i>
                <input type="text" name="content" placeholder="Add subtask..." 
                       class="bg-transparent border-b border-slate-700 text-xs text-slate-300 w-full py-1 focus:outline-none focus:border-indigo-500 placeholder-slate-600">
            </div>
        </form>
        @endif
    </div>
</div>