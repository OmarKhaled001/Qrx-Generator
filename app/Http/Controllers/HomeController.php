<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function getPlans(){
        //check if user auth has subscription
        if($subscription = Auth()->user()->subscriptions->first()){
            //get plan
            $plan_id = $subscription->items->first()->stripe_product;
            $plans = Plan::all()->except('plan_id',$plan_id);
            //retu plan 
            return view('Frontend.index',compact($plans));
        }else{
            $plans = Plan::all();
            //retu plan 
            return view('Frontend.index',compact($plans));
        }
    }
}
