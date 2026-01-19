<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Staff Dashboard - Belleza Rosa')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #1E40AF;
            --gold: #F59E0B;
            --light: #F8FAFC;
            --sidebar-width: 260px;
            --content-max: 80rem; /* 1280px */
        }

        .sidebar {
            width: var(--sidebar-width);
            background: var(--primary);
            color: white;
            position: fixed;
            height: 100%;
            overflow-y: auto;
            z-index: 50;
            transition: transform 0.25s ease;
        }

        .nav-item {
            padding: 16px 30px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .nav-item:hover,
        .nav-item.active {
            background: #1E3A8A;
        }

        .nav-item.active {
            border-left: 5px solid var(--gold);
            background: #3B82F6;
            font-weight: 600;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 24px;
            min-height: 100vh;
            background: #F8FAFC;
        }

        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 40;
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            z-index: 45;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .cursor-pointer:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease;
        }

        .hidden {
            display: none;
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 70px 14px 20px;
            }

            .mobile-menu-btn {
                display: flex;
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="sidebar-overlay hidden" onclick="closeSidebar()" aria-hidden="true"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar" aria-label="Sidebar navigation">
        <div class="p-6 border-b border-white border-opacity-20">
            <div class="flex items-center justify-between">
                <a href="{{ route('staff.dashboard') }}" class="flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Belleza Rosa Logo"
                        style="height: 28px; margin-right: 10px;">
                    <span class="text-xl font-bold">Belleza Rosa</span>
                </a>
                <button type="button" class="lg:hidden text-white/90 hover:text-white"
                    onclick="closeSidebar()" aria-label="Close sidebar">
                    <i class="fas fa-xmark text-xl"></i>
                </button>
            </div>
            <p class="text-sm opacity-80 mt-2">Welcome, {{ auth()->user()->full_name }}</p>
        </div>

        <nav class="mt-4">
            @if (auth()->user()->isStaff())
                <a class="nav-item {{ request()->is('staff/dashboard*') ? 'active' : '' }}" href="{{ route('staff.dashboard') }}"
                    aria-current="{{ request()->is('staff/dashboard*') ? 'page' : 'false' }}">
                    <i class="fas fa-home mr-3"></i> Dashboard
                </a>
                <a class="nav-item {{ request()->is('staff/commission*') ? 'active' : '' }}" href="{{ route('staff.commission') }}"
                    aria-current="{{ request()->is('staff/commission*') ? 'page' : 'false' }}">
                    <i class="fas fa-hand-holding-usd mr-3"></i> My Commissions
                </a>
                <a class="nav-item {{ request()->is('staff/schedule*') ? 'active' : '' }}" href="{{ route('staff.schedule') }}"
                    aria-current="{{ request()->is('staff/schedule*') ? 'page' : 'false' }}">
                    <i class="fas fa-calendar-week mr-3"></i> Weekly Schedule
                </a>
                <a class="nav-item {{ request()->is('dashboard/inventory*') ? 'active' : '' }}" href="{{ route('dashboard.inventory.index') }}"
                    aria-current="{{ request()->is('dashboard/inventory*') ? 'page' : 'false' }}">
                    <i class="fas fa-boxes mr-3"></i> Inventory
                </a>
                <a class="nav-item {{ request()->is('staff/profile*') ? 'active' : '' }}" href="{{ route('staff.profile.edit') }}"
                    aria-current="{{ request()->is('staff/profile*') ? 'page' : 'false' }}">
                    <i class="fas fa-user-circle mr-3"></i> Profile
                </a>
            @endif
            <div class="px-6 mt-6">
                <div class="h-px bg-white/20"></div>
            </div>

            <button type="button" class="nav-item w-full text-left" onclick="confirmLogout()">
                <i class="fas fa-sign-out-alt mr-3"></i> Logout
            </button>
        </nav>
    </aside>

    <!-- Mobile Menu Button -->
    <button type="button" class="mobile-menu-btn inline-flex items-center justify-center rounded-xl bg-white px-3 py-2 shadow-md ring-1 ring-gray-200 text-gray-700 hover:bg-gray-50"
        onclick="openSidebar()" aria-label="Open sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Main Content -->
    <main class="main-content">
        <div class="mx-auto w-full" style="max-width: var(--content-max);">
            @yield('content')
        </div>
    </main>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <!-- Toast Notifications Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-4">
        @if (session('success'))
            <x-toast type="success" :message="session('success')" />
        @endif

        @if (session('error'))
            <x-toast type="error" :message="session('error')" />
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <x-toast type="error" :message="$error" />
            @endforeach
        @endif
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar) sidebar.classList.add('open');
        if (overlay) overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar) sidebar.classList.remove('open');
        if (overlay) overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) {
            closeSidebar();
        }
    });

    async function confirmLogout() {
        const result = await Swal.fire({
            title: 'Logout?',
            text: 'Are you sure you want to logout from your account?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1E40AF',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, logout',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        });

        if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
        }
    }
</script>

@stack('scripts')

</html>

