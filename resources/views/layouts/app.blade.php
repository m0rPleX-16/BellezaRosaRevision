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
                <ul class="nav-links" style="display: flex; list-style: none; gap: 30px;">
                    <li><a href="/#features">Features</a></li>
                    <li><a href="/#services">Services</a></li>
                    <li><a href="/#about">About</a></li>
                    <li><a href="/#contact">Contact</a></li>

                    @auth
                        @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
                            <li><a href="{{ route('dashboard.index') }}" class="btn"
                                    style="background: #1E40AF; color: white; padding: 12px 30px; border-radius: 50px; font-weight: 600;">Dashboard</a>
                            </li>
                        @else
                            <li>
                                <div style="position: relative;">
                                    <a href="{{ route('messages.index') }}"
                                        style="display: flex; align-items: center; position: relative;">
                                        <i class="fas fa-bell" style="font-size: 20px;"></i>
                                        @if (auth()->user()->total_unread_count > 0)
                                            <span class="notification-badge" id="notificationBadge"
                                                style="position: absolute; top: -8px; right: -8px; background: #EF4444; color: white; font-size: 10px; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                {{ auth()->user()->total_unread_count > 9 ? '9+' : auth()->user()->total_unread_count }}
                                            </span>
                                        @endif
                                    </a>
                                </div>
                            </li>
                        @endif

                        <li>
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit"
                                    style="background: none; border: none; color: inherit; cursor: pointer; font: inherit;">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}" class="btn"
                                style="background: #1E40AF; color: white; padding: 12px 30px; border-radius: 50px; font-weight: 600;">Login</a>
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
