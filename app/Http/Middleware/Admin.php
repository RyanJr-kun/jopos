<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Gunakan fungsi bawaan Spatie: hasRole()
        if (! $request->user()->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'Anda Tidak Memiliki Akses Ke Halaman Ini!');
        }
        return $next($request);
    }
}
