<?php

namespace App\Livewire\Dashboard;

use App\Enums\QrStatus;
use App\Models\QrCode;
use App\Services\QrRenderer;
use App\Services\Subscriptions;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class QrShow extends Component
{
    public QrCode $qrCode;

    public function mount(QrCode $qrCode): void
    {
        $this->authorize('view', $qrCode);
        $this->qrCode = $qrCode;
    }

    public function toggleStatus(): void
    {
        $this->authorize('update', $this->qrCode);

        $this->qrCode->update(['status' => $this->qrCode->isPaused() ? QrStatus::Active : QrStatus::Paused]);

        $this->dispatch('toast', message: $this->qrCode->isPaused() ? __('QR code paused.') : __('QR code is live again.'));
    }

    public function delete(): void
    {
        $this->authorize('delete', $this->qrCode);

        $this->qrCode->delete();

        session()->flash('toast', ['message' => __('QR code deleted.'), 'type' => 'success']);
        $this->redirectRoute('qr.index', navigate: true);
    }

    public function render(QrRenderer $renderer, Subscriptions $subscriptions): View
    {
        $plan = $subscriptions->currentPlan($this->qrCode->user);

        return view('livewire.dashboard.qr-show', [
            'preview' => $renderer->svgDataUri($this->qrCode->shortUrl(), $this->qrCode->design, 320),
            'plan' => $plan,
            'limitReached' => $this->qrCode->hasReachedScanLimit($plan),
            'canDownloadEps' => ! $plan->is_free,
        ])->title($this->qrCode->name);
    }
}
