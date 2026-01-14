@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Profile Settings</h1>
        
        <!-- Profile Information Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <!-- Password Update Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            @include('profile.partials.update-password-form')
        </div>

        <!-- Delete Account Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
