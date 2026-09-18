<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request and attach essential HTTP security headers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Mencegah Clickjacking (tidak memperbolehkan website di-iframe oleh domain jahat)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 2. Mencegah MIME Type Sniffing (browser tidak akan menjalankan file selain MIME yang ditentukan)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 3. XSS Filter (perlindungan bawaan browser modern & legacy)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 4. Referrer Policy: Lindungi path dan parameter sensitif saat berpindah halaman
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 5. Batasi akses perangkat keras (Permissions-Policy)
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // 6. Jika dijalankan di lingkungan HTTPS, aktifkan HSTS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
