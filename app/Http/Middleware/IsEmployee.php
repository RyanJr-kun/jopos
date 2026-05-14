<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsEmployee
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sudah login
        // 2. Cek apakah user tersebut memiliki profil employee
        if (Auth::check() && Auth::user()->employee) {
            // Jika dia employee, silakan masuk ke halaman JOPOS
            return $next($request);
        }

        // Jika dia HANYA customer (tidak punya relasi employee), lempar kembali ke Web Utama
        $mainWebUrl = request()->secure() ? 'https://' : 'http://';
        $mainWebUrl .= env('APP_DOMAIN', 'jocomputer.test');
        
        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}