<?php

namespace Modules\Ecommerce\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    // Melempar user ke halaman persetujuan Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Menangani kembalian data dari Google setelah user menyetujui
    public function handleGoogleCallback()
    {
        try {
            // Ambil data user dari Google
            $googleUser = Socialite::driver('google')->user();

            // Cek apakah email sudah terdaftar di database kita
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // JIKA AKUN BELUM ADA (Otomatis Register)
                DB::beginTransaction();
                try {
                    // Buat username dari email (sebelum @)
                    $emailParts = explode('@', $googleUser->email);
                    $baseUsername = Str::slug($emailParts[0], '');
                    $username = $baseUsername . rand(100, 999);
                    while (User::where('username', $username)->exists()) {
                        $username = $baseUsername . rand(1000, 9999);
                    }

                    // Buat User baru (password acak karena dia login via Google)
                    $user = User::create([
                        'name' => $googleUser->name,
                        'username' => $username,
                        'email' => $googleUser->email,
                        'password' => Hash::make(Str::random(24)), 
                        'status' => 1
                    ]);

                    // Buat profil Customer
                    Customer::create([
                        'user_id' => $user->id,
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'status' => 1
                    ]);

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return redirect()->route('customer.login')->withErrors(['error' => 'Gagal mendaftar via Google.']);
                }
            } else {
                // JIKA AKUN SUDAH ADA, pastikan dia punya profil customer
                // (Ini berguna jika admin tanpa sengaja mencoba login via halaman customer)
                $isCustomer = Customer::where('user_id', $user->id)->exists();
                if (!$isCustomer) {
                    // Buatkan profil customer agar tidak error saat belanja
                    Customer::create([
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'status' => 1
                    ]);
                }
            }

            // Loginkan user ke sistem
            Auth::login($user, true); // True untuk "Remember Me" otomatis

            return redirect()->intended('/')->with('success', 'Berhasil masuk dengan Google!');

        } catch (\Exception $e) {
            // Jika user menekan "Cancel" atau terjadi error lain
            return redirect()->route('customer.login')->withErrors(['error' => 'Proses autentikasi Google dibatalkan atau gagal.']);
        }
    }
}