<x-layouts::guest :heading="__('Forgot your password?')" :description="__('Enter your email and we will send you a link to choose a new one.')">
    @if (session('status'))
        <x-ui.alert type="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <x-ui.field :label="__('Email address')" for="email">
            <x-ui.input name="email" type="email" :value="old('email')" required autofocus autocomplete="email" dir="ltr" />
        </x-ui.field>

        <x-ui.button type="submit" class="w-full">{{ __('Email me a reset link') }}</x-ui.button>
    </form>

    <x-slot:footer>
        <a href="{{ route('login') }}" class="font-medium text-primary-ink hover:underline">{{ __('Back to log in') }}</a>
    </x-slot:footer>
</x-layouts::guest>
