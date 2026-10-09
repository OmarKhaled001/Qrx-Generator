<div class="rounded-xl border border-line bg-surface p-6 shadow-card sm:p-8">
    @if ($sent)
        <x-ui.alert type="success" :heading="__('Message sent')">{{ __('Thanks for reaching out. We usually reply within one working day.') }}</x-ui.alert>
    @endif
    <form wire:submit="send" class="mt-2 grid gap-5 sm:grid-cols-2">
        <x-ui.field :label="__('Name')" for="name" required>
            <x-ui.input name="name" wire:model="name" autocomplete="name" />
        </x-ui.field>
        <x-ui.field :label="__('Email address')" for="email" required>
            <x-ui.input name="email" type="email" wire:model="email" autocomplete="email" dir="ltr" />
        </x-ui.field>
        <x-ui.field :label="__('Subject')" for="subject" class="sm:col-span-2" required>
            <x-ui.input name="subject" wire:model="subject" />
        </x-ui.field>
        <x-ui.field :label="__('Message')" for="message" class="sm:col-span-2" required>
            <x-ui.textarea name="message" wire:model="message" rows="5" />
        </x-ui.field>
        <div class="hidden" aria-hidden="true">
            <label for="website">{{ __('Website') }}</label>
            <input id="website" type="text" wire:model="website" tabindex="-1" autocomplete="off">
        </div>
        <div class="sm:col-span-2">
            <x-ui.button type="submit" icon="heroicon-o-paper-airplane" wire:loading.attr="disabled">{{ __('Send message') }}</x-ui.button>
        </div>
    </form>
</div>
