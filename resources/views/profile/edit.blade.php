@extends('layouts.dashboard')

@section('title', 'Profile - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
        <p class="text-gray-600">Manage your account information and preferences</p>
    </div>

    <!-- Profile Information Form -->
    <div class="card">
        @include('profile.partials.update-profile-information-form')
    </div>

    <!-- Password Update Form -->
    <div class="card">
        @include('profile.partials.update-password-form')
    </div>

    <!-- Delete Account Form -->
    <div class="card">
        @include('profile.partials.delete-user-form')
    </div>
</div>
@endsection

@push('scripts')
<script>
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

    // Initialize avatar preview on page load
    document.addEventListener('DOMContentLoaded', function() {
        const avatarInput = document.getElementById('avatar');
        if (avatarInput && !document.getElementById('avatar-preview')) {
            const previewDiv = document.createElement('img');
            previewDiv.id = 'avatar-preview';
            previewDiv.className = 'h-20 w-20 rounded-full object-cover';
            
            if ('{{ $user->avatar }}') {
                previewDiv.src = '{{ asset('storage/' . $user->avatar) }}';
            }
            
            avatarInput.parentNode.insertBefore(previewDiv, avatarInput);
        }
    });
</script>
@endpush
