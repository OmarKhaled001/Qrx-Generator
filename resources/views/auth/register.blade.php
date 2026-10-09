<x-layouts::guest :heading="__('Create your account')" :description="__('Start free with 3 dynamic QR codes. No card needed.')">
    <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
        @csrf
        <x-ui.field :label="__('Full name')" for="name">
            <x-ui.input name="name" :value="old('name')" required autofocus autocomplete="name" />
        </x-ui.field>

        <x-ui.field :label="__('Email address')" for="email">
            <x-ui.input name="email" type="email" :value="old('email')" required autocomplete="email" dir="ltr" />
        </x-ui.field>

        <x-ui.field :label="__('Phone number')" for="phone">
            <x-ui.input name="phone" type="tel" :value="old('phone')" required autocomplete="tel" dir="ltr" :placeholder="__('+20 100 000 0000')" />
        </x-ui.field>

        <div class="grid gap-5 sm:grid-cols-3">
            <x-ui.field :label="__('Country')" for="country">
                <x-ui.input name="country" :value="old('country')" required autocomplete="country-name" />
            </x-ui.field>
            <x-ui.field :label="__('City')" for="city">
                <x-ui.input name="city" :value="old('city')" required autocomplete="address-level2" />
            </x-ui.field>
            <x-ui.field :label="__('Area')" for="area">
                <x-ui.input name="area" :value="old('area')" required autocomplete="address-level3" />
            </x-ui.field>
        </div>

        <x-ui.field :label="__('Password')" for="password" :hint="__('At least 8 characters.')">
            <x-ui.input name="password" type="password" required autocomplete="new-password" />
        </x-ui.field>

        <x-ui.field :label="__('Confirm password')" for="password_confirmation">
            <x-ui.input name="password_confirmation" type="password" required autocomplete="new-password" />
        </x-ui.field>

        <x-ui.button type="submit" class="w-full">{{ __('Create account') }}</x-ui.button>
    </form>

    @includeWhen(View::exists('auth.partials.social'), 'auth.partials.social')

    <x-slot:footer>
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="font-medium text-primary-ink hover:underline">{{ __('Log in') }}</a>
    </x-slot:footer>
</x-layouts::guest>
