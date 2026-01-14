@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        @include('customer.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 min-w-0">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Profile Settings</h1>
                <p class="text-gray-500">Manage your account settings and preferences</p>
            </div>

            <!-- Tab Navigation -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="border-b border-gray-100">
                    <nav class="flex" id="profileTabs">
                        <button onclick="showProfileTab('profile')" id="tab-profile"
                            class="profile-tab-btn flex-1 px-6 py-4 text-sm font-medium border-b-2 transition-colors active">
                            <i class="fas fa-user mr-2"></i>
                            Profile Information
                        </button>
                        <button onclick="showProfileTab('security')" id="tab-security"
                            class="profile-tab-btn flex-1 px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                            <i class="fas fa-lock mr-2"></i>
                            Security
                        </button>
                        <button onclick="showProfileTab('account')" id="tab-account"
                            class="profile-tab-btn flex-1 px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                            <i class="fas fa-cog mr-2"></i>
                            Account
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Profile Tab -->
            <div id="content-profile" class="profile-tab-content">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-pink-50 to-rose-50">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-user text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Profile Information</h2>
                                <p class="text-sm text-gray-500">Update your personal details and contact information</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-8">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <!-- Security Tab -->
            <div id="content-security" class="profile-tab-content hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-lock text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Change Password</h2>
                                <p class="text-sm text-gray-500">Ensure your account stays secure by using a strong password</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-8">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <!-- Account Tab -->
            <div id="content-account" class="profile-tab-content hidden">
                <div class="bg-white rounded-2xl shadow-sm border-2 border-red-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-red-100 bg-gradient-to-r from-red-50 to-rose-50">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-red-800">Danger Zone</h2>
                                <p class="text-sm text-red-600">Permanently delete your account and all associated data</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-8">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .profile-tab-btn {
        border-color: transparent;
        color: #6B7280;
    }
    
    .profile-tab-btn:hover {
        color: #374151;
        background-color: #F9FAFB;
    }
    
    .profile-tab-btn.active {
        border-color: #EC4899;
        color: #EC4899;
    }
</style>
@endpush

@push('scripts')
<script>
    function showProfileTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.profile-tab-content').forEach(el => el.classList.add('hidden'));
        
        // Remove active state from all buttons
        document.querySelectorAll('.profile-tab-btn').forEach(el => {
            el.classList.remove('active');
        });

        // Show selected tab content
        document.getElementById('content-' + tabName).classList.remove('hidden');
        
        // Activate selected tab button
        document.getElementById('tab-' + tabName).classList.add('active');
    }
</script>
@endpush
@endsection
