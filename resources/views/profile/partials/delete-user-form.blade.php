<section class="space-y-6">
    <header>
        <h2 class="text-lg font-semibold text-espresso dark:text-parchment">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-bean dark:text-parchment/60">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-danger-button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-ui.dialog
        name="confirm-user-deletion"
        :title="__('Are you sure you want to delete your account?')"
        :subtitle="__('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.')"
        maxWidth="md"
        :show="$errors->userDeletion->isNotEmpty()"
    >
        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('delete')

            <div>
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full"
                    placeholder="{{ __('Password') }}"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="dialog-actions">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'confirm-user-deletion')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-danger-button>
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-ui.dialog>
</section>
