<?php

namespace App\Http\Middleware;

use App\Support\Locales;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use Symfony\Component\HttpFoundation\Response;

/**
 * Locale = the one chosen in this session, else the signed-in user's
 * preference, else the browser's Accept-Language, else the app default.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = collect([
            $request->hasSession() ? $request->session()->get('locale') : null,
            $request->user()?->locale,
            $request->getPreferredLanguage(Locales::supported()),
        ])->first(fn (?string $candidate) => Locales::isSupported($candidate)) ?? config('app.locale');

        app()->setLocale($locale);
        Carbon::setLocale($locale);
        Number::useLocale($locale);

        return $next($request);
    }
}
