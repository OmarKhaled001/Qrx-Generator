<?php

namespace App\Http\Controllers;

use App\Actions\Qr\RecordScan;
use App\Enums\QrType;
use App\Models\QrCode;
use App\Services\Subscriptions;
use App\Support\VCard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\HeaderUtils;

/**
 * The public page every printed QR code opens: /x/{code}.
 */
class ScanController extends Controller
{
    public function show(Request $request, string $code, RecordScan $recordScan, Subscriptions $subscriptions): Response|RedirectResponse
    {
        $qrCode = $this->available($code, $subscriptions, $unavailable);

        if (! $qrCode) {
            return $this->noStore(response()->view('scan.unavailable', ['reason' => $unavailable], 404));
        }

        $recordScan->handle($qrCode, $request);

        // Website codes go straight to the site (only http/https URLs pass validation).
        if ($qrCode->type === QrType::Url && self::isSafeUrl($qrCode->payload['url'] ?? null)) {
            return $this->noStore(redirect()->away($qrCode->payload['url']));
        }

        return $this->noStore(response()->view('scan.show', ['qrCode' => $qrCode]));
    }

    public function vcard(string $code, Subscriptions $subscriptions): Response
    {
        $qrCode = $this->available($code, $subscriptions);

        abort_unless($qrCode && $qrCode->type === QrType::Vcard, 404);

        $fileName = (Str::slug($qrCode->payload['full_name'] ?? '') ?: 'contact').'.vcf';

        return response(VCard::fromPayload($qrCode->payload), 200, [
            'Content-Type' => 'text/vcard; charset=utf-8',
            'Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $fileName),
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    public static function isSafeUrl(mixed $url): bool
    {
        return is_string($url) && in_array(strtolower((string) parse_url(trim($url), PHP_URL_SCHEME)), ['http', 'https'], true);
    }

    /**
     * The QR code if it can be shown; otherwise null with the reason
     * ("missing" or "unavailable": paused and over-limit look the same to scanners).
     */
    private function available(string $code, Subscriptions $subscriptions, ?string &$reason = null): ?QrCode
    {
        $qrCode = QrCode::query()->with('user')->where('code', $code)->first();

        if (! $qrCode) {
            $reason = 'missing';

            return null;
        }

        if ($qrCode->isPaused() || $qrCode->hasReachedScanLimit($subscriptions->currentPlan($qrCode->user))) {
            $reason = 'unavailable';

            return null;
        }

        return $qrCode;
    }

    private function noStore(Response|RedirectResponse $response): Response|RedirectResponse
    {
        // Every scan must reach us to be counted; and these pages are not for search engines.
        $response->headers->set('Cache-Control', 'no-store, private');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
