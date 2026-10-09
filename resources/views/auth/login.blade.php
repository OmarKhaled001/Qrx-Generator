<x-layouts::guest :heading="__('Welcome back')" :description="__('Log in to manage your QR codes.')">
    @if (session('status'))
        <x-ui.alert type="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
        @csrf
        <x-ui.field :label="__('Email address')" for="email">
            <x-ui.input name="email" type="email" :value="old('email')" required autofocus autocomplete="username" dir="ltr" />
        </x-ui.field>

        <x-ui.field :label="__('Password')" for="password">
            <x-ui.input name="password" type="password" required autocomplete="current-password" />
        </x-ui.field>

        <div class="flex items-center justify-between gap-4">
            <x-ui.checkbox name="remember" :label="__('Remember me')" />
            <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary-ink hover:underline">{{ __('Forgot password?') }}</a>
        </div>

        <x-ui.button type="submit" class="w-full">{{ __('Log in') }}</x-ui.button>
    </form>

    @includeWhen(View::exists('auth.partials.social'), 'auth.partials.social')

    <x-slot:footer>
        {{ __('New to :app?', ['app' => config('app.name')]) }}
        <a href="{{ route('register') }}" class="font-medium text-primary-ink hover:underline">{{ __('Create an account') }}</a>
    </x-slot:footer>
</x-layouts::guest>
