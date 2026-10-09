<?php

namespace App\Services;

use App\Models\QrCode;
use App\Models\Scan;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Locale;

/**
 * Aggregates over the scans table. DATE() works on both MySQL and SQLite.
 */
final class ScanStats
{
    /**
     * @return array{daily: array<string, int>, total: int, unique: int, devices: array<string, int>, countries: array<string, int>}
     */
    public function forQrCode(QrCode $qrCode, int $days = 30): array
    {
        $scans = fn (): Builder => Scan::query()->where('qr_code_id', $qrCode->id)->where('scanned_at', '>=', $this->since($days));

        $daily = $this->daily($scans(), $days);

        return [
            'daily' => $daily,
            'total' => array_sum($daily),
            // Visitor hashes rotate daily, so uniques are counted per day and summed.
            'unique' => (int) $scans()->selectRaw('COUNT(DISTINCT visitor_hash) as visitors, DATE(scanned_at) as day')->groupBy('day')->get()->sum('visitors'),
            'devices' => $scans()->selectRaw('device, COUNT(*) as total')->groupBy('device')->orderByDesc('total')->pluck('total', 'device')->map(fn ($n) => (int) $n)->all(),
            'countries' => $scans()->whereNotNull('country')->selectRaw('country, COUNT(*) as total')->groupBy('country')->orderByDesc('total')->limit(5)
                ->pluck('total', 'country')
                ->mapWithKeys(fn ($n, string $code) => [$this->countryName($code) => (int) $n])
                ->all(),
        ];
    }

    /**
     * Daily scans across all of a user's QR codes.
     *
     * @return array<string, int>
     */
    public function forUser(User $user, int $days = 30): array
    {
        return $this->daily(
            Scan::query()->whereIn('qr_code_id', $user->qrCodes()->select('id'))->where('scanned_at', '>=', $this->since($days)),
            $days,
        );
    }

    /**
     * @param  Builder<Scan>  $scans
     * @return array<string, int> Y-m-d => count, every day present (zero-filled)
     */
    private function daily(Builder $scans, int $days): array
    {
        $counts = $scans->selectRaw('DATE(scanned_at) as day, COUNT(*) as total')->groupBy('day')->pluck('total', 'day');
        $since = $this->since($days);
        $series = [];

        for ($i = 0; $i < $days; $i++) {
            $day = $since->copy()->addDays($i)->toDateString();
            $series[$day] = (int) ($counts[$day] ?? 0);
        }

        return $series;
    }

    private function since(int $days): Carbon
    {
        return now()->subDays($days - 1)->startOfDay();
    }

    private function countryName(string $code): string
    {
        $name = Locale::getDisplayRegion('-'.$code, app()->getLocale());

        return $name !== '' && $name !== $code ? $name : $code;
    }
}
