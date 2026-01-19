@extends(request()->routeIs('staff.*') ? 'layouts.staff' : 'layouts.dashboard')

@section('title', 'Profile - Belleza Rosa')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
        <p class="mt-1 text-sm text-gray-500">Manage your personal information and account settings.</p>
    </div>

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex gap-6" aria-label="Profile tabs">
            <button type="button" onclick="showTab('profile')" id="tab-profile"
                class="tab-btn active pb-3 text-sm font-medium border-b-2 border-blue-600 text-blue-600">
                <i class="fas fa-user mr-2"></i>Profile
            </button>
            <button type="button" onclick="showTab('security')" id="tab-security"
                class="tab-btn pb-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                <i class="fas fa-lock mr-2"></i>Security
            </button>
            <button type="button" onclick="showTab('account')" id="tab-account"
                class="tab-btn pb-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                <i class="fas fa-cog mr-2"></i>Account
            </button>
        </nav>
    </div>

    <!-- Profile Tab -->
    <div id="content-profile" class="tab-content">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                <h2 class="text-base font-semibold text-gray-900">Profile Information</h2>
                <p class="mt-1 text-sm text-gray-500">Update your personal details and contact information.</p>
            </div>
            <div class="p-6">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
    </div>

    <!-- Security Tab -->
    <div id="content-security" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                <h2 class="text-base font-semibold text-gray-900">Change Password</h2>
                <p class="mt-1 text-sm text-gray-500">Ensure your account stays secure by using a strong password.</p>
            </div>
            <div class="p-6">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>

    <!-- Account Tab -->
    <div id="content-account" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-red-100 bg-red-50">
                <h2 class="text-base font-semibold text-red-800">Danger Zone</h2>
                <p class="mt-1 text-sm text-red-600">Permanently delete your account and all associated data.</p>
            </div>
            <div class="p-6">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        // Remove active state from all buttons
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.classList.remove('active', 'border-blue-600', 'text-blue-600');
            el.classList.add('border-transparent', 'text-gray-500');
        });

        // Show selected tab content
        document.getElementById('content-' + tabName).classList.remove('hidden');
        // Activate selected tab button
        const activeBtn = document.getElementById('tab-' + tabName);
        activeBtn.classList.add('active', 'border-blue-600', 'text-blue-600');
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
    }

    function previewAvatar(input) {
        const preview = document.getElementById('avatar-preview');
        const file = input.files[0];
        const reader = new FileReader();

        reader.onloadend = function () {
            if (preview) {
                preview.src = reader.result;
            }
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            if (preview) {
                preview.src = "{{ $user->avatar ? asset('storage/' . $user->avatar) : '#' }}";
            }
        }
    }
</script>
@endpush
