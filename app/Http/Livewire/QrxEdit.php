<?php

namespace App\Http\Livewire;

use App\Models\Plan;
use App\Models\Style;
use Spatie\Color\Hex;
use Spatie\Color\Rgb;
use Spatie\Color\Rgba;
use App\Models\QrxCode;
use Livewire\Component;
use App\Models\FeatureUrl;
use App\Models\FeatureText;
use App\Models\FeatureWifi;
use App\Models\FeatureEmail;
use App\Models\FeaturePhone;
use App\Models\FeatureVcard;
use App\Models\Subscription;
use App\Models\FeatureMessage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrxEdit extends Component
{

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
    $color='#000000' ,$qrx ;
    // fill data by qr
    public function mount($qrx)
    {
        $this->tab          = $qrx->type;
        $this->qrxName      = $qrx->name;
        $this->size         = $qrx->style->size;
        $this->margin       = $qrx->style->margin;
        $this->eye          = $qrx->style->eye;
        $this->style        = $qrx->style->style;
        $this->color        = Rgb::fromString($qrx->style->color)->toHex()  ;
        $this->bgColor      = Rgb::fromString($qrx->style->bg_color)->toHex()  ;
        if($qrx->style->gradient_to != null && $qrx->style->gradient_from  != null){
        $this->gradientTo   = Rgb::fromString($qrx->style->gradient_to)->toHex();
        $this->gradientFrom = Rgb::fromString($qrx->style->gradient_from )->toHex() ;
        }
        switch ($qrx->type) {
            case "text":
                $this->text = $qrx->text->text;
                break;
            case "url":
                $this->url  = $qrx->url->url;
            break;
            case "email":
                $this->email   = $qrx->email->email;
                $this->subject = $qrx->email->subject;
                $this->body    = $qrx->email->body;
            case "message":
                $this->phone = $qrx->message->phone;
                $this->message = $qrx->message->message;
            break;
            case "phone":
                $this->phone = $qrx->phone->phone;
            break;
            case "vcard":
                $this->fullName = $qrx->vcard->full_name;
                $this->phone    = $qrx->vcard->phone;
                $this->tel      = $qrx->vcard->tel;
                $this->email    = $qrx->vcard->email;
                $this->address  = $qrx->vcard->address;
                $this->note     = $qrx->vcard->note;
            break;
            case "wifi":
                $this->wireless   = $qrx->wifi-> wireless_ssid ;
                $this->encryption = $qrx->wifi->encryption;
            break;
            default:
        }
    }
    // Tabs
    public function text(){
        $this->tab ='text'  ;
    }
    public function url(){
        $this->tab ='url'  ;
    }
    public function message(){
        $this->tab ='message'  ;
    }
    public function email(){
        $this->tab ='email'  ;
    }
    public function vcard(){
        $this->tab ='vcard'  ;
    }
    public function phone(){
        $this->tab ='phone'  ;
    }
    public function wifi(){
        $this->tab ='wifi'  ;
    }
    public function location(){
        $this->tab ='location'  ;
    }
    public function mp3(){
        $this->tab ='mp3'  ;
    }
    public function color(){
        $this->settings = 'color'  ;
    }
    public function style(){
        $this->settings = 'style'  ;
    }
    public function size(){
        $this->settings = 'size'  ;
    }
    public function gradient(){
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
            ->style($this->style)
            ->margin($this->margin)
            ->eye($this->eye)
            ->backgroundColor($bgColor->red(),$bgColor->green(),$bgColor->blue())
            ->gradient($gradientFrom->red(),$gradientFrom->green(),$gradientFrom->blue(),$gradientTo->red(),$gradientTo->green(),$gradientTo->blue(), 'diagonal')
            ->generate('https://hossam-x-studios.com');
        }else{
            // generate qr with color
            $QrCode = QrCode::size($this->size)
            ->style($this->style)
            ->margin($this->margin)
            ->eye($this->eye)
            ->color($color->red(),$color->green(),$color->blue())
            ->backgroundColor($bgColor->red(),$bgColor->green(),$bgColor->blue())
            ->generate('https://hossam-x-studios.com');
        }
        // return view and qr
        return view('livewire.qrx-edit',['QrCode' => $QrCode]);
    }

    // genrat Qr
    public function generat(){
        // cheack subscription
        $qrxCodes = QrxCode::where('client_id', Auth()->user()->id)->get();
        $subscription = Subscription::where('client_id', Auth()->user()->id)->get()->first();
        if($subscription != null){
            $plan = Plan::find($subscription->plan_id);
            if($subscription->expire_at >= now()  ){
                if(count($qrxCodes) <= $plan->qrs_count){
                    // covert colors from HEX -> RGB
                    $color        = Hex::fromString($this->color)->toRgb();
                    $bgColor      = Hex::fromString($this->bgColor)->toRgb();
                    if( $this->gradientTo != null && $this->gradientFrom != null){
                    $gradientTo   = Hex::fromString($this->gradientTo)->toRgb();
                    $gradientFrom = Hex::fromString($this->gradientFrom)->toRgb();
                    }
                    //update Qr
                    $qrxCode            = QrxCode::find($this->qrx->id);
                    $qrxCode->name      = $this->qrxName;
                    $qrxCode->type      = $this->tab;
                    $qrxCode->client_id = Auth()->user()->id;
                    $qrxCode->save();
                    //update style
                    $qrCodeStyle                 = Style::find($this->qrx->style->id);
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
                        ->size(250)
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
                        ->size(250)
                        ->errorCorrection('H')
                        ->encoding('UTF-8')
                        ->style($this->style)
                        ->margin($this->margin)
                        ->eye($this->eye)
                        ->color($color->red(),$color->green(),$color->blue())
                        ->backgroundColor($bgColor->red(),$bgColor->green(),$bgColor->blue())
                        ->generate(route('qr.show', $qrxCode->code) );
                    }
                    $path = $qrxCode->code.'.png';
                    Storage::disk('qrs')->delete($qrxCode->path);
                    Storage::disk('qrs')->put($path,$qr);
 
                    if($this->qrx->type == $this->tab){
                    switch ($this->tab) {
                        case "text":
                            $qrxCodeText              = FeatureText::find($this->qrx->text->id);
                            $qrxCodeText->text        = $this->text;
                            $qrxCodeText->qrx_code_id = $qrxCode->id;
                            $qrxCodeText->save();
                            redirect()->route('dashboard.qr.all');
                        break;
                        case "url":
                            $qrxCodeUrl              = FeatureUrl::find($this->qrx->url->id);
                            $qrxCodeUrl->url         = $this->url;
                            $qrxCodeUrl->qrx_code_id = $qrxCode->id;
                            $qrxCodeUrl->save();
                            redirect()->route('dashboard.qr.all');
                        break;
                        case "email":
                            $qrxCodeEmail              = FeatureEmail::find($this->qrx->email->id);
                            $qrxCodeEmail->email       = $this->email;
                            $qrxCodeEmail->subject     = $this->subject;
                            $qrxCodeEmail->body        = $this->body;
                            $qrxCodeEmail->qrx_code_id = $qrxCode->id;
                            $qrxCodeEmail->save();
                            redirect()->route('dashboard.qr.all');
                        case "message":
                            $qrxCodeMessage              = FeatureMessage::find($this->qrx->message->id);
                            $qrxCodeMessage->phone       = $this->phone;
                            $qrxCodeMessage->message     = $this->message;
                            $qrxCodeMessage->qrx_code_id = $qrxCode->id;
                            $qrxCodeMessage->save();
                            redirect()->route('dashboard.qr.all');
                        break;
                        case "phone":
                            $qrxCodePhone              = FeaturePhone::find($this->qrx->phone->id);
                            $qrxCodePhone->phone       = $this->phone;
                            $qrxCodePhone->qrx_code_id = $qrxCode->id;
                            $qrxCodePhone->save();
                            redirect()->route('dashboard.qr.all');
                        break;
                        case "vcard":
                            $qrxCodeVcard              = FeatureVcard::find($this->qrx->vcard->id);
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
                            $qrxCodeWifi                 = FeatureWifi::find($this->qrx->wifi->id);
                            $qrxCodeWifi->wireless_ssid  = $this->wireless;
                            $qrxCodeWifi->password       = $this->password;
                            $qrxCodeWifi->encryption     = $this->encryption;
                            $qrxCodeWifi->qrx_code_id    = $qrxCode->id;
                            $qrxCodeWifi->save();
                            redirect()->route('dashboard.qr.all');
                        break;
                        default:
                    }
                    }else{
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
                            default:
                        }
                    }
                }else{
                    redirect()->route('dashboard.error.plan.upgrade');
                }
            }else{
                redirect()->route('dashboard.error.plan.upgrade');
            }
        }else{
            redirect()->route('dashboard.error.plan.subscripe');
        }
    }

}

