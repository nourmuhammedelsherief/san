<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(in_array($request->segment(1) , ['restaurant'])):
            $lang = session()->has('lang_restaurant') ? session('lang_restaurant') : app()->getLocale();
            if(!session()->has('lang_restaurant')):
                session()->put('lang_restaurant' ,$lang );
            endif;

            app()->setLocale($lang);
        else:

            if (session()->has('locale')) {
                App::setLocale(session()->get('locale'));
            }
        endif;

        return $next($request);
    }
}
