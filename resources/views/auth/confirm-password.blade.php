<x-layouts::guest :heading="__('Confirm your password')" :description="__('This is a secure area. Please confirm your password to continue.')">
    <form method="POST" action="{{ route('password.confirm.store') }}" class="space-y-5">
        @csrf
        <x-ui.field :label="__('Password')" for="password">
            <x-ui.input name="password" type="password" required autofocus autocomplete="current-password" />
        </x-ui.field>

        <x-ui.button type="submit" class="w-full">{{ __('Confirm') }}</x-ui.button>
    </form>
</x-layouts::guest>
