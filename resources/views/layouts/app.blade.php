<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeData()" x-init="initTheme()" :class="{ 'dark': darkMode }" style="visibility: hidden;">
    @php $appSettings = \App\Models\MonthlySetting::getSettings(); @endphp
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Allied Group') }}</title>

        <!-- Favicon -->
        @if($appSettings->logo)
            <link rel="icon" type="image/png" href="{{ $appSettings->logo_url }}">
            <link rel="apple-touch-icon" href="{{ $appSettings->logo_url }}">
        @else
            <link rel="icon" href="{{ asset('favicon.ico') }}">
        @endif

        <!-- Instant Dark Mode (prevent flash) -->
        <script>
            (function() {
                const stored = localStorage.getItem('darkMode');
                const isDark = stored === null ? true : stored === 'true';
                if (isDark) document.documentElement.classList.add('dark');
                // Show page after dark mode is applied
                document.documentElement.style.visibility = 'visible';
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            function themeData() {
                return {
                    darkMode: true,
                    sidebarOpen: false,
                    sidebarCollapsed: false,
                    initTheme() {
                        // Default is dark mode, only switch to light if explicitly set
                        const stored = localStorage.getItem('darkMode');
                        this.darkMode = stored === null ? true : stored === 'true';
                        this.sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                    },
                    toggleDarkMode() {
                        this.darkMode = !this.darkMode;
                        localStorage.setItem('darkMode', this.darkMode);
                    },
                    toggleSidebar() {
                        this.sidebarCollapsed = !this.sidebarCollapsed;
                        localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
                    }
                }
            }
        </script>

        <style>
            * { font-family: 'Inter', sans-serif; }
            
            /* Custom Scrollbar */
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
            ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
            .dark ::-webkit-scrollbar-thumb { background: #475569; }
            .dark ::-webkit-scrollbar-thumb:hover { background: #64748b; }
            [x-cloak] { display: none !important; }

            /* Sidebar Transition */
            .sidebar-transition { transition: transform 0.3s ease-in-out, width 0.3s ease-in-out; }
            
            /* Gradient Text */
            .gradient-text {
                background: linear-gradient(135deg, #0d9488 0%, #0891b2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            /* Card Hover Effect */
            .card-hover { transition: all 0.3s ease; }
            .card-hover:hover { transform: translateY(-2px); box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.15); }

            /* Nav Link Active */
            .nav-link-active {
                background: linear-gradient(135deg, rgba(13, 148, 136, 0.1) 0%, rgba(8, 145, 178, 0.1) 100%);
                border-left: 3px solid #0d9488;
            }
            .dark .nav-link-active {
                background: linear-gradient(135deg, rgba(13, 148, 136, 0.2) 0%, rgba(8, 145, 178, 0.2) 100%);
            }

            /* Collapsed Sidebar */
            .sidebar-collapsed { width: 80px !important; }
            .sidebar-collapsed .nav-text,
            .sidebar-collapsed .section-title,
            .sidebar-collapsed .user-info,
            .sidebar-collapsed .logo-text,
            .sidebar-collapsed .badge-count { display: none !important; }
            .sidebar-collapsed .nav-link { justify-content: center; padding-left: 0; padding-right: 0; }
            .sidebar-collapsed .nav-link svg { margin: 0; }
            .sidebar-collapsed .user-avatar-section { justify-content: center; }
            .sidebar-collapsed .user-avatar-section > div:last-child { display: none; }

            /* Responsive Tables */
            .table-responsive { width: 100%; overflow-x: auto; }
            .table-responsive table { width: 100%; min-width: 600px; }
            .table-responsive th, .table-responsive td { 
                padding: 0.75rem 0.5rem;
                white-space: nowrap;
            }
            @media (max-width: 1024px) {
                .table-responsive th, .table-responsive td { font-size: 0.75rem; padding: 0.5rem 0.25rem; }
            }
            
            /* Fixed Sidebar Layout */
            .main-content-area {
                margin-left: 0;
                transition: margin-left 0.3s ease;
            }
            @media (min-width: 1024px) {
                .main-content-area { margin-left: 18rem; }
                .main-content-area.sidebar-collapsed-margin { margin-left: 5rem; }
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200">
        <div class="min-h-screen flex">
            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false"
                 class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 lg:hidden">
            </div>

            <!-- Sidebar -->
            <aside :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'sidebar-collapsed lg:w-20' : 'w-72']" 
                   class="fixed inset-y-0 left-0 z-50 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 sidebar-transition lg:translate-x-0 flex flex-col shadow-xl lg:shadow-none h-screen">
                
                <!-- Logo Section -->
                <div class="h-16 flex items-center justify-between px-4 border-b border-slate-200 dark:border-slate-700">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                        @if($appSettings->logo)
                            <img src="{{ $appSettings->logo_url }}" alt="{{ $appSettings->app_name ?? 'Allied Group' }}" class="w-10 h-10 rounded-xl object-contain flex-shrink-0">
                        @else
                            <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-cyan-500 rounded-xl flex items-center justify-center shadow-lg shadow-teal-500/30 flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                        @endif
                        <span class="text-lg font-bold gradient-text logo-text">{{ $appSettings->app_name ?? 'Allied Group' }}</span>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto py-6 px-4">
                    <div class="space-y-1">
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'nav-link-active text-teal-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50' }}" title="Dashboard">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-teal-600 dark:text-teal-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span class="nav-text">Dashboard</span>
                        </a>

                        <!-- Contributions Section -->
                        <div class="pt-4">
                            <p class="section-title px-4 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Contributions</p>
                            
                            <a href="{{ route('contributions.index') }}" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('contributions.index') ? 'nav-link-active text-teal-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50' }}" title="All Contributions">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span class="nav-text">All Contributions</span>
                            </a>

                            @can('create contributions')
                            <a href="{{ route('contributions.create') }}" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('contributions.create') ? 'nav-link-active text-teal-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50' }}" title="Add Contribution">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span class="nav-text">Add Contribution</span>
                            </a>
                            @endcan

                            @can('approve contributions')
                            <a href="{{ route('contributions.pending') }}" class="nav-link flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('contributions.pending') ? 'nav-link-active text-teal-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50' }}" title="Pending Approval">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="nav-text">Pending Approval</span>
                                </div>
                                @php $pendingCount = \App\Models\Contribution::pending()->count(); @endphp
                                @if($pendingCount > 0)
                                <span class="badge-count bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400 text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                                @endif
                            </a>
                            @endcan
                        </div>

                        <!-- Withdrawals Section -->
                        <div class="pt-4">
                            <p class="section-title px-4 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Withdrawals</p>
                            
                            <a href="{{ route('withdrawals.index') }}" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('withdrawals.index') ? 'nav-link-active text-teal-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50' }}" title="All Withdrawals">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                <span class="nav-text">All Withdrawals</span>
                            </a>

                            @can('create withdrawals')
                            <a href="{{ route('withdrawals.create') }}" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('withdrawals.create') ? 'nav-link-active text-teal-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50' }}" title="New Withdrawal">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span class="nav-text">New Withdrawal</span>
                            </a>
                            @endcan

                            @can('approve withdrawals')
                            <a href="{{ route('withdrawals.pending') }}" class="nav-link flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('withdrawals.pending') ? 'nav-link-active text-teal-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50' }}" title="Pending Approval">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="nav-text">Pending Approval</span>
                                </div>
                                @php $pendingWithdrawals = \App\Models\Withdrawal::pending()->count(); @endphp
                                @if($pendingWithdrawals > 0)
                                <span class="badge-count bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400 text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingWithdrawals }}</span>
                                @endif
                            </a>
                            @endcan
                        </div>

                        <!-- Reports Section -->
                        <div class="pt-4">
                            <p class="section-title px-4 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Reports</p>
                            
                            <a href="{{ route('reports.monthly') }}" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('reports.monthly') ? 'nav-link-active text-teal-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50' }}" title="Monthly Report">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="nav-text">Monthly Report</span>
                            </a>

                            <a href="{{ route('reports.yearly') }}" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('reports.yearly') ? 'nav-link-active text-teal-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50' }}" title="Yearly Report">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <span class="nav-text">Yearly Report</span>
                            </a>

                            <a href="{{ route('reports.financial') }}" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('reports.financial') ? 'nav-link-active text-teal-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50' }}" title="Financial Report">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="nav-text">Financial Report</span>
                            </a>

                            <a href="{{ route('reports.due') }}" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('reports.due') ? 'nav-link-active text-teal-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50' }}" title="Due Payments">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span class="nav-text">Due Payments</span>
                            </a>
                        </div>

                        <!-- Management Section -->
                        @if(auth()->user()->hasAnyRole(['super-admin', 'admin', 'accountant']))
                        <div class="pt-4">
                            <p class="section-title px-4 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Management</p>
                            
                            @can('view users')
                            <a href="{{ route('members.index') }}" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('members.index') || request()->routeIs('members.show') ? 'nav-link-active text-teal-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50' }}" title="All Members">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span class="nav-text">All Members</span>
                            </a>
                            
                            <a href="{{ route('members.pending') }}" class="nav-link flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('members.pending') ? 'nav-link-active text-teal-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50' }}" title="Pending Approvals">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                    <span class="nav-text">Pending Approvals</span>
                                </div>
                                @php $pendingMembers = \App\Models\User::where('status', 'pending')->count(); @endphp
                                @if($pendingMembers > 0)
                                <span class="badge-count bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400 text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingMembers }}</span>
                                @endif
                            </a>
                            @endcan

                            @can('manage settings')
                            <a href="{{ route('settings.index') }}" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('settings.*') ? 'nav-link-active text-teal-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50' }}" title="Settings">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="nav-text">Settings</span>
                            </a>
                            @endcan
                            
                            @can('view users')
                            <a href="{{ route('users.index') }}" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('users.*') ? 'nav-link-active text-teal-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50' }}" title="Manage Users">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="nav-text">Manage Users</span>
                            </a>
                            @endcan
                        </div>
                        @endif
                    </div>
                </nav>

                <!-- User Section at Bottom -->
                <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                    <div class="user-avatar-section flex items-center space-x-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50">
                        <img src="{{ Auth::user()->avatar_url }}" 
                             alt="{{ Auth::user()->name }}" 
                             class="w-10 h-10 rounded-full object-cover flex-shrink-0 border-2 border-slate-200 dark:border-slate-600">
                        <div class="user-info flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->roles->first()->name ?? 'Member' }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-h-screen lg:min-w-0 main-content-area" :class="{ 'sidebar-collapsed-margin': sidebarCollapsed }">
                <!-- Top Header -->
                <header class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-30">
                    <!-- Left Side -->
                    <div class="flex items-center space-x-4">
                        <!-- Mobile Menu Button -->
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                            <svg class="w-6 h-6 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <!-- Desktop Sidebar Toggle -->
                        <button @click="toggleSidebar()" class="hidden lg:flex p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition" title="Toggle Sidebar">
                            <svg x-show="!sidebarCollapsed" class="w-5 h-5 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                            <svg x-show="sidebarCollapsed" x-cloak class="w-5 h-5 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                            </svg>
                        </button>
                        
                        <!-- Page Title -->
                        @isset($header)
                        <div class="hidden sm:block">
                            <h1 class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ $header }}</h1>
                        </div>
                        @endisset
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <!-- Dark Mode Toggle -->
                        <button @click="toggleDarkMode()" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition" title="Toggle Theme">
                            <svg x-show="!darkMode" class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                            <svg x-show="darkMode" x-cloak class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <!-- Profile Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-2 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                <img src="{{ Auth::user()->avatar_url }}" 
                                     alt="{{ Auth::user()->name }}" 
                                     class="w-8 h-8 rounded-full object-cover border-2 border-slate-200 dark:border-slate-600">
                                <span class="hidden sm:block text-sm font-medium text-slate-700 dark:text-slate-200">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-64 max-w-[calc(100vw-2rem)] bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-2 z-50 overflow-hidden">
                                
                                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex items-center space-x-3">
                                    <img src="{{ Auth::user()->avatar_url }}" 
                                         alt="{{ Auth::user()->name }}" 
                                         class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->email }}</p>
                                    </div>
                                </div>

                                <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-4 py-2 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span>Profile Settings</span>
                                </a>
                                
                                @if(Auth::user()->email !== 'alliedgroup@gmail.com')
                                <a href="{{ route('profile.member.edit') }}" class="flex items-center space-x-3 px-4 py-2 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>Edit My Information</span>
                                </a>
                                @endif

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center space-x-3 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 w-full">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span>Sign Out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Flash Messages -->
                <div class="px-4 lg:px-6 mt-4">
                    @if (session('success'))
                    <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-xl mb-4 flex items-center space-x-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl mb-4 flex items-center space-x-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl mb-4">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <!-- Page Content -->
                <main class="flex-1 px-4 lg:px-6 pb-6">
                    {{ $slot ?? '' }}@yield('content')
                </main>

                <!-- Footer -->
                <footer class="px-4 lg:px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                    <p class="text-center text-sm text-slate-500 dark:text-slate-400">
                        © {{ date('Y') }} {{ $appSettings->app_name ?? 'Allied Group' }}. All rights reserved.
                    </p>
                    <p class="text-center text-xs text-slate-400 dark:text-slate-500 mt-1">
                        Developed by <span class="font-semibold text-indigo-600 dark:text-indigo-400">Mir Javed Jeetu</span> | <a href="tel:01811480222" class="text-indigo-500 hover:text-indigo-700">01811480222</a>
                    </p>
                </footer>
            </div>
        </div>
    </body>
</html>
