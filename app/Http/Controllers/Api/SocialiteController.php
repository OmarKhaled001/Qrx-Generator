<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect($provider){
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider){

        
            DB::beginTransaction();
            try {
                // get data or store frome provider
                $user_provider = Socialite::driver($provider)->user();
                return  response($user_provider,200);
            }
            catch (\Exception $e) {
                DB::rollback();
                return response(['error' => $e->getMessage()], 400);
            }
        }

}
