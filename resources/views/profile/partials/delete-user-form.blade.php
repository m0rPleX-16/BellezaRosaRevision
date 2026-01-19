<section>
    <p class="text-sm text-gray-600 mb-4">
        Once your account is deleted, all of its resources and data will be permanently deleted. This action cannot be undone.
    </p>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        <i class="fas fa-trash-alt mr-2"></i> Delete My Account
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ request()->routeIs('staff.*') ? route('staff.profile.destroy') : route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="text-center mb-6">
                <div class="mx-auto w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Delete Account?</h2>
                <p class="mt-2 text-sm text-gray-600">
                    This will permanently delete your account and all associated data. Please enter your password to confirm.
                </p>
            </div>

            <div class="mb-6">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Enter your password to confirm"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1" />
            </div>

            <div class="flex gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition">
                    <i class="fas fa-trash-alt mr-2"></i> Delete Account
                </button>
            </div>
        </form>
    </x-modal>
</section>
