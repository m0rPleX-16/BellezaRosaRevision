@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Back Navigation -->
    <div class="mb-6">
        <a href="{{ url()->previous() ?: '/' }}" 
           class="inline-flex items-center text-gray-600 hover:text-[var(--primary)] transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back
        </a>
    </div>

    <!-- Terms of Service Header -->
    <div class="bg-gradient-to-r from-[var(--primary)] to-[var(--primary-dark)] rounded-2xl p-8 text-white mb-8">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-file-contract text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold mb-2">Terms of Service</h1>
                <p class="text-white/80">Last updated: {{ date('F j, Y') }}</p>
            </div>
        </div>
        <p class="text-white/90 leading-relaxed">
            Welcome to Belleza Rosa Salon! These Terms of Service govern your use of our salon management system 
            and booking services. By accessing or using our services, you agree to be bound by these terms.
        </p>
    </div>

    <!-- Terms Content -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <div class="prose prose-lg max-w-none">
            <!-- Acceptance of Terms -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-[var(--primary)] mb-4 flex items-center">
                    <i class="fas fa-check-circle mr-3 text-[var(--gold)]"></i>
                    Acceptance of Terms
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>
                        By accessing and using Belleza Rosa Salon services, you acknowledge that you have read, understood, 
                        and agree to be bound by these Terms of Service.
                    </p>
                    <div class="bg-amber-50 rounded-xl p-4 border border-amber-200">
                        <p class="text-amber-800">
                            <strong>Important:</strong> If you do not agree to these terms, you may not access or use our services.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Services Description -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-[var(--primary)] mb-4 flex items-center">
                    <i class="fas fa-spa mr-3 text-[var(--gold)]"></i>
                    Services Description
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>
                        Belleza Rosa Salon provides an online platform for:
                    </p>
                    <ul class="list-disc list-inside space-y-2 ml-6">
                        <li><strong class="text-[var(--primary)]">Appointment Booking:</strong> Scheduling beauty and wellness services</li>
                        <li><strong class="text-[var(--primary)]">Staff Management:</strong> Connecting clients with professional stylists</li>
                        <li><strong class="text-[var(--primary)]">Service Catalog:</strong> Browse available treatments and packages</li>
                        <li><strong class="text-[var(--primary)]">Payment Processing:</strong> Secure online payment for services</li>
                        <li><strong class="text-[var(--primary)]">Customer Account:</strong> Personal profiles and booking history</li>
                    </ul>
                </div>
            </section>

            <!-- User Accounts and Registration -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-[var(--primary)] mb-4 flex items-center">
                    <i class="fas fa-user-plus mr-3 text-[var(--gold)]"></i>
                    User Accounts and Registration
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>To use our services, you must:</p>
                    <ul class="list-disc list-inside space-y-2 ml-6">
                        <li><strong class="text-[var(--primary)]">Be of Legal Age:</strong> 18 years or older, or have parental consent</li>
                        <li><strong class="text-[var(--primary)]">Provide Accurate Information:</strong> Complete and truthful registration details</li>
                        <li><strong class="text-[var(--primary)]">Create Secure Password:</strong> Protect your account with a strong password</li>
                        <li><strong class="text-[var(--primary)]">Maintain Account Security:</strong> Keep login credentials confidential</li>
                        <li><strong class="text-[var(--primary)]">Update Information:</strong> Keep contact details current and accurate</li>
                    </ul>
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <p class="text-blue-800">
                            <strong>Security Note:</strong> You are responsible for all activities under your account.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Booking and Payment Terms -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-[var(--primary)] mb-4 flex items-center">
                    <i class="fas fa-calendar-check mr-3 text-[var(--gold)]"></i>
                    Booking and Payment Terms
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>Regarding appointments and payments:</p>
                    <ul class="list-disc list-inside space-y-2 ml-6">
                        <li><strong class="text-[var(--primary)]">Advance Booking:</strong> Book appointments at least 24 hours in advance when possible</li>
                        <li><strong class="text-[var(--primary)]">Payment Terms:</strong> Full payment required at time of service unless otherwise specified</li>
                        <li><strong class="text-[var(--primary)]">Cancellation Policy:</strong> 24-hour notice required for cancellations to avoid fees</li>
                        <li><strong class="text-[var(--primary)]">No-show Policy:</strong> Late cancellations may result in service denial</li>
                        <li><strong class="text-[var(--primary)]">Price Changes:</strong> Prices subject to change without prior notice</li>
                        <li><strong class="text-[var(--primary)]">Payment Methods:</strong> We accept cash, credit cards, and digital payments</li>
                    </ul>
                </div>
            </section>

            <!-- User Conduct -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-[var(--primary)] mb-4 flex items-center">
                    <i class="fas fa-handshake mr-3 text-[var(--gold)]"></i>
                    User Conduct
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>As a user of Belleza Rosa Salon services, you agree to:</p>
                    <ul class="list-disc list-inside space-y-2 ml-6">
                        <li><strong class="text-[var(--primary)]">Professional Conduct:</strong> Treat staff and other clients with respect</li>
                        <li><strong class="text-[var(--primary)]Timeliness:</strong> Arrive on time for scheduled appointments</li>
                        <li><strong class="text-[var(--primary)]">Communication:</strong> Provide accurate contact information for appointment confirmations</li>
                        <li><strong class="text-[var(--primary)]">Facility Rules:</strong> Follow salon policies and guidelines</li>
                        <li><strong class="text-[var(--primary)]>Prohibited Activities:</strong> No illegal, harmful, or disruptive behavior</li>
                    </ul>
                </div>
            </section>

            <!-- Intellectual Property -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-[var(--primary)] mb-4 flex items-center">
                    <i class="fas fa-copyright mr-3 text-[var(--gold)]"></i>
                    Intellectual Property
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>
                        All content, trademarks, and intellectual property on Belleza Rosa Salon platform belong to us or our licensors:
                    </p>
                    <ul class="list-disc list-inside space-y-2 ml-6">
                        <li><strong class="text-[var(--primary)]">Platform Content:</strong> Website design, text, graphics, and software</li>
                        <li><strong class="text-[var(--primary)]">Service Information:</strong> Service descriptions, pricing, and availability</li>
                        <li><strong class="text-[var(--primary)]">Brand Elements:</strong> Logo, name, and distinctive brand features</li>
                        <li><strong class="text-[var(--primary)]>Usage Rights:</strong> Limited to personal, non-commercial use</li>
                        <li><strong class="text-[var(--primary)]>Prohibited Use:</strong> No reproduction, distribution, or modification without permission</li>
                    </ul>
                </div>
            </section>

            <!-- Limitation of Liability -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-[var(--primary)] mb-4 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3 text-[var(--gold)]"></i>
                    Limitation of Liability
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>
                        To the fullest extent permitted by law, Belleza Rosa Salon shall not be liable for:
                    </p>
                    <ul class="list-disc list-inside space-y-2 ml-6">
                        <li><strong class="text-[var(--primary)]">Service Quality:</strong> Individual results may vary based on personal factors</li>
                        <li><strong class="text-[var(--primary)]">Indirect Damages:</strong> Not liable for consequential or incidental damages</li>
                        <li><strong class="text-[var(--primary)]">Service Interruptions:</strong> Not responsible for temporary service disruptions</li>
                        <li><strong class="text-[var(--primary)]">Third-party Actions:</strong> Not liable for third-party service provider conduct</li>
                        <li><strong class="text-[var(--primary)]>Maximum Liability:</strong> Limited to amount paid for specific service</li>
                    </ul>
                </div>
            </section>

            <!-- Termination -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-[var(--primary)] mb-4 flex items-center">
                    <i class="fas fa-times-circle mr-3 text-[var(--gold)]"></i>
                    Termination
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>
                        We may terminate or suspend your account immediately, without prior notice or liability, for any reason, including:
                    </p>
                    <ul class="list-disc list-inside space-y-2 ml-6">
                        <li><strong class="text-[var(--primary)]">Breach of Terms:</strong> Violation of any provision in these terms</li>
                        <li><strong class="text-[var(--primary)]">Illegal Activity:</strong> Use of services for unlawful purposes</li>
                        <li><strong class="text-[var(--primary)]">Fraud:</strong> Fraudulent or deceptive practices</li>
                        <li><strong class="text-[var(--primary)]>Service Abuse:</strong> Harmful or disruptive behavior</li>
                        <li><strong class="text-[var(--primary)]>Non-payment:</strong> Failure to pay for services rendered</li>
                    </ul>
                </div>
            </section>

            <!-- Changes to Terms -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-[var(--primary)] mb-4 flex items-center">
                    <i class="fas fa-sync-alt mr-3 text-[var(--gold)]"></i>
                    Changes to Terms
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>
                        We reserve the right to modify these Terms of Service at any time. Changes will be effective immediately upon posting.
                    </p>
                    <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                        <p class="text-green-800">
                            <strong>Notification:</strong> Continued use of services constitutes acceptance of any changes.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Contact Information -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-[var(--primary)] mb-4 flex items-center">
                    <i class="fas fa-envelope mr-3 text-[var(--gold)]"></i>
                    Contact Us
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>
                        If you have any questions about these Terms of Service, please contact us:
                    </p>
                    <div class="bg-blue-50 rounded-xl p-6 border border-blue-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-[var(--primary)] mr-3"></i>
                                <span><strong>Email:</strong> bellezarosa@gmail.com</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-phone text-[var(--primary)] mr-3"></i>
                                <span><strong>Phone:</strong> (02) 8123-4567</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-[var(--primary)] mr-3"></i>
                                <span><strong>Address:</strong> 2nd Floor Victoria Plaza, Davao City</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-globe text-[var(--primary)] mr-3"></i>
                                <span><strong>Website:</strong> www.bellezarosa.com</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
