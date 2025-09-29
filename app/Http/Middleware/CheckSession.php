<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Route yang tidak perlu login
        $excluded = [
            'login',
            'login/*',
            'otp',
            'otp/*',
            'request-new-otp',
            'captcha*',
            'monitoring/*',
            'monitoring',
            'asakai', // supaya gambar captcha bisa di-load
            'asakai/*',
        ];

        // Jika request cocok dengan route pengecualian, lewati middleware
        if ($request->is($excluded) || $request->routeIs([
            'login*',
            'show.otp',
            'verify.otp',
            'request.new.otp',
        ])) {
            return $next($request);
        }

        // Cek session login
        if (!session()->has('authenticated')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
