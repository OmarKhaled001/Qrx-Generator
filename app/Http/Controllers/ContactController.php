<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store( Request $request){
        // check data valid 
        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'phone'   => ['required', 'numeric', 'min:11'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:255'],
        ]);
        // careate new countact
        $countact = new Contact();
        $countact->name     = $request->name ;
        $countact->email    = $request->email ;
        $countact->phone    = $request->phone ;
        $countact->subject  = $request->subject ;
        $countact->message  = $request->message ;
        $countact->save();
        redirect()->url('/');
    }
}
