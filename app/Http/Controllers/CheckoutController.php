<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request ,string $id)
    {
        $plan = Plan::find($id);
        $subscription = $request->user()
        ->newSubscription($plan->plan_id, $plan->price_id)
        ->allowPromotionCodes()
        ->checkout([
            'success_url' => route('dashboard'),
            'cancel_url' => route('home'),
        ]);
        $transactions = new Transaction;
        $transactions->amount = $plan->price;
        $transactions->save();
        return $subscription;

    }
}
