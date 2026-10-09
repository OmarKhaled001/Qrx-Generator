<x-layouts::guest :heading="__('Check your inbox')" :description="__('We sent a verification link to your email address. Open it to start using your dashboard.')">
    @if (session('status') === 'verification-link-sent')
        <x-ui.alert type="success" class="mb-5">{{ __('A new verification link has been sent.') }}</x-ui.alert>
    @endif

    <div class="space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-ui.button type="submit" class="w-full">{{ __('Resend the email') }}</x-ui.button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-ui.button type="submit" variant="ghost" class="w-full">{{ __('Log out') }}</x-ui.button>
        </form>
    </div>
</x-layouts::guest>
