<?php

namespace App\Livewire\Dashboard;

use App\Actions\Qr\CreateQrCode;
use App\Actions\Qr\UpdateQrCode;
use App\Enums\QrType;
use App\Exceptions\QrQuotaExceeded;
use App\Models\QrCode;
use App\Services\QrRenderer;
use App\Services\Subscriptions;
use App\Support\QrDesign;
use App\Support\QrPresets;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

/**
 * Create and edit QR codes: type, content, design and a live preview.
 */
class QrBuilder extends Component
{
    public ?QrCode $qrCode = null;

    public string $name = '';

    public string $type = 'url';

    /** @var array<string, mixed> */
    public array $payload = [];

    /** @var array<string, mixed> */
    public array $design = [];

    public bool $useGradient = false;

    public function mount(?QrCode $qrCode = null): void
    {
        if ($qrCode?->exists) {
            $this->authorize('update', $qrCode);

            $this->qrCode = $qrCode;
            $this->name = $qrCode->name;
            $this->type = $qrCode->type->value;
            $this->payload = array_merge($qrCode->type->defaults(), $qrCode->payload);
            $design = QrDesign::normalize($qrCode->design);
        } else {
            $this->payload = QrType::Url->defaults();
            $design = QrDesign::defaults();
        }

        $this->useGradient = $design['gradient'] !== null;
        $this->design = array_merge($design, ['gradient' => $design['gradient'] ?? ['from' => '#0e7490', 'to' => '#7c3aed']]);
    }

    public function applyPreset(string $key): void
    {
        $preset = QrPresets::find($key);

        if ($preset === null) {
            return;
        }

        $this->useGradient = $preset['gradient'] !== null;
        $this->design = array_merge($this->design, [
            'style' => $preset['style'],
            'eye' => $preset['eye'],
            'color' => $preset['color'],
            'bg' => $preset['bg'],
            'gradient' => $preset['gradient'] ?? $this->design['gradient'],
        ]);
    }

    public function selectType(string $type): void
    {
        $qrType = QrType::tryFrom($type);

        if (! $qrType || $qrType->value === $this->type) {
            return;
        }

        $this->type = $qrType->value;
        $this->payload = $qrType->defaults();
        $this->resetValidation();
    }

    public function save(Subscriptions $subscriptions, CreateQrCode $create, UpdateQrCode $update): void
    {
        $type = QrType::from($this->type);
        $design = $this->designForSaving();

        $this->validate(
            [
                'name' => ['required', 'string', 'max:120'],
                'type' => ['required', Rule::enum(QrType::class)],
                ...$type->rules(),
                ...collect(QrDesign::rules())->except($this->useGradient ? [] : ['design.gradient', 'design.gradient.from', 'design.gradient.to'])->all(),
            ],
            [],
            ['name' => __('Name')] + $type->attributes(),
        );

        if ($this->qrCode) {
            $this->authorize('update', $this->qrCode);
            $update->handle($this->qrCode, $this->name, $type, $this->payload, $design);
            session()->flash('toast', ['message' => __('Changes saved. Printed codes update instantly.'), 'type' => 'success']);
            $this->redirectRoute('qr.show', $this->qrCode, navigate: true);

            return;
        }

        try {
            $qrCode = $create->handle(auth()->user(), $this->name, $type, $this->payload, $design);
        } catch (QrQuotaExceeded) {
            $this->addError('quota', __('You have reached the limit of your plan.'));

            return;
        }

        session()->flash('toast', ['message' => __('Your QR code is ready.'), 'type' => 'success']);
        $this->redirectRoute('qr.show', $qrCode, navigate: true);
    }

    /**
     * @return array<string, mixed>
     */
    private function designForSaving(): array
    {
        return array_merge($this->design, ['gradient' => $this->useGradient ? $this->design['gradient'] : null]);
    }

    public function render(QrRenderer $renderer, Subscriptions $subscriptions): View
    {
        $design = QrDesign::normalize($this->designForSaving());
        $content = $this->qrCode?->shortUrl() ?? url('/x/preview');

        return view('livewire.dashboard.qr-builder', [
            'types' => QrType::cases(),
            'presets' => QrPresets::all(),
            'preview' => $renderer->svgDataUri($content, $design, 320),
            'lowContrast' => QrDesign::weakestContrast($design) < 3,
            'atLimit' => ! $this->qrCode && ! $subscriptions->canCreateQrCode(auth()->user()),
            'plan' => $subscriptions->currentPlan(auth()->user()),
        ])->title($this->qrCode ? __('Edit QR code') : __('Create QR code'));
    }
}
