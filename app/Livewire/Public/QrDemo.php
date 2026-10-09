<?php

namespace App\Livewire\Public;

use App\Services\QrRenderer;
use App\Support\QrDesign;
use App\Support\QrPresets;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\On;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * "Try it" on the landing page: no account, nothing stored, rate-limited per IP.
 */
class QrDemo extends Component
{
    public string $text = '';

    public string $style = 'round';

    public string $eye = 'circle';

    public string $color = '#0f172a';

    public string $bg = '#ffffff';

    /** @var array{from: string, to: string}|null Set by a preset; cleared when the colour is picked by hand. */
    public ?array $gradient = null;

    public bool $limited = false;

    public function hydrate(): void
    {
        $key = 'qr-demo:'.request()->ip();
        $this->limited = RateLimiter::tooManyAttempts($key, config('qrx.demo.requests_per_minute'));

        if (! $this->limited) {
            RateLimiter::hit($key, 60);
        }
    }

    #[On('apply-preset')]
    public function applyPreset(string $key): void
    {
        $preset = QrPresets::find($key);

        if ($preset === null) {
            return;
        }

        [$this->style, $this->eye, $this->color, $this->bg, $this->gradient] = [$preset['style'], $preset['eye'], $preset['color'], $preset['bg'], $preset['gradient']];
    }

    public function updatedColor(): void
    {
        $this->gradient = null;
    }

    public function download(QrRenderer $renderer): ?StreamedResponse
    {
        if ($this->limited) {
            return null;
        }

        $svg = $renderer->render($this->content(), $this->design(), 'svg', 1000);

        return response()->streamDownload(fn () => print ($svg), 'qrx-demo.svg', ['Content-Type' => 'image/svg+xml']);
    }

    private function content(): string
    {
        $text = mb_substr(trim($this->text), 0, 500);

        return $text !== '' ? $text : url('/');
    }

    /**
     * @return array<string, mixed>
     */
    private function design(): array
    {
        return QrDesign::normalize(['style' => $this->style, 'eye' => $this->eye, 'color' => $this->color, 'bg' => $this->bg, 'gradient' => $this->gradient]);
    }

    public function render(QrRenderer $renderer): View
    {
        return view('livewire.public.qr-demo', [
            'preview' => $renderer->svgDataUri($this->content(), $this->design(), 280),
            'presets' => QrPresets::all(),
        ]);
    }
}
