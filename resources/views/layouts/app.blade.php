<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D PrintHub ERP</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- REMOVED: The CDN script and the tailwind.config script -->

    <!-- ADDED: Vite Directive to load compiled CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Keep your custom CSS here if you prefer, or move to app.css */
        body { font-family: 'Inter', sans-serif; }
        .glass {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="bg-slate-900 text-slate-200 antialiased h-screen flex overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-800 border-r border-slate-700 flex-shrink-0 flex flex-col transition-all duration-300">
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-slate-700 bg-slate-800">
            <div class="text-xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
                <i class="fa-solid fa-cube mr-2 text-indigo-400"></i>3D PrintHub
            </div>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 py-4 space-y-1">
            
            <a href="{{ route('erp.dashboard') }}" 
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ Route::is('erp.dashboard') ? 'bg-indigo-600/20 text-indigo-300' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
                <i class="fa-solid fa-gauge w-6 text-center mr-2 {{ Route::is('erp.dashboard') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-white' }}"></i>
                Dashboard
            </a>

            <a href="{{ route('erp.products.index') }}" 
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ Route::is('erp.products.*') ? 'bg-indigo-600/20 text-indigo-300' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
                <i class="fa-solid fa-shapes w-6 text-center mr-2 {{ Route::is('erp.products.*') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-white' }}"></i>
                Products / Designs
            </a>

            <a href="{{ route('erp.kanban.index') }}" 
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ Route::is('erp.kanban') ? 'bg-indigo-600/20 text-indigo-300' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
                <i class="fa-solid fa-columns w-6 text-center mr-2 {{ Route::is('erp.kanban') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-white' }}"></i>
                Project Board
            </a>

            <a href="{{ route('erp.categories.index') }}" 
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ Route::is('erp.categories.*') ? 'bg-indigo-600/20 text-indigo-300' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
                <i class="fa-solid fa-tags w-6 text-center mr-2 {{ Route::is('erp.categories.*') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-white' }}"></i>
                Categories
            </a>

            <a href="{{ route('erp.planning.index') }}" 
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ Route::is('erp.planning.*') ? 'bg-indigo-600/20 text-indigo-300' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
                <i class="fa-solid fa-calendar-check w-6 text-center mr-2 {{ Route::is('erp.planning.*') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-white' }}"></i>
                Planning
            </a>

        </nav>

        <!-- User/Footer -->
        <div class="p-4 border-t border-slate-700">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center font-bold text-xs text-white">
                    U
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-white">Team Admin</p>
                    <p class="text-xs text-slate-500">Connected</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-slate-900 relative">
        <!-- Top Header -->
        <header class="h-16 flex items-center justify-between px-8 bg-slate-900/80 backdrop-blur sticky top-0 z-10 border-b border-slate-800">
            <h2 class="text-xl font-semibold text-white">
                @yield('title', 'Dashboard')
            </h2>
            <div class="flex items-center gap-4">
               <button class="text-slate-400 hover:text-white transition"><i class="fa-regular fa-bell"></i></button>
            </div>
        </header>

        <div class="p-8">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400 flex items-center gap-3">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
    
     <script>
        function openModal(id) {
            const el = document.getElementById(id);
            if(el) {
                el.classList.remove('hidden');
                el.classList.add('flex');
            }
        }
        function closeModal(id) {
            const el = document.getElementById(id);
            if(el) {
                el.classList.add('hidden');
                el.classList.remove('flex');
            }
        }
        
        // Confirm Delete
        function confirmDelete(e) {
            if(!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        }
    </script>
</body>
</html>
