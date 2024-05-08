<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SocialiteController extends Controller
{
    public function redirect($provider){
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider){
        $user_provider = Socialite::driver($provider)->user();
        return response()->json($user_provider);
    }
}
