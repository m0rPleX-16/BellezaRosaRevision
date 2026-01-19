@php
/** @var \App\Models\User $user */
@endphp
<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ request()->routeIs('customer.*') ? route('customer.profile.update') : (request()->routeIs('staff.*') ? route('staff.profile.update') : route('profile.update')) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('patch')

        <!-- Avatar Upload -->
        <div class="flex items-center gap-5">
            <div class="shrink-0">
                @if($user->avatar)
                    <img id="avatar-preview" class="h-20 w-20 rounded-full object-cover border-2 border-gray-200" 
                         src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->full_name }}">
                @else
                    <div class="h-20 w-20 rounded-full bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center border-2 border-pink-200">
                        <span class="text-2xl font-bold text-white">{{ strtoupper(substr($user->full_name, 0, 1)) }}</span>
                    </div>
                @endif
            </div>
            <div>
                <label class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer transition">
                    <i class="fas fa-camera mr-2 text-gray-400"></i>
                    Change Photo
                    <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*" onchange="previewAvatar(this)">
                </label>
                <p class="mt-1 text-xs text-gray-500">JPG, PNG up to 2MB</p>
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            </div>
        </div>

        <!-- Two Column Grid for Name/Username -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Full Name -->
            <div>
                <x-input-label for="full_name" :value="__('Full Name')" />
                <x-text-input id="full_name" name="full_name" type="text" 
                    :value="old('full_name', $user->full_name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-1" :messages="$errors->get('full_name')" />
            </div>

            <!-- Username -->
            <div>
                <x-input-label for="username" :value="__('Username')" />
                <x-text-input id="username" name="username" type="text" 
                    :value="old('username', $user->username)" required autocomplete="username" />
                <x-input-error class="mt-1" :messages="$errors->get('username')" />
            </div>
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" name="email" type="email" 
                :value="old('email', $user->email)" required autocomplete="email" />
            <x-input-error class="mt-1" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 flex items-center gap-2 text-sm">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                        <i class="fas fa-exclamation-circle mr-1"></i> Unverified
                    </span>
                    <button form="send-verification" class="text-blue-600 hover:text-blue-800 underline text-sm">
                        Resend verification email
                    </button>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-sm text-green-600">
                        <i class="fas fa-check-circle mr-1"></i> Verification link sent!
                    </p>
                @endif
            @endif
        </div>

        <!-- Phone & Gender Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Phone -->
            <div>
                <x-input-label for="phone" :value="__('Phone Number')" />
                <x-text-input id="phone" name="phone" type="tel" 
                    :value="old('phone', $user->phone)" autocomplete="tel" placeholder="09xx xxx xxxx" />
                <x-input-error class="mt-1" :messages="$errors->get('phone')" />
            </div>

            <!-- Gender -->
            <div>
                <x-input-label for="gender" :value="__('Gender')" />
                <select id="gender" name="gender" class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                <x-input-error class="mt-1" :messages="$errors->get('gender')" />
            </div>
        </div>

        <!-- Customer Specific Fields -->
        @if(method_exists($user, 'isCustomer') && $user->isCustomer() && $user->customer)
            <div class="pt-5 mt-5 border-t border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Additional Information</h3>
                
                <div class="space-y-4">
                    <!-- Birth Date -->
                    <div>
                        <x-input-label for="birth_date" :value="__('Birth Date')" />
                        @php
                            $birthDate = $user->customer->birth_date;
                            $birthDateValue = $birthDate instanceof \Carbon\Carbon ? $birthDate->format('Y-m-d') : '';
                        @endphp
                        <x-text-input id="birth_date" name="birth_date" type="date" 
                            :value="old('birth_date', $birthDateValue)" 
                            max="{{ now()->format('Y-m-d') }}" />
                        <x-input-error class="mt-1" :messages="$errors->get('birth_date')" />
                    </div>

                    <!-- Notes -->
                    <div>
                        <x-input-label for="notes" :value="__('Notes / Preferences')" />
                        <textarea id="notes" name="notes" rows="3" 
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Any preferences or notes for your visits...">{{ old('notes', $user->customer->notes) }}</textarea>
                        <x-input-error class="mt-1" :messages="$errors->get('notes')" />
                    </div>
                </div>
            </div>
        @endif

        <!-- Staff Specific Fields -->
        @if(method_exists($user, 'isStaff') && $user->isStaff() && $user->staff)
            <div class="pt-5 mt-5 border-t border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Staff Information</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Specialty (Read-only) -->
                    <div>
                        <x-input-label for="specialty" :value="__('Specialty')" />
                        <x-text-input id="specialty" name="specialty" type="text" 
                            :value="old('specialty', $user->staff->formatted_specialty)" disabled />
                        <p class="mt-1 text-xs text-gray-500">Contact admin to change specialty</p>
                    </div>

                    <!-- Color Code -->
                    <div>
                        <x-input-label for="color_code" :value="__('Calendar Color')" />
                        <div class="flex items-center gap-3">
                            <input type="color" id="color_code_picker"
                                value="{{ old('color_code', $user->staff->color_code ?? '#3B82F6') }}"
                                onchange="document.getElementById('color_code').value = this.value"
                                class="h-12 w-16 border border-gray-300 rounded-lg cursor-pointer">
                            <x-text-input type="text" id="color_code" name="color_code"
                                value="{{ old('color_code', $user->staff->color_code ?? '#3B82F6') }}"
                                oninput="document.getElementById('color_code_picker').value = this.value"
                                pattern="^#[0-9A-Fa-f]{6}$"
                                class="flex-1" placeholder="#3B82F6" />
                        </div>
                        <x-input-error class="mt-1" :messages="$errors->get('color_code')" />
                    </div>
                </div>
            </div>
        @endif

        <!-- Save Button -->
        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-rose-600 hover:from-pink-600 hover:to-rose-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition">
                <i class="fas fa-save mr-2"></i> Save Changes
            </button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm text-green-600 flex items-center animate-pulse">
                    <i class="fas fa-check-circle mr-1"></i> Saved successfully!
                </p>
            @endif
        </div>
    </form>
</section>
