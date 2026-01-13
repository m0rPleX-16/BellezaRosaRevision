@extends('layouts.dashboard')

@section('title', 'Staff Details - ' . $user->full_name)

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('dashboard.users.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i> Back to Users
            </a>
        </div>

        <!-- Staff Profile Card -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center space-x-4">
                        <div
                            class="h-24 w-24 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 flex items-center justify-center text-white text-3xl font-bold">
                            {{ strtoupper(substr($user->full_name, 0, 1)) }}
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $user->full_name }}</h1>
                            <div class="flex items-center mt-1">
                                <span
                                    class="px-3 py-1 text-sm font-medium rounded-full 
                                    @if ($user->is_active) bg-green-100 text-green-800 
                                    @else 
                                        bg-red-100 text-red-800 @endif">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <span class="ml-2 px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 md:mt-0">
                        <span
                            class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
                            <i class="fas fa-star text-yellow-500 mr-2"></i>
                            {{ $user->staff ? $user->staff->formatted_specialty . ' Specialist' : 'No Specialty' }}
                        </span>
                    </div>
                </div>

                <!-- Staff Details -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Contact Information</h3>
                            <div class="mt-1">
                                <p class="text-gray-900">{{ $user->email ?? 'No email provided' }}</p>
                                <p class="text-gray-900">{{ $user->phone ?? 'No phone provided' }}</p>
                                @if($user->gender)
                                    <p class="text-gray-900">Gender: <span class="font-medium">{{ $user->formatted_gender }}</span></p>
                                @endif
                            </div>
                        </div>

                        @if ($user->staff && $user->staff->bio)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">About</h3>
                                <p class="mt-1 text-gray-900 whitespace-pre-line">{{ $user->staff->bio }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Account Status</h3>
                            <div class="mt-1">
                                <p>Created: {{ $user->created_at->format('M d, Y') }}</p>
                                <p>Last Updated: {{ $user->updated_at->format('M d, Y') }}</p>
                            </div>
                        </div>

                        @if ($user->staff)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Staff Details</h3>
                                <div class="mt-1">
                                    <p>Specialty: <span class="font-medium">{{ $user->staff->formatted_specialty }}</span>
                                    </p>
                                    <p>Member Since: {{ $user->staff->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-3">
                    <a href="{{ route('dashboard.users.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
