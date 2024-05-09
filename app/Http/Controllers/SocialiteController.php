<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect($provider){
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider){
        // get data or store frome provider
        $user_provider = Socialite::driver($provider)->user();
        // check if is login before 
        $user = User::where(['provider'=>$provider , 'provider_id'=>$user_provider->id])->first();
        if(! $user){
            DB::beginTransaction();
            try {
                // create user 
                $user               =  new User;
                $user->name         =  $user_provider->name;
                $user->email        =  $user_provider->email;
                $user->password     =  Hash::make(Str::random(8));
                $user->provider     =  $provider;
                $user->provider_id  =  $user_provider->id;
                //save data
                $user->save();
                DB::commit();
            }
            catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->withErrors(['error' => $e->getMessage()]);
            }
            // login and return to home 
            Auth::login($user);
            return redirect()->route('dashboard');
        }
    }
}
