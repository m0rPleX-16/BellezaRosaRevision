<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Belleza Rosa Spa'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Smooth dropdown transitions */
        [data-dropdown] {
            transition: opacity 0.2s ease, transform 0.2s ease;
            transform-origin: top right;
        }
        
        [data-dropdown].hidden {
            opacity: 0;
            transform: scale(0.95);
            pointer-events: none;
        }
        
        [data-dropdown]:not(.hidden) {
            opacity: 1;
            transform: scale(1);
        }
        
        /* Navigation active state */
        .nav-link.active {
            color: #1E40AF !important;
            font-weight: 600;
        }
        
        /* Header shadow on scroll */
        #header.scrolled {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.98);
        }
        
        /* Mobile menu styles */
        .mobile-menu {
            display: none;
        }
        
        @media (max-width: 768px) {
            .nav-links {
                display: none !important;
            }
            
            .mobile-menu {
                display: block;
            }
            
            .mobile-nav.active {
                display: flex !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body class="font-sans antialiased">
    <!-- Header with improved navigation -->
    <header id="header" class="sticky top-0 z-40 bg-white border-b border-gray-100 transition-all duration-300">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <nav class="navbar" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 0;">
                <!-- Logo -->
                <a href="{{ auth()->check() && auth()->user()->isCustomer() ? route('customer.dashboard') : '/' }}" 
                   class="logo flex items-center space-x-2 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow">
                        <i class="fas fa-spa text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                        Belleza Rosa
                    </span>
                </a>

                <!-- Mobile Menu Button -->
                <button type="button" id="mobileMenuBtn" class="mobile-menu p-2 rounded-lg hover:bg-gray-100 focus:outline-none">
                    <i class="fas fa-bars text-xl text-gray-700"></i>
                </button>

                <!-- Navigation Links -->
                <ul class="nav-links" style="display: flex; list-style: none; gap: 24px; align-items: center;">
                    @auth
                        @if (auth()->user()->isCustomer())
                            <li>
                                <a href="{{ route('customer.dashboard') }}"
                                    class="nav-link flex items-center px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('customer.dashboard') ? 'bg-pink-50 text-pink-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                    <i class="fas fa-home mr-2"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('customer.appointments.index') }}"
                                    class="nav-link flex items-center px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('customer.appointments.*') ? 'bg-pink-50 text-pink-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                    <i class="fas fa-calendar-alt mr-2"></i> Appointments
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('customer.staff') }}"
                                    class="nav-link flex items-center px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('customer.staff*') ? 'bg-pink-50 text-pink-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                    <i class="fas fa-spa mr-2"></i> Book Now
                                </a>
                            </li>
                        @else
                            <li><a href="/#features" class="text-gray-600 hover:text-gray-900 transition-colors">Features</a></li>
                            <li><a href="/#services" class="text-gray-600 hover:text-gray-900 transition-colors">Services</a></li>
                            <li><a href="/#about" class="text-gray-600 hover:text-gray-900 transition-colors">About</a></li>
                            <li><a href="/#contact" class="text-gray-600 hover:text-gray-900 transition-colors">Contact</a></li>
                        @endif

                        @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
                            <li>
                                <a href="{{ route('dashboard.index') }}" 
                                   class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-pink-500 to-rose-600 text-white rounded-full font-semibold shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                                    <i class="fas fa-chart-line mr-2"></i> Dashboard
                                </a>
                            </li>
                        @else
                            <!-- Notification Bell -->
                            <li class="relative">
                                <button type="button" 
                                    id="notificationToggle" 
                                    class="relative p-2 rounded-full hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-pink-300"
                                    aria-haspopup="true" 
                                    aria-expanded="false">
                                    <i class="fas fa-bell text-xl text-gray-600"></i>
                                    @if (auth()->user()->unreadNotifications->count() > 0)
                                        <span id="notificationBadge"
                                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full 
                                            h-5 w-5 flex items-center justify-center animate-pulse">
                                            {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                                        </span>
                                    @endif
                                </button>
                                <!-- Notification Dropdown -->
                                <div id="notificationDropdown" 
                                    class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl z-50 border border-gray-100 overflow-hidden"
                                    data-dropdown>
                                    <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-pink-50 to-rose-50 flex justify-between items-center">
                                        <h3 class="font-bold text-gray-800">
                                            <i class="fas fa-bell mr-2 text-pink-500"></i>Notifications
                                        </h3>
                                        @if(auth()->user()->unreadNotifications->count() > 0)
                                            <form id="markAllReadForm" action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-xs text-pink-600 hover:text-pink-800 font-medium">
                                                    Mark all as read
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    <div id="notificationList" class="max-h-80 overflow-y-auto">
                                        @include('partials.notifications.list', ['notifications' => auth()->user()->notifications->take(10)])
                                    </div>
                                    <div class="p-3 border-t border-gray-100 text-center bg-gray-50">
                                        <a href="{{ route('notifications.index') }}" class="text-sm text-pink-600 hover:text-pink-800 font-medium">
                                            View all notifications <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endif

                        <!-- User Menu -->
                        <li class="relative">
                            <button type="button" 
                                id="userMenuToggle"
                                class="flex items-center space-x-2 p-2 rounded-full hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-pink-300">
                                <div class="w-8 h-8 bg-gradient-to-br from-pink-400 to-rose-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="text-gray-700 font-medium hidden sm:block">{{ auth()->user()->first_name ?? auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                            </button>
                            <div id="userDropdown" 
                                class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl py-2 z-50 border border-gray-100"
                                data-dropdown>
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->full_name ?? auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                @if(auth()->user()->isCustomer())
                                    <a href="{{ route('customer.profile.edit') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-user-circle mr-3 text-gray-400 w-5"></i> My Profile
                                    </a>
                                    <a href="{{ route('customer.appointments.index') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-calendar-check mr-3 text-gray-400 w-5"></i> My Appointments
                                    </a>
                                    <hr class="my-2 border-gray-100">
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt mr-3 w-5"></i> Sign Out
                                    </button>
                                </form>
                            </div>
                        </li>
                    @else
                        <li><a href="/#features" class="text-gray-600 hover:text-gray-900 transition-colors">Features</a></li>
                        <li><a href="/#services" class="text-gray-600 hover:text-gray-900 transition-colors">Services</a></li>
                        <li><a href="/#about" class="text-gray-600 hover:text-gray-900 transition-colors">About</a></li>
                        <li><a href="/#contact" class="text-gray-600 hover:text-gray-900 transition-colors">Contact</a></li>
                        <li>
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-pink-500 to-rose-600 text-white rounded-full font-semibold shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                                <i class="fas fa-sign-in-alt mr-2"></i> Login
                            </a>
                        </li>
                    @endauth
                </ul>
            </nav>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobileNav" class="mobile-nav hidden flex-col bg-white border-t border-gray-100 p-4 space-y-2 md:hidden">
            @auth
                @if (auth()->user()->isCustomer())
                    <a href="{{ route('customer.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('customer.dashboard') ? 'bg-pink-50 text-pink-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-home mr-3 w-5"></i> Dashboard
                    </a>
                    <a href="{{ route('customer.appointments.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('customer.appointments.*') ? 'bg-pink-50 text-pink-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-calendar-alt mr-3 w-5"></i> Appointments
                    </a>
                    <a href="{{ route('customer.staff') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('customer.staff*') ? 'bg-pink-50 text-pink-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-spa mr-3 w-5"></i> Book Now
                    </a>
                    <a href="{{ route('customer.profile.edit') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('customer.profile.*') ? 'bg-pink-50 text-pink-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-user-circle mr-3 w-5"></i> Profile
                    </a>
                @endif
                <hr class="border-gray-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-3 rounded-lg text-red-600 hover:bg-red-50">
                        <i class="fas fa-sign-out-alt mr-3 w-5"></i> Sign Out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-pink-500 to-rose-600 text-white rounded-lg font-semibold">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </a>
            @endauth
        </div>
    </header>

    <main class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
        <!-- Flash Messages -->
        @if (session('success'))
            <div id="successAlert" class="fixed top-20 right-4 z-50 max-w-md">
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl shadow-lg flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                    <button onclick="document.getElementById('successAlert').remove()" class="ml-auto pl-3">
                        <i class="fas fa-times text-green-400 hover:text-green-600"></i>
                    </button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div id="errorAlert" class="fixed top-20 right-4 z-50 max-w-md">
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-lg flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('error') }}</p>
                    </div>
                    <button onclick="document.getElementById('errorAlert').remove()" class="ml-auto pl-3">
                        <i class="fas fa-times text-red-400 hover:text-red-600"></i>
                    </button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer for Customer Pages -->
    @auth
        @if (auth()->user()->isCustomer())
            <footer class="bg-white border-t border-gray-100">
                <div class="max-w-7xl mx-auto px-4 py-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <!-- Brand -->
                        <div class="col-span-1 md:col-span-2">
                            <div class="flex items-center space-x-2 mb-4">
                                <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-spa text-white text-lg"></i>
                                </div>
                                <span class="text-xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                                    Belleza Rosa
                                </span>
                            </div>
                            <p class="text-gray-500 text-sm max-w-md">
                                Your premier destination for beauty and wellness. Experience luxury treatments that rejuvenate your body and mind.
                            </p>
                        </div>
                        
                        <!-- Quick Links -->
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Quick Links</h4>
                            <ul class="space-y-2">
                                <li><a href="{{ route('customer.dashboard') }}" class="text-gray-500 hover:text-pink-600 text-sm transition-colors">Dashboard</a></li>
                                <li><a href="{{ route('customer.appointments.index') }}" class="text-gray-500 hover:text-pink-600 text-sm transition-colors">My Appointments</a></li>
                                <li><a href="{{ route('customer.staff') }}" class="text-gray-500 hover:text-pink-600 text-sm transition-colors">Book a Service</a></li>
                                <li><a href="{{ route('customer.profile.edit') }}" class="text-gray-500 hover:text-pink-600 text-sm transition-colors">My Profile</a></li>
                            </ul>
                        </div>
                        
                        <!-- Contact -->
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Contact Us</h4>
                            <ul class="space-y-2 text-sm text-gray-500">
                                <li class="flex items-center">
                                    <i class="fas fa-phone mr-2 text-pink-400 w-4"></i>
                                    <span>(+63) 123-456-7890</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-envelope mr-2 text-pink-400 w-4"></i>
                                    <span>hello@bellezarosa.com</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-map-marker-alt mr-2 text-pink-400 w-4 mt-1"></i>
                                    <span>123 Beauty Lane, Makati City</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-100 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center">
                        <p class="text-sm text-gray-400">&copy; {{ date('Y') }} Belleza Rosa Spa. All rights reserved.</p>
                        <div class="flex space-x-4 mt-4 md:mt-0">
                            <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 hover:bg-pink-100 hover:text-pink-500 transition-colors">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 hover:bg-pink-100 hover:text-pink-500 transition-colors">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 hover:bg-pink-100 hover:text-pink-500 transition-colors">
                                <i class="fab fa-twitter"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </footer>
        @endif
    @endauth

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-4 right-4 z-50 hidden transition-all duration-300 transform translate-y-2 opacity-0">
        <div class="bg-gray-900 text-white px-6 py-3 rounded-xl shadow-2xl flex items-center">
            <i id="toast-icon" class="fas fa-check-circle mr-3 text-green-400"></i>
            <span id="toast-message" class="font-medium"></span>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')

    <script>
        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            const toastIcon = document.getElementById('toast-icon');
            
            toastMessage.textContent = message;
            
            // Update icon based on type
            if (type === 'error') {
                toastIcon.className = 'fas fa-exclamation-circle mr-3 text-red-400';
            } else if (type === 'warning') {
                toastIcon.className = 'fas fa-exclamation-triangle mr-3 text-yellow-400';
            } else {
                toastIcon.className = 'fas fa-check-circle mr-3 text-green-400';
            }
            
            toast.classList.remove('hidden', 'translate-y-2', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            
            setTimeout(() => {
                toast.classList.add('translate-y-2', 'opacity-0');
                toast.classList.remove('translate-y-0', 'opacity-100');
                setTimeout(() => toast.classList.add('hidden'), 300);
            }, 3000);
        }

        // Dropdown toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Notification dropdown toggle
            const notificationToggle = document.getElementById('notificationToggle');
            const notificationDropdown = document.getElementById('notificationDropdown');
            
            if (notificationToggle && notificationDropdown) {
                notificationToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationDropdown.classList.toggle('hidden');
                    // Close user dropdown if open
                    const userDropdown = document.getElementById('userDropdown');
                    if (userDropdown) userDropdown.classList.add('hidden');
                });
            }
            
            // User menu dropdown toggle
            const userMenuToggle = document.getElementById('userMenuToggle');
            const userDropdown = document.getElementById('userDropdown');
            
            if (userMenuToggle && userDropdown) {
                userMenuToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userDropdown.classList.toggle('hidden');
                    // Close notification dropdown if open
                    if (notificationDropdown) notificationDropdown.classList.add('hidden');
                });
            }
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (notificationDropdown && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.add('hidden');
                }
                if (userDropdown && !userDropdown.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
            
            // Mobile menu toggle
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileNav = document.getElementById('mobileNav');
            
            if (mobileMenuBtn && mobileNav) {
                mobileMenuBtn.addEventListener('click', function() {
                    mobileNav.classList.toggle('active');
                    const icon = mobileMenuBtn.querySelector('i');
                    if (mobileNav.classList.contains('active')) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                    } else {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                });
            }
            
            // Header scroll effect
            const header = document.getElementById('header');
            if (header) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 10) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                });
            }
            
            // Auto-hide flash messages after 5 seconds
            setTimeout(() => {
                const successAlert = document.getElementById('successAlert');
                const errorAlert = document.getElementById('errorAlert');
                if (successAlert) successAlert.remove();
                if (errorAlert) errorAlert.remove();
            }, 5000);
        });
    </script>
</body>

</html>
