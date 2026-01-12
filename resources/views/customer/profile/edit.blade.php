<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Profile Photo -->
                    <div class="mb-8">
                        <div class="flex items-center">
                            <div class="mr-4">
                                <img class="w-20 h-20 rounded-full"
                                    src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}"
                                    alt="{{ $user->full_name }}">
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ $user->full_name }}</h3>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Update Profile Information -->
                    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf
                        @method('patch')

                        <!-- Full Name -->
                        <div>
                            <x-label for="full_name" :value="__('Full Name')" />
                            <x-input id="full_name" class="block w-full mt-1" type="text" name="full_name"
                                :value="old('full_name', $user->full_name)" required autofocus />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-label for="email" :value="__('Email')" />
                            <x-input id="email" class="block w-full mt-1" type="email" name="email"
                                :value="old('email', $user->email)" required />
                        </div>

                        <!-- Phone -->
                        <div>
                            <x-label for="phone" :value="__('Phone')" />
                            <x-input id="phone" class="block w-full mt-1" type="text" name="phone"
                                :value="old('phone', $user->customer->phone ?? '')" />
                        </div>

                        <!-- Gender -->
                        <div>
                            <x-label for="gender" :value="__('Gender')" />
                            <select id="gender" name="gender"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Select Gender</option>
                                <option value="male"
                                    {{ old('gender', $user->customer->gender ?? '') == 'male' ? 'selected' : '' }}>Male
                                </option>
                                <option value="female"
                                    {{ old('gender', $user->customer->gender ?? '') == 'female' ? 'selected' : '' }}>
                                    Female</option>
                                <option value="other"
                                    {{ old('gender', $user->customer->gender ?? '') == 'other' ? 'selected' : '' }}>
                                    Other</option>
                            </select>
                        </div>

                        <!-- Birth Date -->
                        <div>
                            <x-label for="birth_date" :value="__('Birth Date')" />
                            <x-input id="birth_date" class="block w-full mt-1" type="date" name="birth_date"
                                :value="old('birth_date', $user->customer->birth_date ?? '')" />
                        </div>

                        <!-- Address -->
                        <div>
                            <x-label for="address" :value="__('Address')" />
                            <textarea id="address" name="address" rows="3"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('address', $user->customer->address ?? '') }}</textarea>
                        </div>

                        <!-- Avatar -->
                        <div>
                            <x-label for="avatar" :value="__('Profile Photo')" />
                            <input id="avatar" name="avatar" type="file" class="block w-full mt-1">
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Save') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
