<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Avatar Upload -->
        <div class="flex items-center space-x-6">
            <div class="shrink-0">
                @if($user->avatar)
                    <img id="avatar-preview" class="h-20 w-20 rounded-full object-cover" 
                         src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->full_name }}">
                @else
                    <div class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center">
                        <span class="text-2xl text-gray-500">{{ substr($user->full_name, 0, 1) }}</span>
                    </div>
                @endif
            </div>
            <label class="block">
                <span class="sr-only">{{ __('Choose profile photo') }}</span>
                <input type="file" name="avatar" id="avatar" class="block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-blue-50 file:text-blue-700
                    hover:file:bg-blue-100"
                    onchange="previewAvatar(this)">
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            </label>
        </div>

        <!-- Full Name -->
        <div>
            <x-input-label for="full_name" :value="__('Full Name')" />
            <x-text-input id="full_name" name="full_name" type="text" class="mt-1 block w-full" 
                :value="old('full_name', $user->full_name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('full_name')" />
        </div>

        <!-- Username -->
        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" 
                :value="old('username', $user->username)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" 
                :value="old('email', $user->email)" required autocomplete="email" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Phone -->
        <div>
            <x-input-label for="phone" :value="__('Phone Number')" />
            <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" 
                :value="old('phone', $user->phone)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <!-- Staff Specific Fields -->
        @if($user->isStaff() && $user->staff)
            <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Staff Information') }}
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <x-input-label for="specialty" :value="__('Specialty')" />
                        <x-text-input id="specialty" name="specialty" type="text" class="mt-1 block w-full" 
                            :value="old('specialty', $user->staff->specialty)" disabled />
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('Contact an administrator to change your specialty.') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
