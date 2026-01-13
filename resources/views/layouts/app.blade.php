<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Laravel'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @stack('styles')
</head>

<body class="font-sans antialiased">
    <!-- Include your custom header/navbar from landing.blade.php -->
    <header id="header">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <nav class="navbar"
                style="display: flex; justify-content: space-between; align-items: center; padding: 20px 0;">
                <a href="/" class="logo"
                    style="display: flex; align-items: center; font-size: 24px; font-weight: 700; color: #1E40AF;">
                    <img src="{{ asset('images/logo.png') }}" alt="Belleza Rosa Logo"
                        style="height: 28px; margin-right: 10px;">
                    <span>Belleza Rosa</span>
                </a>
                <ul class="nav-links" style="display: flex; list-style: none; gap: 30px; align-items: center;">
                    @auth
                        @if (auth()->user()->isCustomer())
                            <li>
                                <a href="{{ route('customer.appointments.index') }}"
                                    class="nav-link {{ request()->is('customer/appointments*') ? 'active' : '' }}"
                                    style="font-weight: 600; color: #1E40AF;">
                                    <i class="fas fa-calendar-alt mr-1"></i> My Appointments
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('customer.staff') }}"
                                    class="nav-link {{ request()->is('customer/staff*') ? 'active' : '' }}"
                                    style="font-weight: 600; color: #1E40AF;">
                                    <i class="fas fa-user-tie mr-1"></i> Spa Receptionist
                                </a>
                            </li>
                        @else
                            <li><a href="/#features">Features</a></li>
                            <li><a href="/#services">Services</a></li>
                            <li><a href="/#about">About</a></li>
                            <li><a href="/#contact">Contact</a></li>
                        @endif
                        @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
                            <li>
                                <a href="{{ route('dashboard.index') }}" class="btn"
                                    style="background: #1E40AF; color: white; padding: 12px 30px; border-radius: 50px; font-weight: 600;">
                                    Dashboard
                                </a>
                            </li>
                        @else
                            <li class="relative">
                                <button type="button" 
                                    id="notificationToggle" 
                                    class="flex items-center focus:outline-none relative"
                                    aria-haspopup="true" 
                                    aria-expanded="false"
                                    data-dropdown-toggle="notificationDropdown">
                                    <i class="fas fa-bell text-xl text-gray-600 hover:text-gray-800"></i>
                                    @if (auth()->user()->unreadNotifications->count() > 0)
                                        <span id="notificationBadge"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full 
                                            h-5 w-5 flex items-center justify-center">
                                            {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                                        </span>
                                    @endif
                                </button>
                                <!-- Notification Dropdown -->
                                <div id="notificationDropdown" 
                                    class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 border border-gray-200"
                                    style="max-height: 400px; overflow-y: auto;"
                                    data-dropdown>
                                    <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                                        <h3 class="font-semibold text-gray-800">Notifications</h3>
                                        @if(auth()->user()->unreadNotifications->count() > 0)
                                            <form id="markAllReadForm" action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                    class="text-xs text-blue-600 hover:text-blue-800">
                                                    Mark all as read
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    <div id="notificationList">
                                        @include('partials.notifications.list', ['notifications' => auth()->user()->notifications->take(10)])
                                    </div>
                                    <div class="p-3 border-t border-gray-200 text-center">
                                        <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                            View all notifications
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endif
                        <li class="relative">
                            <button type="button" 
                                class="flex items-center focus:outline-none"
                                data-dropdown-toggle="userDropdown">
                                <span class="mr-1 text-gray-700 hover:text-gray-900">{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div id="userDropdown" 
                                class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200"
                                data-dropdown>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </li>
                    @else
                        <li><a href="/#features">Features</a></li>
                        <li><a href="/#services">Services</a></li>
                        <li><a href="/#about">About</a></li>
                        <li><a href="/#contact">Contact</a></li>
                        <li>
                            <a href="{{ route('login') }}" class="btn"
                                style="background: #1E40AF; color: white; padding: 12px 30px; border-radius: 50px; font-weight: 600;">
                                Login
                            </a>
                        </li>
                    @endauth
                </ul>
            </nav>
        </div>
    </header>

    <main style="min-h-screen; background-color: #F8FAFC;">
        @yield('content')
    </main>

    <!-- Toast Notification -->
    <div id="toast"
        class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 hidden transition-all duration-300">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span id="toast-message"></span>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')

    <script>
        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            toastMessage.textContent = message;
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }
    </script>
</body>

</html>
