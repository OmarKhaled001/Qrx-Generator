<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Plan;
use Spatie\Color\Hex;
use App\Models\Folder;
use App\Models\QrxCode;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Charts\MonthlyStatusChart;
use App\Charts\MonthlyQrxCodesChart;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrxController extends Controller
{

    public function statistics(MonthlyQrxCodesChart $QrType,MonthlyStatusChart $QrStatus){
        //title 
        $title = 'Dashboard';
        // get count qr
        $qrxs = QrxCode::where('user_id', Auth()->user()->id)->count();
        $topQrxs = QrxCode::where('user_id', Auth()->user()->id)->orderByDesc('scan_count')->take(3)->get();
        // get charts 
        $QrType = $QrType->build();
        $QrStatus = $QrStatus->build();
        //get plan & subscription if isset
        if( empty(Auth()->user()->subscriptions) ){
            $user = Auth('user')->user();
            $subscription = $user->subscriptions;
            $subscription = $user->subscriptions->first();
            $plan_id = $subscription->items->first()->stripe_product;
            $plan = Plan::where('plan_id',$plan_id)->get()->first();
            $now = Carbon::now();
            $startDate =  Carbon::parse($subscription->created_at);
            $endDate = $startDate->addYear(1);
            $totalDays = $now->diffInDays($startDate );
            $countDay = $endDate->diffInDays($startDate);
            // retunn view with data
        return view('Dashboard.index',compact('qrxs','title','QrType','QrStatus','topQrxs','plan','totalDays','endDate','countDay'));
        }else{
            return view('Dashboard.index',compact('qrxs','title'));   
        }
    }
    
    public function getAllQr(){
        // title
        $title = 'All Qr';
        // get all qrx by paginate (9 in page)
        $qrxCodes= QrxCode::where('user_id', Auth()->user()->id)->paginate(9);
        // return view with data
        return view('Dashboard.qrx-all',compact('qrxCodes','title'));
    }
    public function getAllActiveQr(){
        // title
        $title = 'Active QRs';
        // get active qrx by paginate (9 in page)
        $qrxCodes= QrxCode::where('user_id', Auth()->user()->id)->where('status','active')->paginate(9);
        // return view with data
        return view('Dashboard.qrx-active',compact('qrxCodes','title'));
    }
    public function getAllPauseQr(){
        // title
        $title = 'Pause QRs';
        // get active qrx by paginate (9 in page)
        $qrxCodes= QrxCode::where('user_id', Auth()->user()->id)->where('status','pause')->paginate(9);
        // return view with data
        return view('Dashboard.qrx-pause',compact('qrxCodes','title'));
    }
    public function getAllExpiredQr(){
        // title
        $title = 'Expired QRs';
        // get active qrx by paginate (9 in page)
        $qrxCodes= QrxCode::where('user_id', Auth()->user()->id)->where('status','exp')->paginate(9);
        // return view with data
        return view('Dashboard.qrx-exp',compact('qrxCodes','title'));
        
    }
    public function showQr(string $code)
    {           
        $qrxCode = QrxCode::where('code',$code)->get()->first();
        if($qrxCode->status == 'pause'){
            return view('Dashboard.error-not-found');
        }else{
            $qrxCode->status = 'active';
            $qrxCode->scan_count += 1;
            $qrxCode->save();
            return view('Dashboard.view',compact('qrxCode'));
        };

    }
    public function switchStatuQr(string $id)
    {   
        $qrxCode = QrxCode::find($id);
        if($qrxCode->status != 'active'){
            $qrxCode->status = 'active';
            $qrxCode->save();
            return redirect()->back();
        }else{
            $qrxCode->status = 'pause';
            $qrxCode->save();
            return redirect()->back();
        };

    }
    public function editQr(string $id)
    {   
        $qrx = QrxCode::find($id);
        return view('Dashboard.qrx-edit',compact('qrx'));

    }
    public function deleteQr(string $id)
    {   
        $qrxCode = QrxCode::find($id);
        Storage::disk('public')->delete($qrxCode->path);
        QrxCode::destroy($id);
        return redirect()->back();
    }
    public function downloadQr(Request $request)
    {   
        $qrxCode = QrxCode::find($request -> id);
        $qr = QrCode::format($request -> type)
        ->size(250)
        ->errorCorrection('H')
        ->encoding('UTF-8')
        ->style($qrxCode->style->style)
        ->margin($qrxCode->style->margin)
        ->eye($qrxCode->style->eye)

        ->generate(route('qr.show', $qrxCode->code) );

        return response()->streamDownload(
            $qr,
            $request -> name.$request -> type,
            [
                'Content-Type' => 'image/'.$request -> type,
            ]

        );
    }



}
