@extends('layouts.dashboard')

@section('title', 'Manage Staff - Belleza Rosa')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Manage Staff</h1>
            <button onclick="openAddUserModal()"
                class="bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold py-3 px-6 rounded-xl shadow-lg transform hover:-translate-y-1 transition">
                <i class="fas fa-user-plus mr-2"></i> Add Staff
            </button>
        </div>
        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Admins -->
            <div class="card border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-xl">
                            <i class="fas fa-user-shield text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Admins</h3>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $users->where('role', 'admin')->count() }}
                    </p>
                </div>
            </div>

            <!-- Staff -->
            <div class="card border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-xl">
                            <i class="fas fa-users-cog text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Staff</h3>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $users->where('role', 'staff')->count() }}
                    </p>
                </div>
            </div>

            <!-- Active Users -->
            <div class="card border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-xl">
                            <i class="fas fa-user-check text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Active Users</h3>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $users->where('is_active', true)->count() }}
                    </p>
                </div>
            </div>
        </div>
        <!-- Users Table -->
        <div class="card">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-blue-600 text-white">
                            <th class="px-4 py-3 text-left text-sm font-semibold">User</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Contact</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Role</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Last Login</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $user->full_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->username }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="text-gray-900">{{ $user->phone }}</div>
                                    <div class="text-gray-500">{{ $user->email ?? 'No email' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="px-3 py-1 text-xs rounded-full font-semibold
                                {{ $user->role == 'admin' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $user->role == 'staff' ? 'bg-blue-100 text-blue-800' : '' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="px-3 py-1 text-xs rounded-full font-semibold {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <!-- Show Button -->
                                        <a href="{{ route('dashboard.users.show', $user) }}"
                                            class="text-blue-600 hover:text-blue-800 transition" title="View User">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Toggle Active Status -->
                                        @php
                                            $isLastAdmin = $user->isAdmin() && $users->where('role', 'admin')->where('is_active', true)->count() <= 1;
                                            $hasAppointments = $user->staff && $user->staff->appointments()->count() > 0;
                                        @endphp
                                        <form action="{{ route('dashboard.users.toggle', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded-lg transition {{ $isLastAdmin && $user->is_active ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                {{ $isLastAdmin && $user->is_active ? 'disabled title="Cannot deactivate the last admin"' : 'title="' . ($user->is_active ? 'Deactivate User' : 'Activate User') . '"' }}>
                                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                        <!-- Delete Button (only for staff, not admin) -->
                                        @if($user->isStaff())
                                            <form action="{{ route('dashboard.users.destroy', $user) }}" method="POST" class="inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete {{ $user->full_name }}? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-xs bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded-lg transition {{ $hasAppointments ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                    {{ $hasAppointments ? 'disabled title="Cannot delete staff with appointments"' : 'title="Delete Staff"' }}>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                                    <p>No users found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>



        <!-- Add User Modal -->
        <div id="addUserModal"
            class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="bg-gradient-to-r from-blue-900 to-blue-700 text-white p-6 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold">Add New Staff</h2>
                        <button onclick="closeModal('addUserModal')"
                            class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('dashboard.users.store') }}" method="POST" id="addUserForm">
                        @csrf
                        <div class="space-y-4">
                            @if ($errors->any())
                                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                                    <p class="font-bold">Error</p>
                                    <ul class="list-disc pl-5 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="block text-gray-700 font-semibold mb-2">Full Name *</label>
                                    <input type="text" name="full_name" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                                        placeholder="Enter full name" value="{{ old('full_name') }}">
                                </div>
                                <div class="form-group">
                                    <label class="block text-gray-700 font-semibold mb-2">Username *</label>
                                    <input type="text" name="username" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                                        placeholder="Choose username" value="{{ old('username') }}">
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="block text-gray-700 font-semibold mb-2">Email *</label>
                                    <input type="email" name="email" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                                        placeholder="Email address" value="{{ old('email') }}">
                                </div>
                                <div class="form-group">
                                    <label class="block text-gray-700 font-semibold mb-2">Phone</label>
                                    <input type="tel" name="phone"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                                        placeholder="Phone number" value="{{ old('phone') }}">
                                </div>
                                <div class="form-group">
                                    <label class="block text-gray-700 font-semibold mb-2">Gender *</label>
                                    <select name="gender" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Hidden role field (always staff) -->
                            <input type="hidden" name="role" value="staff">

                            <div class="form-group">
                                <label class="block text-gray-700 font-semibold mb-2">Password *</label>
                                <input type="password" name="password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                                    placeholder="Enter password">
                            </div>

                            <div class="form-group">
                                <label class="block text-gray-700 font-semibold mb-2">Confirm Password *</label>
                                <input type="password" name="password_confirmation" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                                    placeholder="Confirm password">
                            </div>

                            <!-- Staff Specific Fields -->
                            <div id="staffFields" class="bg-blue-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold text-blue-800 mb-3">Staff Information</h3>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="block text-gray-700 font-semibold mb-2">Specialty</label>
                                        <select name="specialty" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                                            <option value="all" {{ old('specialty', 'all') == 'all' ? 'selected' : '' }}>All Services (Hair, Nail & Spa)</option>
                                            <option value="hair" {{ old('specialty') == 'hair' ? 'selected' : '' }}>Hair Only</option>
                                            <option value="nail" {{ old('specialty') == 'nail' ? 'selected' : '' }}>Nail Only</option>
                                            <option value="spa" {{ old('specialty') == 'spa' ? 'selected' : '' }}>Spa Only</option>
                                            <option value="hair_nail" {{ old('specialty') == 'hair_nail' ? 'selected' : '' }}>Hair & Nail</option>
                                            <option value="hair_spa" {{ old('specialty') == 'hair_spa' ? 'selected' : '' }}>Hair & Spa</option>
                                            <option value="nail_spa" {{ old('specialty') == 'nail_spa' ? 'selected' : '' }}>Nail & Spa</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="block text-gray-700 font-semibold mb-2">Color Code</label>
                                        <div class="flex items-center">
                                            <input type="color" name="color_code" value="#3b82f6"
                                                class="w-12 h-12 rounded-lg border border-gray-300 mr-3">
                                            <input type="text" name="color_code_display"
                                                class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                                                placeholder="#3b82f6" value="{{ old('color_code', '#3b82f6') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-4">
                            <button type="button" onclick="closeModal('addUserModal')"
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-6 py-3 bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold rounded-xl shadow-lg transform hover:scale-105 transition">
                                <i class="fas fa-user-plus mr-2"></i> Create Staff
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function openAddUserModal() {
                // Reset form and clear any previous errors
                const form = document.getElementById('addUserForm');
                if (form) {
                    form.reset();
                    // Clear any error messages
                    const errorMessages = document.querySelectorAll('.error-message');
                    errorMessages.forEach(el => el.remove());
                    // Reset staff fields visibility
                    toggleStaffFields();
                }
                document.getElementById('addUserModal').classList.remove('hidden');
            }

            function closeModal(modalId) {
                document.getElementById(modalId).classList.add('hidden');
            }

            // Staff fields are always visible since we're always adding staff
            function toggleStaffFields() {
                const staffFields = document.getElementById('staffFields');
                if (staffFields) {
                    staffFields.classList.remove('hidden');
                }
            }

            // Sync color picker and text input
            function syncColorValue(input) {
                const colorInput = document.querySelector('input[name="color_code"]');
                const displayInput = document.querySelector('input[name="color_code_display"]');

                if (input.name === 'color_code') {
                    displayInput.value = input.value;
                } else if (input.name === 'color_code_display') {
                    // Validate hex color
                    if (/^#[0-9A-F]{6}$/i.test(input.value)) {
                        colorInput.value = input.value;
                    }
                }
            }

            // Initialize event listeners when the document is ready
            document.addEventListener('DOMContentLoaded', function() {
                // Always show staff fields since we're always adding staff
                toggleStaffFields();

                // Initialize color picker sync
                const colorInputs = document.querySelectorAll('input[name^="color_code"]');
                colorInputs.forEach(input => {
                    input.addEventListener('input', (e) => syncColorValue(e.target));
                });

                // Handle form submission
                const form = document.getElementById('addUserForm');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        const submitButton = form.querySelector('button[type="submit"]');
                        if (submitButton) {
                            submitButton.disabled = true;
                            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating...';
                        }
                    });
                }
            });

            // Close modal when clicking outside
            window.onclick = function(event) {
                if (event.target.classList.contains('fixed')) {
                    event.target.classList.add('hidden');
                }
            }
        </script>
    @endsection
