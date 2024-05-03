<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\Style;
use Spatie\Color\Hex;
use App\Models\QrxCode;
use Livewire\Component;
use App\Models\FeatureMp3;
use App\Models\FeatureUrl;
use App\Models\FeatureText;
use App\Models\FeatureWifi;
use App\Models\FeatureEmail;
use App\Models\FeaturePhone;
use App\Models\FeatureVcard;
use App\Models\Subscription;
use Livewire\WithFileUploads;
use App\Models\FeatureMessage;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class QrxCreate extends Component
{
    use WithFileUploads;

    // Property
    public $qrxName ='' ,$text ='' , $url ='' ,
    $address='' , $email ='' , $body='' ,
    $subject='' , $note='' , $tel='' ,
    $phone='' , $profile= '' , $message='' ,
    $fullName='' , $wireless='' , $password='' ,
    $encryption='WPA/WEP' , $tab='text' ,
    $settings='size' , $eye='square' ,
    $margin='1' ,  $size='200' ,
    $style='square' , $bgColor='#ffffff' ,
    $gradientFrom='' , $gradientTo='' ,
    $color='#000000' ,$qr;
    // Tabs
    public function text(){
        $this->resetExcept('tab');
        $this->tab ='text'  ;
    }
    public function url(){
        $this->resetExcept('tab');
        $this->tab ='url'  ;
    }
    public function message(){
        $this->resetExcept('tab');
        $this->tab ='message'  ;
    }
    public function email(){
        $this->resetExcept('tab');
        $this->tab ='email'  ;
    }
    public function vcard(){
        $this->resetExcept('tab');
        $this->tab ='vcard'  ;
    }
    public function phone(){
        $this->resetExcept('tab');
        $this->tab ='phone'  ;
    }
    public function wifi(){
        $this->resetExcept('tab');
        $this->tab ='wifi'  ;
    }
    public function location(){
    }
    public function mp3(){
        $this->tab ='mp3'  ;
    }
    public function color(){
        $this->reset('gradientFrom','gradientTo') ;
        $this->settings = 'color'  ;
    }
    public function style(){
        $this->settings = 'style'  ;
    }
    public function size(){
        $this->settings = 'size'  ;
    }
    public function gradient(){
        $this->reset('gradientFrom','gradientTo','color')  ;
        $this->settings = 'gradient'  ;
    }
    // Return view with qr
    public function render()
    {
        // covert colors from HEX -> RGB
        $color        = Hex::fromString($this->color)->toRgb();
        $bgColor      = Hex::fromString($this->bgColor)->toRgb();
        // generate qr with gradient color
        if( $this->gradientTo != null && $this->gradientFrom != null){
            $gradientTo   = Hex::fromString($this->gradientTo)->toRgb();
            $gradientFrom = Hex::fromString($this->gradientFrom)->toRgb();
            $QrCode = QrCode::size($this->size)
            ->encoding('UTF-8')
            ->style($this->style)
            ->margin($this->margin)
            ->eye($this->eye)
            ->color($color->red(),$color->green(),$color->blue())
            ->backgroundColor($bgColor->red(),$bgColor->green(),$bgColor->blue())
            ->gradient($gradientFrom->red(),$gradientFrom->green(),$gradientFrom->blue(),$gradientTo->red(),$gradientTo->green(),$gradientTo->blue(), 'diagonal')
            ->generate('https://hossam-x-studios.com');
        }else{
            // generate qr with color
            $QrCode = QrCode::size($this->size)
            ->encoding('UTF-8')
            ->style($this->style)
            ->margin($this->margin)
            ->eye($this->eye)
            ->color($color->red(),$color->green(),$color->blue())
            ->backgroundColor($bgColor->red(),$bgColor->green(),$bgColor->blue())
            ->generate('https://hossam-x-studios.com');
        }
        // return view and qr
        return view('livewire.qrx-create',['QrCode' => $QrCode]);
    }

    // genrat Qr
    public function generat(){
            if( count(Auth()->user()->subscriptions) > 0){
                $user = Auth('user')->user();
                $qrxCodex = QrxCode::where('user_id',$user->id )->count();
                $subscription = $user->subscriptions;
                $subscription = $user->subscriptions->first();
                $plan_id = $subscription->items->first()->stripe_product;
                $plan = Plan::where('plan_id',$plan_id)->get()->first();
                $startDate =  Carbon::parse($subscription->created_at);
                $endDate = $startDate->addYear(1);
                if($endDate > now() && $plan->qr_count > $qrxCodex){
                    // covert colors from HEX -> RGB
                    $color        = Hex::fromString($this->color)->toRgb();
                    $bgColor      = Hex::fromString($this->bgColor)->toRgb();
                    if( $this->gradientTo != null && $this->gradientFrom != null){
                    $gradientTo   = Hex::fromString($this->gradientTo)->toRgb();
                    $gradientFrom = Hex::fromString($this->gradientFrom)->toRgb();
                    }
                    //create Qr
                    $qrxCode            = new QrxCode();
                    $qrxCode->name      = $this->qrxName;
                    $qrxCode->type      = $this->tab;
                    $qrxCode->user_id = Auth()->user()->id;
                    $qrxCode->generateCode();
                    $qrxCode->save();
                    //create style
                    $qrCodeStyle                 = new Style();
                    $qrCodeStyle->size           = $this->size;
                    $qrCodeStyle->margin         = $this->margin;
                    $qrCodeStyle->eye            = $this->eye;
                    $qrCodeStyle->style          = $this->style;
                    $qrCodeStyle->color          = $color;
                    $qrCodeStyle->bg_color       = $bgColor;
                    if( $this->gradientTo != null && $this->gradientFrom != null){
                    $qrCodeStyle->gradient_from  = $gradientFrom;
                    $qrCodeStyle->gradient_to    = $gradientTo;
                    }
                    $qrCodeStyle->qrx_code_id    = $qrxCode->id;
                    $qrCodeStyle->save();
                    //store qr as image
                    if( $this->gradientTo != null && $this->gradientFrom != null){
                        $qr = QrCode::format('png')
                        ->size(500)
                        ->errorCorrection('H')
                        ->encoding('UTF-8')
                        ->style($this->style)
                        ->margin($this->margin)
                        ->eye($this->eye)
                        ->gradient($gradientFrom->red(),$gradientFrom->green(),$gradientFrom->blue(),$gradientTo->red(),$gradientTo->green(),$gradientTo->blue(), 'diagonal')
                        ->backgroundColor($bgColor->red(),$bgColor->green(),$bgColor->blue())
                        ->generate(route('qr.show', $qrxCode->code) );
                    }else{
                        $qr = QrCode::format('png')
                        ->size(500)
                        ->errorCorrection('H')
                        ->encoding('UTF-8')
                        ->style($this->style)
                        ->margin($this->margin)
                        ->eye($this->eye)
                        ->color($color->red(),$color->green(),$color->blue())
                        ->backgroundColor($bgColor->red(),$bgColor->green(),$bgColor->blue())
                        ->generate(route('qr.show', $qrxCode->code) );
                    }   
                    $path = 'qrxs/'.$qrxCode->code.'.png';
                    Storage::disk('public')->put($path,$qr);
                    $qrxCode->path = $path;
                    $qrxCode->save();
                    // create feture by tab
                    switch ($this->tab) {
                        case "text":
                            $qrxCodeText              = new FeatureText();
                            $qrxCodeText->text        = $this->text;
                            $qrxCodeText->qrx_code_id = $qrxCode->id;
                            $qrxCodeText->save();
                            redirect()->route('dashboard.qr.all');
                        break;
                        case "url":
                            $qrxCodeUrl              = new FeatureUrl();
                            $qrxCodeUrl->url         = $this->url;
                            $qrxCodeUrl->qrx_code_id = $qrxCode->id;
                            $qrxCodeUrl->save();
                            redirect()->route('dashboard.qr.all');
                        break;
                        case "email":
                            $qrxCodeEmail              = new FeatureEmail();
                            $qrxCodeEmail->email       = $this->email;
                            $qrxCodeEmail->subject     = $this->subject;
                            $qrxCodeEmail->body        = $this->body;
                            $qrxCodeEmail->qrx_code_id = $qrxCode->id;
                            $qrxCodeEmail->save();
                            redirect()->route('dashboard.qr.all');
                        case "message":
                            $qrxCodeMessage              = new FeatureMessage();
                            $qrxCodeMessage->phone       = $this->phone;
                            $qrxCodeMessage->message     = $this->message;
                            $qrxCodeMessage->qrx_code_id = $qrxCode->id;
                            $qrxCodeMessage->save();
                            redirect()->route('dashboard.qr.all');
                        break;
                        case "phone":
                            $qrxCodePhone              = new FeaturePhone();
                            $qrxCodePhone->phone       = $this->phone;
                            $qrxCodePhone->qrx_code_id = $qrxCode->id;
                            $qrxCodePhone->save();
                            redirect()->route('dashboard.qr.all');
                        break;
                        case "vcard":
                            $qrxCodeVcard              = new FeatureVcard();
                            $qrxCodeVcard->full_name   = $this->fullName;
                            $qrxCodeVcard->email       = $this->email;
                            $qrxCodeVcard->phone       = $this->phone;
                            $qrxCodeVcard->tel         = $this->tel;
                            $qrxCodeVcard->note        = $this->note;
                            $qrxCodeVcard->address     = $this->address;
                            $qrxCodeVcard->qrx_code_id = $qrxCode->id;
                            $qrxCodeVcard->save();
                            redirect()->route('dashboard.qr.all');
                        break;
                        case "wifi":
                            $qrxCodeWifi                 = new FeatureWifi();
                            $qrxCodeWifi->wireless_ssid  = $this->wireless;
                            $qrxCodeWifi->password       = $this->password;
                            $qrxCodeWifi->encryption     = $this->encryption;
                            $qrxCodeWifi->qrx_code_id    = $qrxCode->id;
                            $qrxCodeWifi->save();
                            redirect()->route('dashboard.qr.all');
                        break;
                    }
                    // if subscription exp redirect to upgrade plan
                    }else{
                        redirect()->route('dashboard.error.plan.upgrade');
                    }
                }else{
                    redirect()->route('dashboard.error.plan.subscripe');
                }
    }
}
