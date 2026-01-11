<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewAvatar(input) {
            const preview = document.getElementById('avatar-preview');
            const file = input.files[0];
            const reader = new FileReader();

            reader.onloadend = function () {
                // If there's no preview element, create one
                if (!preview) {
                    const previewDiv = document.createElement('img');
                    previewDiv.id = 'avatar-preview';
                    previewDiv.className = 'h-20 w-20 rounded-full object-cover';
                    input.parentNode.insertBefore(previewDiv, input);
                } else {
                    // Update existing preview
                    preview.src = reader.result;
                }
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                // If no file is selected, show the default avatar
                if (preview) {
                    preview.src = "{{ $user->avatar ? asset('storage/' . $user->avatar) : '#' }}";
                }
            }
        }

        // Initialize avatar preview on page load
        document.addEventListener('DOMContentLoaded', function() {
            const avatarInput = document.getElementById('avatar');
            if (avatarInput) {
                // Create a preview element if it doesn't exist
                if (!document.getElementById('avatar-preview')) {
                    const previewDiv = document.createElement('img');
                    previewDiv.id = 'avatar-preview';
                    previewDiv.className = 'h-20 w-20 rounded-full object-cover';
                    
                    if ('{{ $user->avatar }}') {
                        previewDiv.src = '{{ asset('storage/' . $user->avatar) }}';
                    } else {
                        // Show initial with user's initial
                        const name = '{{ $user->full_name }}';
                        const initial = name ? name.charAt(0).toUpperCase() : 'U';
                        previewDiv.alt = '{{ $user->full_name }}';
                        previewDiv.className = 'h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center text-2xl text-gray-500';
                        previewDiv.textContent = initial;
                    }
                    
                    avatarInput.parentNode.insertBefore(previewDiv, avatarInput);
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
