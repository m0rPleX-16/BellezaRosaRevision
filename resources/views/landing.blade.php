<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>Belleza Rosa Salon - Premium Beauty Experience</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1E40AF;
            --primary-light: #3B82F6;
            --primary-dark: #1E3A8A;
            --gold: #F59E0B;
            --gold-light: #FBBF24;
            --white: #FFFFFF;
            --light: #F8FAFC;
            --gray: #64748B;
            --dark: #1E293B;
            --pink: #FBCFE8;
            --pink-light: #FDF2F8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: var(--white);
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 16px;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(30, 64, 175, 0.2);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: var(--white);
            transform: translateY(-3px);
        }

        .btn-gold {
            background: var(--gold);
            color: var(--white);
        }

        .btn-gold:hover {
            background: var(--gold-light);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(245, 158, 11, 0.2);
        }

        /* Enhanced Header & Navigation */
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            position: fixed;
            width: 100%;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-bottom: 1px solid rgba(59, 130, 246, 0.1);
        }

        header.scrolled {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.12);
            padding: 8px 0;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            position: relative;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .logo:hover {
            transform: translateY(-2px);
        }

        .logo img {
            transition: all 0.3s ease;
            filter: drop-shadow(0 2px 4px rgba(59, 130, 246, 0.2));
        }

        .logo:hover img {
            transform: scale(1.05);
            filter: drop-shadow(0 4px 8px rgba(59, 130, 246, 0.3));
        }

        .logo span {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: all 0.3s ease;
        }

        .nav-links {
            display: flex;
            list-style: none;
            align-items: center;
            gap: 8px;
        }

        .nav-links li {
            margin-left: 20px;
        }

        .nav-links a {
            font-weight: 500;
            color: var(--dark);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-links a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary), var(--gold));
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-links a:hover {
            color: var(--primary);
            background: rgba(59, 130, 246, 0.05);
            transform: translateY(-1px);
        }

        .nav-links a:hover::before {
            width: 80%;
        }

        .nav-links .btn {
            margin-left: 10px;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
            border: none;
        }

        .nav-links .btn:hover {
            box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
            transform: translateY(-2px);
        }

        .mobile-menu {
            display: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--primary);
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .mobile-menu:hover {
            background: rgba(59, 130, 246, 0.1);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 160px 0 100px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,192C1248,192,1344,128,1392,96L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: center;
        }

        .hero-content {
            max-width: 600px;
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
        }

        .hero-image {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 50%;
            max-width: 600px;
            z-index: 1;
        }

        /* Features Section */
        .features {
            padding: 100px 0;
            background: var(--light);
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 36px;
            color: var(--primary-dark);
            margin-bottom: 15px;
        }

        .section-title p {
            color: var(--gray);
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background: var(--white);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: var(--pink-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .feature-icon i {
            font-size: 30px;
            color: var(--primary);
        }

        .feature-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: var(--primary-dark);
        }

        .feature-card p {
            color: var(--gray);
        }

        /* Services Section */
        .services {
            padding: 100px 0;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .service-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-10px);
        }

        .service-image {
            height: 200px;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 24px;
        }

        .service-content {
            padding: 25px;
        }

        .service-content h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: var(--primary-dark);
        }

        .service-content p {
            color: var(--gray);
            margin-bottom: 20px;
        }

        .service-price {
            font-size: 20px;
            font-weight: 700;
            color: var(--gold);
        }

        /* CTA Section */
        .cta {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            text-align: center;
        }

        .cta h2 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .cta p {
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto 30px;
            opacity: 0.9;
        }

        /* Enhanced Footer */
        footer {
            background: linear-gradient(135deg, var(--dark) 0%, #1a1a2e 100%);
            color: var(--white);
            padding: 80px 0 30px;
            position: relative;
            overflow: hidden;
        }
        
        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            animation: shimmer 3s ease-in-out infinite;
        }
        
        @keyframes shimmer {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 50px;
            margin-bottom: 60px;
            position: relative;
            z-index: 1;
        }

        .footer-column h3 {
            font-size: 20px;
            margin-bottom: 25px;
            color: var(--gold);
            font-weight: 600;
            position: relative;
            display: inline-block;
        }
        
        .footer-column h3::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 50px;
            height: 2px;
            background: var(--gold);
            transition: width 0.3s ease;
        }
        
        .footer-column:hover h3::after {
            width: 100%;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }
        
        .footer-links li:hover {
            transform: translateX(5px);
        }

        .footer-links a {
            color: var(--light);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            display: inline-block;
        }
        
        .footer-links a::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--gold);
            transition: width 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--gold);
            transform: translateX(3px);
        }
        
        .footer-links a:hover::before {
            width: 100%;
        }
        
        .footer-links li i {
            color: var(--gold);
            margin-right: 10px;
            width: 16px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .footer-links li:hover i {
            transform: scale(1.2);
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .social-links a {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }
        
        .social-links a::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: var(--gold);
            border-radius: 50%;
            transition: all 0.4s ease;
            transform: translate(-50%, -50%);
        }
        
        .social-links a:hover {
            background: var(--gold);
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);
            border-color: var(--gold);
        }
        
        .social-links a:hover::before {
            width: 100%;
            height: 100%;
        }
        
        .social-links a i {
            color: var(--white);
            font-size: 18px;
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover i {
            transform: rotate(360deg);
        }

        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--gray);
            font-size: 14px;
            position: relative;
        }
        
        .copyright p {
            margin: 0;
            transition: all 0.3s ease;
        }
        
        .copyright:hover p {
            color: var(--gold);
        }

        /* Notification Badge */
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #EF4444;
            color: white;
            font-size: 10px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Enhanced Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        /* Enhanced Button Styles */
        .btn {
            position: relative;
            overflow: hidden;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: all 0.6s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        /* Enhanced Card Styles */
        .feature-card, .service-card {
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before, .service-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            transition: all 0.8s ease;
        }
        
        .feature-card:hover::before, .service-card:hover::before {
            top: -75%;
            left: -75%;
        }
        
        /* Enhanced Hero Section */
        .hero {
            position: relative;
            overflow: hidden;
        }
        
        .hero::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.05) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }
        
        /* Enhanced Typography */
        .section-title h2 {
            position: relative;
            display: inline-block;
        }
        
        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gold);
            transition: width 0.3s ease;
        }
        
        .section-title h2:hover::after {
            width: 100%;
        }
        
        /* Enhanced Responsive Design */
        @media (max-width: 1024px) {
            .nav-links {
                gap: 16px;
            }
            
            .hero h1 {
                font-size: 42px;
            }
            
            .hero-image {
                width: 40%;
            }
            
            .features-grid, .services-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .navbar {
                padding: 12px 0;
            }
            
            .nav-links {
                display: none !important;
            }
            
            .mobile-menu {
                display: block;
            }
            
            .mobile-nav.active {
                display: flex !important;
            }
            
            .logo span:first-child {
                font-size: 18px;
            }
            
            .logo span:last-child {
                font-size: 10px;
            }
            
            .hero {
                padding: 120px 0 60px;
                text-align: center;
            }

            .hero-content {
                max-width: 100%;
                padding: 0 20px;
            }

            .hero h1 {
                font-size: 32px;
                line-height: 1.3;
            }
            
            .hero p {
                font-size: 16px;
                margin-bottom: 25px;
            }

            .hero-image {
                display: none;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
                max-width: 280px;
                padding: 14px 24px;
                font-size: 15px;
            }
            
            .section-title h2 {
                font-size: 28px;
            }
            
            .features-grid, .services-grid {
                grid-template-columns: 1fr;
                gap: 25px;
            }
            
            .feature-card, .service-card {
                padding: 25px;
            }
            
            .cta {
                padding: 80px 0;
            }
            
            .cta h2 {
                font-size: 28px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 15px;
            }
            
            .hero {
                padding: 100px 0 50px;
            }
            
            .hero h1 {
                font-size: 28px;
                line-height: 1.4;
            }
            
            .hero p {
                font-size: 15px;
            }
            
            .section-title h2 {
                font-size: 24px;
            }
            
            .section-title p {
                font-size: 14px;
            }
            
            .feature-card, .service-card {
                padding: 20px;
                border-radius: 12px;
            }
            
            .feature-icon {
                width: 60px;
                height: 60px;
            }
            
            .feature-icon i {
                font-size: 26px;
            }
            
            .service-image {
                height: 160px;
            }
            
            .service-content {
                padding: 20px;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
                text-align: center;
            }
            
            .social-links {
                justify-content: center;
                margin-top: 20px;
            }
            
            .copyright {
                padding-top: 25px;
                font-size: 13px;
            }
        }
    </style>
</head>

<body data-auth="{{ auth()->check() ? '1' : '0' }}" 
      data-unread-url="{{ route('messages.unreadCount') }}" 
      data-messages-url="{{ route('messages.index') }}">
    <!-- Header with consistent navigation -->
    <header id="header" class="sticky top-0 z-40 bg-white border-b border-gray-100 transition-all duration-300">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <nav class="navbar" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 0;">
                <!-- Logo -->
                <a href="/" 
                   class="logo flex items-center space-x-3 group">
                    <img src="{{ asset('images/logo.png') }}" alt="Belleza Rosa Logo"
                        class="h-10 w-auto transition-transform group-hover:scale-105">
                    <div class="flex flex-col">
                        <span class="text-xl font-bold bg-gradient-to-r from-[var(--primary)] to-[var(--primary-dark)] bg-clip-text text-transparent leading-tight">
                            Belleza Rosa
                        </span>
                        <span class="text-xs text-[var(--gold)] font-medium leading-none">Premium Salon</span>
                    </div>
                </a>

                <!-- Mobile Menu Button -->
                <button type="button" id="mobileMenuBtn" class="mobile-menu p-2 rounded-lg hover:bg-gray-100 focus:outline-none">
                    <i class="fas fa-bars text-xl text-gray-700"></i>
                </button>

                <!-- Navigation Links -->
                <ul class="nav-links" style="display: flex; list-style: none; gap: 24px; align-items: center;">
                    <li><a href="#features" class="text-gray-600 hover:text-gray-900 transition-colors">Features</a></li>
                    <li><a href="#services" class="text-gray-600 hover:text-gray-900 transition-colors">Services</a></li>
                    <li><a href="#about" class="text-gray-600 hover:text-gray-900 transition-colors">About</a></li>
                    <li><a href="#contact" class="text-gray-600 hover:text-gray-900 transition-colors">Contact</a></li>

                    @auth
                        @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
                            <li>
                                <a href="{{ route('dashboard.index') }}" 
                                   class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-[var(--primary)] to-[var(--primary-dark)] text-white rounded-full font-semibold shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                                    <i class="fas fa-chart-line mr-2"></i> Dashboard
                                </a>
                            </li>
                        @else
                            <!-- Notification Bell -->
                            <li class="relative">
                                <button type="button" 
                                    id="notificationToggle" 
                                    class="relative p-2 rounded-full hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-300"
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
                            </li>
                        @endif

                        <!-- User Menu -->
                        <li class="relative">
                            <button type="button" 
                                id="userMenuToggle"
                                class="flex items-center space-x-2 p-2 rounded-full hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-300">
                                <div class="w-8 h-8 bg-gradient-to-br from-[var(--gold)] to-[var(--gold-light)] rounded-full flex items-center justify-center text-white font-semibold text-sm">
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
                        <li>
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[var(--primary)] to-[var(--primary-dark)] !text-white rounded-full font-semibold shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 hover:scale-105 border border-blue-600/20">
                                <i class="fas fa-sign-in-alt mr-2 !text-white"></i> <span class="!text-white">Login</span>
                            </a>
                        </li>
                    @endauth
                </ul>
            </nav>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobileNav" class="mobile-nav hidden flex-col bg-white border-t border-gray-100 p-4 space-y-2 md:hidden">
            <ul class="space-y-2">
                <li><a href="#features" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50">Features</a></li>
                <li><a href="#services" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50">Services</a></li>
                <li><a href="#about" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50">About</a></li>
                <li><a href="#contact" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50">Contact</a></li>
                @auth
                    <li>
                        <a href="{{ route('customer.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-home mr-3 w-5"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('customer.appointments.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-calendar-alt mr-3 w-5"></i> Appointments
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <i class="fas fa-sign-out-alt mr-3 w-5"></i> Sign Out
                            </button>
                        </form>
                    </li>
                @else
                    <li>
                        <a href="{{ route('login') }}" class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-[var(--primary)] to-[var(--primary-dark)] !text-white rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5">
                            <i class="fas fa-sign-in-alt mr-3 w-5 !text-white"></i> <span class="!text-white">Login</span>
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Transform Your Salon Management Experience</h1>
                <p>Belleza Rosa Salon System streamlines appointments, customer management, and business operations so
                    you can focus on what you do best - creating beautiful transformations.</p>
                <div class="hero-buttons">
                    <a href="{{ route('login') }}" class="btn btn-gold">Book Now</a>
                    <a href="#features" class="btn btn-outline">Learn More</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="{{ asset('images/aa.png') }}" alt="Belleza Rosa Salon"
                    class="w-full max-w-md lg:max-w-lg xl:max-w-2xl object-cover rounded-2xl">
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Powerful Features</h2>
                <p>Our comprehensive salon management system includes everything you need to run your business
                    efficiently</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>Smart Scheduling</h3>
                    <p>Easily manage appointments with our intuitive calendar system that prevents double-booking and
                        sends automatic reminders.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Customer Management</h3>
                    <p>Keep track of customer preferences, history, and to provide personalized service every time.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Business Analytics</h3>
                    <p>Gain insights into your business performance with detailed reports on revenue, popular services,
                        and staff productivity.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>Staff Management</h3>
                    <p>Assign staff to appointments, track their performance, and manage schedules with our
                        comprehensive staff tools.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3>Payment Processing</h3>
                    <p>Accept multiple payment methods and track transactions seamlessly.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Friendly</h3>
                    <p>Access your salon management system from any device with our fully responsive design that works
                        perfectly on mobile.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
        <div class="container">
            <div class="section-title">
                <h2>Our Services</h2>
                <p>Belleza Rosa Salon offers a wide range of premium beauty services</p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-image" style="background: #FBCFE8;">
                        <i class="fas fa-cut" style="color: #DB2777;"></i>
                    </div>
                    <div class="service-content">
                        <h3>Hair Styling</h3>
                        <p>From precision cuts to creative coloring, our expert stylists create looks that enhance your
                            natural beauty.</p>
                        <div class="service-price">Starting at ₱350</div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-image" style="background: #C7D2FE;">
                        <i class="fas fa-hand-sparkles" style="color: #4F46E5;"></i>
                    </div>
                    <div class="service-content">
                        <h3>Nail Care</h3>
                        <p>Pamper yourself with our luxurious manicures and pedicures using premium products for
                            long-lasting results.</p>
                        <div class="service-price">Starting at ₱250</div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Ready to Book Your Appointment?</h2>
            <p>Experience premium beauty services with our easy online booking system. Login or create an account to get started!</p>
            <a href="{{ route('login') }}" class="btn btn-gold">Book Now</a>
        </div>
    </section>

    <!-- Footer -->
    <footer id="footer">
        <div class="container">
            <div class="footer-content">
                <!-- Brand Column with Logo -->
                <div class="footer-column">
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Belleza Rosa Logo"
                            class="h-10 w-auto transition-transform hover:scale-105">
                        <span class="text-xl font-bold text-white">Belleza Rosa</span>
                    </div>
                    <p class="text-gray-300 text-sm leading-relaxed">
                        Premium salon management system designed to help beauty professionals focus on their craft while
                        we handle the business side.
                    </p>
                    <div class="social-links">
                        <a href="https://www.facebook.com/bellezarosasalon" target="_blank" rel="noopener noreferrer" 
                           class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center text-white hover:bg-white hover:text-blue-600 transition-all duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center text-white hover:bg-white hover:text-pink-600 transition-all duration-300">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center text-white hover:bg-white hover:text-blue-400 transition-all duration-300">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center text-white hover:bg-white hover:text-red-600 transition-all duration-300">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links Column -->
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#features">Features</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}">Terms of Service</a></li>
                    </ul>
                </div>
                
                <!-- Services Column -->
                <div class="footer-column">
                    <h3>Our Services</h3>
                    <ul class="footer-links">
                        <li><a href="#services">Hair Styling</a></li>
                        <li><a href="#services">Nail Care</a></li>
                        <li><a href="#services">Skin Treatment</a></li>
                        <li><a href="#services">Makeup Services</a></li>
                        <li><a href="#services">Spa Packages</a></li>
                        <li><a href="{{ route('customer.staff') }}">Book Now</a></li>
                    </ul>
                </div>
                
                <!-- Contact Column -->
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <ul class="footer-links">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-[var(--gold)] mr-3 w-4 mt-1"></i>
                            <span>2nd Floor Victoria Plaza, Davao City</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone text-[var(--gold)] mr-3 w-4"></i>
                            <span>(02) 8123-4567</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-[var(--gold)] mr-3 w-4"></i>
                            <span>bellezarosa@gmail.com</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock text-[var(--gold)] mr-3 w-4"></i>
                            <span>Mon-Sat: 9AM-8PM</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-globe text-[var(--gold)] mr-3 w-4"></i>
                            <span>www.bellezarosa.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                <p class="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-4">
                    <span>&copy; {{ date('Y') }} Belleza Rosa Salon. All rights reserved.</span>
                    <span class="text-[var(--gold)]">|</span>
                    <span>Made with <i class="fas fa-heart text-red-500 mx-1"></i> for beauty professionals</span>
                </p>
            </div>
        </div>
    </footer>

    <!-- Booking Modal Removed - Users now redirect to login page -->
    {{-- @livewire('guest-booking-modal') --}}
    <!-- Toast Notification -->
    <div id="toast"
        class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 hidden transition-all duration-300">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span id="toast-message"></span>
        </div>
    </div>
    <script>
        // Enhanced Mobile Menu Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileNav = document.getElementById('mobileNav');
            const navLinks = document.querySelector('.nav-links');
            
            if (mobileMenuBtn && mobileNav) {
                mobileMenuBtn.addEventListener('click', function() {
                    mobileNav.classList.toggle('hidden');
                    mobileNav.classList.toggle('flex');
                    
                    // Animate menu button
                    const icon = this.querySelector('i');
                    if (mobileNav.classList.contains('flex')) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                    } else {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                });
            }
            
            // Close mobile menu when clicking on links
            const mobileNavLinks = mobileNav?.querySelectorAll('a');
            if (mobileNavLinks) {
                mobileNavLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        mobileNav.classList.add('hidden');
                        mobileNav.classList.remove('flex');
                        const icon = mobileMenuBtn.querySelector('i');
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    });
                });
            }
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (mobileNav && !mobileNav.classList.contains('hidden') && 
                    !mobileNav.contains(event.target) && 
                    !mobileMenuBtn.contains(event.target)) {
                    mobileNav.classList.add('hidden');
                    mobileNav.classList.remove('flex');
                    const icon = mobileMenuBtn.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
            
            // Enhanced scroll effects
            let lastScrollTop = 0;
            window.addEventListener('scroll', function() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                const header = document.getElementById('header');
                
                if (header) {
                    if (scrollTop > 50) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                    
                    // Hide/show header on scroll
                    if (scrollTop > lastScrollTop && scrollTop > 100) {
                        header.style.transform = 'translateY(-100%)';
                    } else {
                        header.style.transform = 'translateY(0)';
                    }
                }
                
                lastScrollTop = scrollTop;
            });
            
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        const headerHeight = document.getElementById('header')?.offsetHeight || 80;
                        const targetPosition = targetElement.offsetTop - headerHeight - 20;
                        
                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });
                        
                        // Close mobile menu if open
                        if (mobileNav && !mobileNav.classList.contains('hidden')) {
                            mobileNav.classList.add('hidden');
                            mobileNav.classList.remove('flex');
                            const icon = mobileMenuBtn.querySelector('i');
                            icon.classList.remove('fa-times');
                            icon.classList.add('fa-bars');
                        }
                    }
                });
            });
            
            // Add touch support for mobile
            if ('ontouchstart' in window) {
                document.body.classList.add('touch-device');
            }
        });

        // Toast Notification Function
        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');

            toastMessage.textContent = message;
            toast.classList.remove('hidden');

            setTimeout(() => {
                toast.classList.add('hidden');
            }, 5000); // Toast stays 5 seconds
        }

        // Listen for toast events
        window.addEventListener('toast', (event) => {
            showToast(event.detail.message);
        });

        // Listen for Livewire flash messages
        window.addEventListener('show-toast', (event) => {
            showToast(event.detail.message);
        });

        // Open Booking Modal
        function openBookingModal() {
            window.dispatchEvent(new CustomEvent('open-booking-modal'));
        }

        // Check if user is authenticated and set URLs from data attributes
        const bodyEl = document.body;
        const isAuthenticated = bodyEl.dataset.auth === '1';
        const unreadCountUrl = bodyEl.dataset.unreadUrl;
        const messagesIndexUrl = bodyEl.dataset.messagesUrl;

        // Update notification badge periodically
        function updateNotificationBadge() {
            if (!isAuthenticated) return;
            
            fetch(unreadCountUrl)
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notificationBadge');
                    const total = data.total_unread;

                    if (total > 0) {
                        if (!badge) {
                            // Create badge if it doesn't exist
                            const bellLink = document.querySelector('a[href="' + messagesIndexUrl + '"]');
                            if (bellLink) {
                                const newBadge = document.createElement('span');
                                newBadge.id = 'notificationBadge';
                                newBadge.className = 'notification-badge';
                                newBadge.textContent = total > 9 ? '9+' : total;
                                bellLink.appendChild(newBadge);
                            }
                        } else {
                            badge.textContent = total > 9 ? '9+' : total;
                        }
                    } else if (badge) {
                        badge.remove();
                    }
                })
                .catch(error => {
                    console.error('Error fetching unread count:', error);
                });
        }

        // Update every 30 seconds (only if authenticated)
        if (isAuthenticated) {
            setInterval(updateNotificationBadge, 30000);

            // Initial update
            document.addEventListener('DOMContentLoaded', updateNotificationBadge);
        }
    </script>
    <script>
        // Enhanced Page Animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate elements on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, observerOptions);
            
            // Observe all feature cards and service cards
            document.querySelectorAll('.feature-card, .service-card').forEach(el => {
                observer.observe(el);
            });
            
            // Smooth scroll for navigation
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
            
            // Parallax effect for hero section
            window.addEventListener('scroll', function() {
                const scrolled = window.pageYOffset;
                const hero = document.querySelector('.hero');
                const heroImage = document.querySelector('.hero-image');
                if (hero && heroImage) {
                    const speed = 0.5;
                    heroImage.style.transform = `translateY(${scrolled * speed}px)`;
                }
            });
            
            // Add hover effects to buttons
            document.querySelectorAll('.btn').forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
            
            // Counter animation for stats
            const animateCounter = (element, target, duration = 2000) => {
                let start = 0;
                let end = parseInt(element.textContent);
                const increment = (end - start) / (duration / 16);
                
                const timer = setInterval(() => {
                    start += increment;
                    element.textContent = Math.floor(start);
                    if (start >= end) {
                        clearInterval(timer);
                    }
                }, 16);
            };
            
            // Initialize counters if they exist
            const statNumbers = document.querySelectorAll('.feature-card h3 span, .service-price');
            statNumbers.forEach(el => {
                if (el && el.textContent) {
                    const finalValue = parseInt(el.textContent);
                    el.textContent = '0';
                    setTimeout(() => animateCounter(el, finalValue), 100);
                }
            });
        });
    </script>
</body>

</html>
