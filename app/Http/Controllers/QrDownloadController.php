<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Services\QrRenderer;
use App\Services\Subscriptions;
use App\Support\QrOptions;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\HeaderUtils;

class QrDownloadController extends Controller
{
    public function __invoke(Request $request, QrCode $qrCode, QrRenderer $renderer, Subscriptions $subscriptions): Response
    {
        Gate::authorize('view', $qrCode);

        $validated = $request->validate([
            'format' => ['required', Rule::in(array_keys(QrOptions::DOWNLOAD_FORMATS))],
            'size' => ['nullable', 'integer'],
        ]);

        $format = $validated['format'];

        // EPS (print vector) is a paid-plan feature, as listed on the pricing page.
        abort_if($format === 'eps' && $subscriptions->currentPlan($qrCode->user)->is_free, 403, __('EPS downloads are available on paid plans.'));

        $size = QrOptions::size($validated['size'] ?? QrOptions::DEFAULT_DOWNLOAD_SIZE, QrOptions::MAX_DOWNLOAD_SIZE);
        $fileName = (Str::slug($qrCode->name) ?: $qrCode->code).'.'.$format;

        return response($renderer->forQrCode($qrCode, $format, $size), 200, [
            'Content-Type' => QrOptions::DOWNLOAD_FORMATS[$format],
            'Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $fileName, Str::ascii($fileName)),
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
