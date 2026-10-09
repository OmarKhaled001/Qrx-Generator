<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\QrRenderer;
use App\Services\Subscriptions;
use App\Support\QrPresets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Request $request, Subscriptions $subscriptions, QrRenderer $renderer): View
    {
        return view('home', [
            // Only active plans are shown (and purchasable): approved change.
            'plans' => Plan::query()->active()->get(),
            'currentPlan' => $request->user() ? $subscriptions->currentPlan($request->user()) : null,
            // One sample code per style preset (gallery, use cases); they open this site. Cached: rendering is the slow part.
            'samples' => collect(QrPresets::all())->map(fn (array $preset, string $key) => Cache::remember(
                'landing-sample:'.$key.':'.md5(url('/')), now()->addDay(),
                fn () => $renderer->svgDataUri(url('/'), QrPresets::design($key), 240),
            ))->all(),
        ]);
    }
}
