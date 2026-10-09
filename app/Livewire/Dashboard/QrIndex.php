<?php

namespace App\Livewire\Dashboard;

use App\Enums\QrStatus;
use App\Models\QrCode;
use App\Services\QrRenderer;
use App\Services\Subscriptions;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class QrIndex extends Component
{
    use WithPagination;

    public const FILTERS = ['all', 'active', 'paused', 'limit'];

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: 'all')]
    public string $status = 'all';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->status = in_array($this->status, self::FILTERS, true) ? $this->status : 'all';
        $this->resetPage();
    }

    public function toggleStatus(int $id): void
    {
        $qrCode = QrCode::query()->findOrFail($id);
        $this->authorize('update', $qrCode);

        $qrCode->update(['status' => $qrCode->isPaused() ? QrStatus::Active : QrStatus::Paused]);

        $this->dispatch('toast', message: $qrCode->isPaused() ? __('QR code paused.') : __('QR code is live again.'));
    }

    public function delete(int $id): void
    {
        $qrCode = QrCode::query()->findOrFail($id);
        $this->authorize('delete', $qrCode);

        $qrCode->delete();

        $this->dispatch('toast', message: __('QR code deleted.'));
    }

    public function render(Subscriptions $subscriptions, QrRenderer $renderer): View
    {
        $user = auth()->user();
        $plan = $subscriptions->currentPlan($user);
        $limit = $plan->scan_limit;

        $filtered = fn (string $filter): Builder => $user->qrCodes()->getQuery()
            ->when($filter === 'active', fn (Builder $q) => $q->where('status', QrStatus::Active)->where('scan_count', '<', $limit))
            ->when($filter === 'paused', fn (Builder $q) => $q->where('status', QrStatus::Paused))
            ->when($filter === 'limit', fn (Builder $q) => $q->where('scan_count', '>=', $limit));

        $qrCodes = $filtered($this->status)
            ->when(trim($this->search) !== '', function (Builder $q) {
                $term = '%'.str_replace(['%', '_'], ['\%', '\_'], trim($this->search)).'%';
                $q->where(fn (Builder $q) => $q->where('name', 'like', $term)->orWhere('code', 'like', $term));
            })
            ->latest()
            ->paginate(12);

        return view('livewire.dashboard.qr-index', [
            'qrCodes' => $qrCodes,
            'plan' => $plan,
            'counts' => collect(self::FILTERS)->mapWithKeys(fn (string $filter) => [$filter => $filtered($filter)->count()]),
            'previews' => $qrCodes->getCollection()->mapWithKeys(fn (QrCode $qrCode) => [
                $qrCode->id => $renderer->svgDataUri($qrCode->shortUrl(), $qrCode->design, 160),
            ]),
            'canCreate' => $subscriptions->canCreateQrCode($user),
        ])->title(__('QR codes'));
    }
}
