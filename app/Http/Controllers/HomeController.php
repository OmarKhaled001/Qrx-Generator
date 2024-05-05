<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function getPlans(){
        if(Auth()->user()){
            if(count(Auth()->user()->subscriptions) > 0){
                $user   = Auth()->user();
                $subscription = $user->subscriptions->first();
                $plan_id = $subscription->items->first()->stripe_product;
                $plans = Plan::where('plan_id', '!=', $plan_id)->get();
                //retu plan 
                return view('Frontend.index',compact('plans'));        
            }else{
                $plans = Plan::all();
                //retu plan 
                return view('Frontend.index',compact('plans'));
            }
        }else{
            $plans = Plan::all();
            //retu plan 
            return view('Frontend.index',compact('plans'));
        }
    }
}
