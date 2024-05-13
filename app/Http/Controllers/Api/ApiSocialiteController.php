<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Dotenv\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class ApiSocialiteController extends Controller
{
    
    public function Apiredirect(Request $request,$provider){
        $device_token = $request->device_token;
        return Socialite::driver($provider)->redirect();
    }

    public function Apicallback($provider){
        
            $user_provider = Socialite::driver($provider)->user();

            DB::beginTransaction();
            try {
                // get data or store frome provider
                $user_provider = Socialite::driver($provider)->user();
                // check if is login before 
                $user = User::where(['provider'=>$provider , 'provider_id'=>$user_provider->id])->first();
                if(! $user){
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

                return response($user);

            }
            
            catch (\Exception $e) {
                DB::rollback();
                return response(['error' => $e->getMessage()], 400);
            
        }

    }
    
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }


    protected function createNewToken($token){
        return response([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 36000000,
        ]);
    }
}
