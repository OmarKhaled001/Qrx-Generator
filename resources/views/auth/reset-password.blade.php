<x-layouts::guest :heading="__('Choose a new password')">
    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-ui.field :label="__('Email address')" for="email">
            <x-ui.input name="email" type="email" :value="old('email', $request->email)" required autocomplete="username" dir="ltr" />
        </x-ui.field>

        <x-ui.field :label="__('New password')" for="password" :hint="__('At least 8 characters.')">
            <x-ui.input name="password" type="password" required autofocus autocomplete="new-password" />
        </x-ui.field>

        <x-ui.field :label="__('Confirm password')" for="password_confirmation">
            <x-ui.input name="password_confirmation" type="password" required autocomplete="new-password" />
        </x-ui.field>

        <x-ui.button type="submit" class="w-full">{{ __('Reset password') }}</x-ui.button>
    </form>
</x-layouts::guest>
