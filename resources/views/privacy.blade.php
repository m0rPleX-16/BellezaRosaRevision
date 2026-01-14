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

    <!-- Privacy Policy Header -->
    <div class="bg-gradient-to-r from-[var(--primary)] to-[var(--primary-dark)] rounded-2xl p-8 text-white mb-8">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-shield-alt text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold mb-2">Privacy Policy</h1>
                <p class="text-white/80">Last updated: {{ date('F j, Y') }}</p>
            </div>
        </div>
        <p class="text-white/90 leading-relaxed">
            At Belleza Rosa Salon, we are committed to protecting your privacy and ensuring the security of your personal information. 
            This Privacy Policy outlines how we collect, use, and safeguard your data when you use our salon management system.
        </p>
    </div>

    <!-- Privacy Policy Content -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <div class="prose prose-lg max-w-none">
            <!-- Information We Collect -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-[var(--primary)] mb-4 flex items-center">
                    <i class="fas fa-database mr-3 text-[var(--gold)]"></i>
                    Information We Collect
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>
                        We collect information you provide directly to us when you use our services, including:
                    </p>
                    <ul class="list-disc list-inside space-y-2 ml-6">
                        <li><strong class="text-[var(--primary)]">Personal Information:</strong> Name, email address, phone number, and contact details</li>
                        <li><strong class="text-[var(--primary)]">Appointment Data:</strong> Service preferences, booking history, and appointment schedules</li>
                        <li><strong class="text-[var(--primary)]">Payment Information:</strong> Billing details and payment method information</li>
                        <li><strong class="text-[var(--primary)]">Usage Data:</strong> How you interact with our services and features</li>
                        <li><strong class="text-[var(--primary)]">Device Information:</strong> IP address, browser type, and device identifiers</li>
                    </ul>
                </div>
            </section>

            <!-- How We Use Your Information -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-[var(--primary)] mb-4 flex items-center">
                    <i class="fas fa-cogs mr-3 text-[var(--gold)]"></i>
                    How We Use Your Information
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>We use your information to:</p>
                    <ul class="list-disc list-inside space-y-2 ml-6">
                        <li><strong class="text-[var(--primary)]">Provide Services:</strong> Manage appointments, send reminders, and deliver salon services</li>
                        <li><strong class="text-[var(--primary)]">Improve User Experience:</strong> Personalize content and optimize our platform</li>
                        <li><strong class="text-[var(--primary)]">Communicate:</strong> Send appointment confirmations and important updates</li>
                        <li><strong class="text-[var(--primary)]">Process Payments:</strong> Handle billing and payment processing securely</li>
                        <li><strong class="text-[var(--primary)]">Analytics:</strong> Analyze usage patterns to improve our services</li>
                    </ul>
                </div>
            </section>

            <!-- Data Sharing and Disclosure -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-[var(--primary)] mb-4 flex items-center">
                    <i class="fas fa-share-alt mr-3 text-[var(--gold)]"></i>
                    Data Sharing and Disclosure
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>
                        We do not sell, rent, or trade your personal information. We may share your data only in the following circumstances:
                    </p>
                    <ul class="list-disc list-inside space-y-2 ml-6">
                        <li><strong class="text-[var(--primary)]">Service Providers:</strong> With salon staff to provide your booked services</li>
                        <li><strong class="text-[var(--primary)]">Payment Processors:</strong> With payment gateways to process transactions</li>
                        <li><strong class="text-[var(--primary)]">Legal Requirements:</strong> When required by law or to protect our rights</li>
                        <li><strong class="text-[var(--primary)]">Business Transfers:</strong> In case of merger, acquisition, or asset sale</li>
                    </ul>
                </div>
            </section>

            <!-- Data Security -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-[var(--primary)] mb-4 flex items-center">
                    <i class="fas fa-lock mr-3 text-[var(--gold)]"></i>
                    Data Security
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>
                        We implement appropriate security measures to protect your personal information:
                    </p>
                    <ul class="list-disc list-inside space-y-2 ml-6">
                        <li><strong class="text-[var(--primary)]">Encryption:</strong> SSL/TLS encryption for data transmission</li>
                        <li><strong class="text-[var(--primary)]">Access Controls:</strong> Restricted access to authorized personnel only</li>
                        <li><strong class="text-[var(--primary)]">Regular Updates:</strong> Security patches and system updates</li>
                        <li><strong class="text-[var(--primary)]">Monitoring:</strong> 24/7 security monitoring and threat detection</li>
                    </ul>
                </div>
            </section>

            <!-- Your Rights -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-[var(--primary)] mb-4 flex items-center">
                    <i class="fas fa-user-shield mr-3 text-[var(--gold)]"></i>
                    Your Rights and Choices
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>You have the right to:</p>
                    <ul class="list-disc list-inside space-y-2 ml-6">
                        <li><strong class="text-[var(--primary)]">Access:</strong> Review and update your personal information</li>
                        <li><strong class="text-[var(--primary)]">Delete:</strong> Request deletion of your account and data</li>
                        <li><strong class="text-[var(--primary)]">Opt-out:</strong> Choose not to receive marketing communications</li>
                        <li><strong class="text-[var(--primary)]">Portability:</strong> Request a copy of your data in a portable format</li>
                        <li><strong class="text-[var(--primary)]">Complaint:</strong> File complaints with data protection authorities</li>
                    </ul>
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
                        If you have questions about this Privacy Policy or how we handle your data, please contact us:
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
