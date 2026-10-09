<?php

namespace App\Http\Controllers;

use App\Services\Subscriptions;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, Subscriptions $subscriptions): View
    {
        $user = $request->user();
        $plan = $subscriptions->currentPlan($user);

        return view('dashboard', [
            'plan' => $plan,
            'qrCount' => $user->qrCodes()->count(),
            'totalScans' => (int) $user->qrCodes()->sum('scan_count'),
            'topQrCodes' => $user->qrCodes()->orderByDesc('scan_count')->limit(5)->get(),
        ]);
    }
}
