<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request ,string $plan='price_1PAdULLRlOehGOvHY9ctreRf' )
    {
        return $request->user()
        ->newSubscription('prod_Q0en1KmBn5AGLI', $plan)
        ->allowPromotionCodes()
        ->checkout([
            'success_url' => route('dashboard'),
            'cancel_url' => route('home'),
        ]);
    }
}
