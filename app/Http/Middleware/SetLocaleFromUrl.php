<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        $segment = $request->route('locale');
        $supported = config('locale.supported', []);

        if (is_string($segment) && in_array($segment, $supported, true)) {
            app()->setLocale($segment);
        } else {
            app()->setLocale(config('locale.default'));
        }

        URL::defaults(['locale' => app()->getLocale()]);

        return $next($request);
    }
}
