<?php

namespace App\Http\Livewire;

use Spatie\Color\Hex;
use Livewire\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrDemo extends Component
{
    // Property
    public $qrxName ='' ,$text ='' ,
    $settings='size' , $eye='square' ,
    $margin='1' ,  $size='300' ,
    $style='square' , $bgColor='#ffffff' ,
    $gradientFrom='' , $gradientTo='' ,
    $color='#000000' , $tab='text' ,$qr;

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

    public function render()
    {
        $color        = Hex::fromString($this->color)->toRgba();
        $bgColor      = Hex::fromString($this->bgColor)->toRgba();
        // generate qr with gradient color

            if( $this->gradientTo != null && $this->gradientFrom != null){
                $gradientTo   = Hex::fromString($this->gradientTo)->toRgba();
                $gradientFrom = Hex::fromString($this->gradientFrom)->toRgba();
                if($this->text != null ){
                $QrCode = QrCode::size($this->size)
                ->encoding('UTF-8')
                ->style($this->style)
                ->margin($this->margin)
                ->eye($this->eye)
                ->backgroundColor($bgColor->red(),$bgColor->green(),$bgColor->blue())
                ->gradient($gradientFrom->red(),$gradientFrom->green(),$gradientFrom->blue(),$gradientTo->red(),$gradientTo->green(),$gradientTo->blue(), 'diagonal')
                ->generate($this->text);
                }else{
                    $QrCode = QrCode::size($this->size)
                    ->encoding('UTF-8')
                    ->style($this->style)
                    ->margin($this->margin)
                    ->eye($this->eye)
                    ->gradient($gradientFrom->red(),$gradientFrom->green(),$gradientFrom->blue(),$gradientTo->red(),$gradientTo->green(),$gradientTo->blue(), 'diagonal')
                    ->backgroundColor($bgColor->red(),$bgColor->green(),$bgColor->blue())
                    ->generate('https://hossam-x-studios.com/en');
                }
            }else{
                if($this->text != null ){
                // generate qr with color
                $QrCode = QrCode::size($this->size)
                ->encoding('UTF-8')
                ->style($this->style)
                ->margin($this->margin)
                ->eye($this->eye)
                ->color($color->red(),$color->green(),$color->blue())
                ->backgroundColor($bgColor->red(),$bgColor->green(),$bgColor->blue())
                ->generate($this->text);
                }else{
                    $QrCode = QrCode::size($this->size)
                    ->encoding('UTF-8')
                    ->style($this->style)
                    ->margin($this->margin)
                    ->eye($this->eye)
                    ->color($color->red(),$color->green(),$color->blue())
                    ->backgroundColor($bgColor->red(),$bgColor->green(),$bgColor->blue())
                    ->generate('https://hossam-x-studios.com/en');
                }
            }

        // return view and qr
        return view('livewire.qr-demo',['QrCode' => $QrCode]);
    }
    public function download(){
        return response()->streamDownload(
            function () {
                $color        = Hex::fromString($this->color)->toRgba();
                $bgColor      = Hex::fromString($this->bgColor)->toRgba();
                // generate qr with gradient color
        
                    if( $this->gradientTo != null && $this->gradientFrom != null){
                        $gradientTo   = Hex::fromString($this->gradientTo)->toRgba();
                        $gradientFrom = Hex::fromString($this->gradientFrom)->toRgba();
                        if($this->text != null ){
                        $QrCode = QrCode::size($this->size)
                        ->encoding('UTF-8')
                        ->style($this->style)
                        ->margin($this->margin)
                        ->eye($this->eye)
                        ->backgroundColor($bgColor->red(),$bgColor->green(),$bgColor->blue())
                        ->gradient($gradientFrom->red(),$gradientFrom->green(),$gradientFrom->blue(),$gradientTo->red(),$gradientTo->green(),$gradientTo->blue(), 'diagonal')
                        ->generate($this->text);
                        }else{
                            $QrCode = QrCode::size($this->size)
                            ->encoding('UTF-8')
                            ->style($this->style)
                            ->margin($this->margin)
                            ->eye($this->eye)
                            ->gradient($gradientFrom->red(),$gradientFrom->green(),$gradientFrom->blue(),$gradientTo->red(),$gradientTo->green(),$gradientTo->blue(), 'diagonal')
                            ->backgroundColor($bgColor->red(),$bgColor->green(),$bgColor->blue())
                            ->generate('https://hossam-x-studios.com/en');
                        }
                    }else{
                        if($this->text != null ){
                        // generate qr with color
                        $QrCode = QrCode::size($this->size)
                        ->format('png')
                        ->errorCorrection('h')
                        ->encoding('UTF-8')
                        ->style($this->style)
                        ->margin($this->margin)
                        ->eye($this->eye)
                        ->color($color->red(),$color->green(),$color->blue())
                        ->backgroundColor($bgColor->red(),$bgColor->green(),$bgColor->blue())
                        ->generate($this->text);
                        }else{
                            $QrCode = QrCode::size($this->size)
                            ->format('png')
                            ->errorCorrection('h')
                            ->encoding('UTF-8')
                            ->style($this->style)
                            ->margin($this->margin)
                            ->eye($this->eye)
                            ->color($color->red(),$color->green(),$color->blue())
                            ->backgroundColor($bgColor->red(),$bgColor->green(),$bgColor->blue())
                            ->generate('https://hossam-x-studios.com/en');
                        }
                    }

                echo $QrCode;

            },
            'qrx-code.'.time().'.png',
            [
                'Content-Type' => 'image/png',
            ]
        );
    }

}
