<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Pastikan nama view ini mengarah ke desain login khusus karyawan
        return view('content.auth.index'); 
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', 
            'password' => 'required|string', 
        ]);

        $loginValue = $request->input('login'); 
        $password = $request->input('password'); 

        $user = User::query()->where('username', $loginValue) 
            ->orWhere('email', $loginValue) 
            ->first(); 

        if ($user && Hash::check($password, $user->password)) { 
            
            // TAMBAHAN: Pengecekan apakah user ini benar-benar Karyawan.
            // Memeriksa apakah user memiliki relasi di tabel employee_profiles 
            // atau memeriksa hak akses/role (menggunakan model_has_roles).
            $isEmployee = \DB::table('employee_profiles')->where('user_id', $user->id)->exists();
            
            if (!$isEmployee) {
                return back()->withErrors([
                    'login' => 'Akses ditolak. Akun ini bukan akun karyawan.',
                ])->onlyInput('login');
            }

            Auth::login($user); 
            $request->session()->regenerate(); 
            
            return redirect()->intended('/dashboard')->with('success', 'Selamat Datang, ' . Auth::user()->name); 
        }

        return back()->withErrors([
            'login' => 'Email atau password salah.', 
        ])->onlyInput('login'); 
    }

    public function logout(Request $request)
    {
        Auth::logout(); 
        $request->session()->invalidate(); 
        $request->session()->regenerateToken(); 
        
        // Sesuaikan redirect logout agar kembali ke halaman login karyawan
        return redirect('/')->with('success', 'Anda telah berhasil logout.');
    }

    public function showLinkRequestForm()
    {
        // Pastikan path view ini sesuai dengan lokasi file forgot-password Anda
        // Berdasarkan file sebelumnya, sepertinya ada di folder content/auth/
        return view('content.auth.auth-forgot-password'); 
    }

    /**
     * Memvalidasi email dan mengirimkan link reset password.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(['status' => __($status)]);
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token)
    {
        // Melempar token dan email ke halaman form reset
        return view('content.auth.auth-reset-password', ['token' => $token, 'email' => $request->email]);
    }

    /**
     * Memproses password baru yang diinputkan user.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed', // Pastikan form punya input password_confirmation
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Update password user di database
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        // Jika berhasil, arahkan ke login dengan pesan sukses
        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('employee.login')->with('status', __($status));
        }

        // Jika gagal (token kadaluarsa/email salah), kembali ke form dengan error
        return back()->withErrors(['email' => __($status)]);
    }
}