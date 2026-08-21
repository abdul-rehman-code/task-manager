<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - Performance Management</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Phosphor Icons for UI -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #FAFAFB; /* Soft light background */
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #E2E8F0;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #CBD5E1;
        }
        /* Smooth transitions */
        .transition-all-fast {
            transition: all 0.2s ease-in-out;
        }
    </style>
</head>
<body class="text-slate-800 antialiased h-screen overflow-hidden flex flex-col md:flex-row">

    <!-- Mobile Top Header -->
    <div class="md:hidden flex items-center justify-between px-6 py-4 bg-white border-b border-slate-100 z-50">
        <div class="flex items-center gap-3">
            <div class="bg-indigo-600 rounded-lg p-2 flex items-center justify-center">
                <i class="ph-fill ph-rocket text-white text-xl"></i>
            </div>
            <h1 class="font-bold text-lg text-slate-800 tracking-tight leading-tight">Performance<br>Management</h1>
        </div>
        <button id="mobile-menu-btn" class="text-slate-500 hover:text-indigo-600 transition-colors">
            <i class="ph ph-list text-2xl"></i>
        </button>
    </div>

    <!-- Sidebar (Desktop) / Mobile Menu Slide-over -->
    <aside id="sidebar" class="fixed md:static inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-100 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col h-full overflow-y-auto pt-20 md:pt-0">
        
        <!-- Logo (Desktop) -->
        <div class="hidden md:flex items-center gap-3 px-6 py-8">
            <div class="bg-indigo-600 rounded-xl p-2.5 shadow-sm shadow-indigo-200 flex items-center justify-center">
                <i class="ph-fill ph-rocket text-white text-2xl"></i>
            </div>
            <h1 class="font-bold text-xl text-slate-800 tracking-tight leading-tight">Performance<br>Management</h1>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 space-y-1 mb-8">
            <a href="{{ route('employee.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all-fast {{ request()->routeIs('employee.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                <i class="ph-fill ph-squares-four text-xl"></i>
                Dashboard
            </a>
            
            <div class="pt-4 pb-2 px-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tasks</p>
            </div>
            <a href="{{ route('employee.tasks.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium transition-all-fast {{ request()->routeIs('employee.tasks.index') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                <i class="ph ph-check-square text-xl"></i>
                All Tasks
            </a>
            <a href="{{ route('employee.history') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium transition-all-fast {{ request()->routeIs('employee.history') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                <i class="ph ph-clock-counter-clockwise text-xl"></i>
                Task History
            </a>

            <div class="pt-4 pb-2 px-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Account</p>
            </div>
            <a href="{{ route('employee.profile') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium transition-all-fast {{ request()->routeIs('employee.profile') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                <i class="ph ph-user text-xl"></i>
                Profile
            </a>
            <a href="{{ route('employee.settings') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium transition-all-fast {{ request()->routeIs('employee.settings') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                <i class="ph ph-gear text-xl"></i>
                Settings
            </a>
        </nav>
        
        <!-- Logout Button at bottom -->
        <div class="p-4 border-t border-slate-100">
             <form method="POST" action="{{ route('employee.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-rose-500 hover:bg-rose-50 font-medium transition-all-fast">
                    <i class="ph ph-sign-out text-xl"></i>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Overlay for mobile sidebar -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-30 hidden md:hidden transition-opacity"></div>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col md:flex-row h-full overflow-hidden relative">
        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation (Optional/Alternative to Sidebar) - Kept hidden for now to stick to sidebar slideover -->
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            let isSidebarOpen = false;

            function toggleSidebar() {
                isSidebarOpen = !isSidebarOpen;
                if (isSidebarOpen) {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                    // Prevent background scrolling
                    document.body.style.overflow = 'hidden';
                } else {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            }

            mobileMenuBtn?.addEventListener('click', toggleSidebar);
            overlay?.addEventListener('click', toggleSidebar);
        });
    </script>
</body>
</html>
