<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\Subscriptions;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Request $request, Subscriptions $subscriptions): View
    {
        return view('home', [
            // Only active plans are shown (and purchasable): approved change.
            'plans' => Plan::query()->active()->get(),
            'currentPlan' => $request->user() ? $subscriptions->currentPlan($request->user()) : null,
        ]);
    }
}
