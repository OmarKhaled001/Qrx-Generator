<?php

namespace App\Actions\Qr;

use App\Models\QrCode;
use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Counts a scan and stores an analytics row without any raw IP address:
 * only a country code (from a trusted proxy header, if present) and a visitor
 * hash keyed with a secret that changes every day, so it can count unique
 * visitors within a day but cannot be reversed or linked across days.
 */
final class RecordScan
{
    private const BOTS = '/bot|crawl|spider|slurp|preview|facebookexternalhit|whatsapp|telegram|discord|slack|skype|curl|wget|python-requests|headless/i';

    /**
     * @return bool whether the scan was counted (bots and link previews are not)
     */
    public function handle(QrCode $qrCode, Request $request): bool
    {
        $userAgent = (string) $request->userAgent();

        if ($userAgent === '' || preg_match(self::BOTS, $userAgent)) {
            return false;
        }

        $now = now();

        QrCode::query()->whereKey($qrCode->getKey())->increment('scan_count', 1, ['last_scanned_at' => $now]);

        Scan::query()->create([
            'qr_code_id' => $qrCode->getKey(),
            'scanned_at' => $now,
            'device' => $this->device($userAgent),
            'referrer_host' => $this->referrerHost($request),
            'country' => $this->country($request),
            'visitor_hash' => $this->visitorHash((string) $request->ip(), $userAgent, $now->toDateString()),
        ]);

        return true;
    }

    private function device(string $userAgent): string
    {
        return match (true) {
            (bool) preg_match('/iPad|Tablet|PlayBook|Silk/i', $userAgent),
            preg_match('/Android/i', $userAgent) && ! preg_match('/Mobile/i', $userAgent) => 'tablet',
            (bool) preg_match('/Mobile|iPhone|iPod|Android|Windows Phone/i', $userAgent) => 'mobile',
            default => 'desktop',
        };
    }

    private function referrerHost(Request $request): ?string
    {
        $host = parse_url((string) $request->headers->get('referer'), PHP_URL_HOST);

        return is_string($host) ? Str::limit($host, 255, '') : null;
    }

    private function country(Request $request): ?string
    {
        $header = config('qrx.scans.country_header');
        $country = $header ? strtoupper((string) $request->headers->get($header)) : '';

        // XX = unknown, T1 = Tor (Cloudflare conventions).
        return preg_match('/^[A-Z]{2}$/', $country) && ! in_array($country, ['XX', 'T1'], true) ? $country : null;
    }

    private function visitorHash(string $ip, string $userAgent, string $day): string
    {
        $dailyKey = hash_hmac('sha256', 'scan-visitor|'.$day, (string) config('app.key'));

        return hash_hmac('sha256', $ip.'|'.$userAgent, $dailyKey);
    }
}
