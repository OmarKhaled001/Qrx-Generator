<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('profile', ['user' => $request->user()]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Accounts created through social login have no password: confirm with the email instead.
        $request->validateWithBag('userDeletion', $user->password === null
            ? ['confirm_email' => ['required', 'in:'.$user->email]]
            : ['password' => ['required', 'current_password']]);

        // Stop billing before the account (and its Stripe link) disappears.
        if ($user->subscribed()) {
            $user->subscription()->cancelNow();
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('toast', ['message' => __('Your account has been deleted.'), 'type' => 'success']);
    }
}
