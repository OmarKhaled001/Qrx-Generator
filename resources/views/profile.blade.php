<x-layouts::app :title="__('Profile')">
    <x-ui.page-header :heading="__('Profile')" :description="__('Your account details, password and language.')" />

    <div class="grid max-w-3xl gap-6">
        <x-ui.card :heading="__('Account details')">
            @if (session('status') === 'profile-information-updated')
                <x-ui.alert type="success" class="mb-5">{{ __('Saved.') }}</x-ui.alert>
            @endif
            <form method="POST" action="{{ route('user-profile-information.update') }}" class="grid gap-5 sm:grid-cols-2">
                @csrf
                @method('PUT')
                <x-ui.field :label="__('Full name')" for="name" class="sm:col-span-2">
                    <x-ui.input id="name" name="name" :value="old('name', $user->name)" required autocomplete="name" />
                    @error('name', 'updateProfileInformation')<p class="text-xs font-medium text-danger-600" role="alert">{{ $message }}</p>@enderror
                </x-ui.field>
                <x-ui.field :label="__('Email address')" for="email" class="sm:col-span-2" :hint="__('Changing it means verifying the new address.')">
                    <x-ui.input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="email" dir="ltr" />
                    @error('email', 'updateProfileInformation')<p class="text-xs font-medium text-danger-600" role="alert">{{ $message }}</p>@enderror
                </x-ui.field>
                <x-ui.field :label="__('Phone number')" for="phone" class="sm:col-span-2">
                    <x-ui.input id="phone" name="phone" type="tel" :value="old('phone', $user->phone)" autocomplete="tel" dir="ltr" />
                    @error('phone', 'updateProfileInformation')<p class="text-xs font-medium text-danger-600" role="alert">{{ $message }}</p>@enderror
                </x-ui.field>
                <x-ui.field :label="__('Country')" for="country">
                    <x-ui.input id="country" name="country" :value="old('country', $user->country)" autocomplete="country-name" />
                </x-ui.field>
                <x-ui.field :label="__('City')" for="city">
                    <x-ui.input id="city" name="city" :value="old('city', $user->city)" autocomplete="address-level2" />
                </x-ui.field>
                <x-ui.field :label="__('Area')" for="area">
                    <x-ui.input id="area" name="area" :value="old('area', $user->area)" autocomplete="address-level3" />
                </x-ui.field>
                <div class="sm:col-span-2">
                    <x-ui.button type="submit">{{ __('Save changes') }}</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card :heading="__('Password')">
            @if (session('status') === 'password-updated')
                <x-ui.alert type="success" class="mb-5">{{ __('Password updated.') }}</x-ui.alert>
            @endif
            <form method="POST" action="{{ route('user-password.update') }}" class="grid gap-5">
                @csrf
                @method('PUT')
                <x-ui.field :label="__('Current password')" for="current_password">
                    <x-ui.input id="current_password" name="current_password" type="password" autocomplete="current-password" />
                    @error('current_password', 'updatePassword')<p class="text-xs font-medium text-danger-600" role="alert">{{ $message }}</p>@enderror
                </x-ui.field>
                <x-ui.field :label="__('New password')" for="password">
                    <x-ui.input id="password" name="password" type="password" autocomplete="new-password" />
                    @error('password', 'updatePassword')<p class="text-xs font-medium text-danger-600" role="alert">{{ $message }}</p>@enderror
                </x-ui.field>
                <x-ui.field :label="__('Confirm password')" for="password_confirmation">
                    <x-ui.input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
                </x-ui.field>
                <div>
                    <x-ui.button type="submit">{{ __('Update password') }}</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card :heading="__('Delete account')" :description="__('Permanently delete your account, your QR codes and their scan history. Printed codes stop working.')">
            <x-ui.button variant="danger" x-data x-on:click="$dispatch('open-modal', 'delete-account')">{{ __('Delete account') }}</x-ui.button>
            @error('password', 'userDeletion')<p class="mt-3 text-sm font-medium text-danger-600" role="alert">{{ $message }}</p>@enderror
            @error('confirm_email', 'userDeletion')<p class="mt-3 text-sm font-medium text-danger-600" role="alert">{{ __('That is not your email address.') }}</p>@enderror
        </x-ui.card>
    </div>

    <x-ui.modal name="delete-account" :heading="__('Delete your account?')">
        <form id="delete-account-form" method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('DELETE')
            @if ($user->password === null)
                <p>{{ __('This cannot be undone. Type your email address to confirm.') }}</p>
                <x-ui.field :label="__('Email address')" for="confirm-email">
                    <x-ui.input id="confirm-email" name="confirm_email" type="email" autocomplete="off" dir="ltr" />
                </x-ui.field>
            @else
                <p>{{ __('This cannot be undone. Enter your password to confirm.') }}</p>
                <x-ui.field :label="__('Password')" for="delete-password">
                    <x-ui.input id="delete-password" name="password" type="password" autocomplete="current-password" />
                </x-ui.field>
            @endif
        </form>
        <x-slot:footer>
            <x-ui.button variant="secondary" x-on:click="$dispatch('close-modal')">{{ __('Cancel') }}</x-ui.button>
            <x-ui.button variant="danger" type="submit" form="delete-account-form">{{ __('Delete account') }}</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
</x-layouts::app>
