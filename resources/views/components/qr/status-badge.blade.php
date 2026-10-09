@props(['qrCode', 'plan'])
@if ($qrCode->isPaused())
    <x-ui.badge color="warning" dot {{ $attributes }}>{{ __('Paused') }}</x-ui.badge>
@elseif ($qrCode->hasReachedScanLimit($plan))
    <x-ui.badge color="danger" dot {{ $attributes }}>{{ __('Limit reached') }}</x-ui.badge>
@else
    <x-ui.badge color="success" dot {{ $attributes }}>{{ __('Active') }}</x-ui.badge>
@endif
