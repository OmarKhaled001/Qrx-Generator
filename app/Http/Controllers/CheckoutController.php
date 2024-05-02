<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request ,string $price ,string $plan)
    {
        return $request->user()
        ->newSubscription($plan, $price)
        ->allowPromotionCodes()
        ->checkout([
            'success_url' => route('dashboard'),
            'cancel_url' => route('home'),
        ]);
    }
}
