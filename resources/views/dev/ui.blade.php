{{-- Component gallery (local/testing only). Switch language and theme in the top bar to check RTL and dark mode. --}}
<x-layouts::public :title="__('Component gallery')">
    <div class="mx-auto max-w-5xl space-y-10 px-4 py-12 sm:px-6">
        <x-ui.page-header :heading="__('Component gallery')" :description="__('Every UI component in light, dark, LTR and RTL.')" />

        <x-ui.card :heading="__('Buttons')">
            <div class="flex flex-wrap gap-3">
                <x-ui.button icon="heroicon-o-plus">{{ __('Create QR code') }}</x-ui.button>
                <x-ui.button variant="secondary">{{ __('Cancel') }}</x-ui.button>
                <x-ui.button variant="ghost">{{ __('Edit') }}</x-ui.button>
                <x-ui.button variant="danger">{{ __('Delete') }}</x-ui.button>
                <x-ui.button size="sm">{{ __('Save') }}</x-ui.button>
                <x-ui.button size="lg">{{ __('Get started') }}</x-ui.button>
            </div>
        </x-ui.card>

        <x-ui.card :heading="__('Form fields')">
            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.field :label="__('Name')" for="gallery-name" :hint="__('Shown only to you.')">
                    <x-ui.input id="gallery-name" :placeholder="__('Restaurant menu')" />
                </x-ui.field>
                <x-ui.field :label="__('Security')" for="gallery-security">
                    <x-ui.select id="gallery-security">
                        <option>WPA</option>
                        <option>WEP</option>
                    </x-ui.select>
                </x-ui.field>
                <x-ui.field :label="__('Message')" for="gallery-message" class="sm:col-span-2">
                    <x-ui.textarea id="gallery-message" />
                </x-ui.field>
                <x-ui.checkbox id="gallery-hidden" :label="__('Hidden network')" />
            </div>
        </x-ui.card>

        <x-ui.card :heading="__('Status')">
            <div class="flex flex-wrap gap-2">
                <x-ui.badge color="success" dot>{{ __('Active') }}</x-ui.badge>
                <x-ui.badge color="warning" dot>{{ __('Paused') }}</x-ui.badge>
                <x-ui.badge color="danger" dot>{{ __('Limit reached') }}</x-ui.badge>
                <x-ui.badge color="primary">{{ __('Website') }}</x-ui.badge>
                <x-ui.badge>{{ __('Free') }}</x-ui.badge>
            </div>
            <div class="mt-5 space-y-3">
                <x-ui.alert type="info">{{ __('Dynamic QR codes you can edit, track and brand.') }}</x-ui.alert>
                <x-ui.alert type="success">{{ __('Saved.') }}</x-ui.alert>
                <x-ui.alert type="warning">{{ __('You have reached the limit of your plan.') }}</x-ui.alert>
                <x-ui.alert type="danger">{{ __('Something went wrong.') }}</x-ui.alert>
            </div>
        </x-ui.card>

        <div class="grid gap-4 sm:grid-cols-3">
            <x-ui.stat :label="__('QR codes')" value="12" :hint="__(':used of :limit used', ['used' => 12, 'limit' => 25])" icon="heroicon-o-qr-code" />
            <x-ui.stat :label="__('Total scans')" value="4,821" icon="heroicon-o-cursor-arrow-rays" />
            <x-ui.stat :label="__('Current plan')" :value="__('Free')" icon="heroicon-o-credit-card" />
        </div>

        <x-ui.card :heading="__('Scans')" :description="__('Last 30 days')">
            <x-ui.bar-chart :series="collect(range(29, 0))->mapWithKeys(fn ($d) => [now()->subDays($d)->toDateString() => (int) round(20 + 15 * sin($d / 3) + $d % 7)])->all()" :label="__('Scans per day, last 30 days')" />
        </x-ui.card>

        <x-ui.empty-state :heading="__('No QR codes yet')" :description="__('Create your first dynamic QR code. You can change where it points at any time.')">
            <x-ui.button icon="heroicon-o-plus">{{ __('Create QR code') }}</x-ui.button>
        </x-ui.empty-state>

        <div class="flex gap-3">
            <x-ui.copy-button text="https://qrx.test/x/aBcD2345" />
            <x-ui.button variant="secondary" x-data x-on:click="$dispatch('open-modal', 'gallery')">{{ __('Delete') }}</x-ui.button>
        </div>

        <x-ui.modal name="gallery" :heading="__('Delete this QR code?')">
            {{ __('Printed copies will stop working. This cannot be undone.') }}
            <x-slot:footer>
                <x-ui.button variant="secondary" x-on:click="$dispatch('close-modal')">{{ __('Cancel') }}</x-ui.button>
                <x-ui.button variant="danger">{{ __('Delete') }}</x-ui.button>
            </x-slot:footer>
        </x-ui.modal>
    </div>
</x-layouts::public>
