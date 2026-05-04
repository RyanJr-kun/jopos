<?php

namespace Modules\Ecommerce\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\Customer; // Pastikan model Customer sudah Anda buat
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Inventory\Models\Category;

class CustomerAuthController extends Controller
{
    // Menampilkan halaman Login Pelanggan
    public function showLoginForm()
    {
        $kategoris = Category::with('children')->whereNull('parent_id')->get();
        // Sesuaikan path view dengan lokasi file blade yang Anda buat sebelumnya
        return view('ecommerce::auth.login', compact('kategoris'));
    }

    // Proses Login Pelanggan
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            
            // Pengecekan: Pastikan user ini adalah Pelanggan (ada di tabel customers)
            // Jangan sampai Karyawan login lewat jalur Customer
            $isCustomer = DB::table('customers')->where('user_id', $user->id)->exists();
            
            if (!$isCustomer) {
                return back()->withErrors([
                    'email' => 'Akun tidak terdaftar sebagai pelanggan.',
                ])->onlyInput('email');
            }

            // Gunakan fitur "Remember Me" jika dicentang
            $remember = $request->has('remember');
            Auth::login($user, $remember);

            $request->session()->regenerate();
            
            // Redirect ke halaman Market/Home
            return redirect()->intended('/')->with('success', 'Selamat datang kembali, ' . $user->name);
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Menampilkan halaman Register Pelanggan
    public function showRegisterForm()
    {
        $kategoris = Category::with('children')->whereNull('parent_id')->get();
        return view('ecommerce::auth.register', compact('kategoris'));
    }

    // Proses Register Pelanggan
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // Pastikan input name 'password_confirmation' ada di view
        ]);

        DB::beginTransaction();
        try {
            // Generate username unik otomatis dari nama (karena DB mewajibkan username)
            $baseUsername = Str::slug($request->name, '');
            $username = $baseUsername . rand(100, 999);
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . rand(1000, 9999);
            }

            // 1. Buat data login di tabel users
            $user = User::create([
                'name' => $request->name,
                'username' => $username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 1
            ]);

            // 2. Buat profil di tabel customers
            Customer::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'status' => 1
            ]);

            DB::commit();

            // Langsung otomatis login setelah daftar
            Auth::login($user);
            $request->session()->regenerate();

            return redirect('/')->with('success', 'Pendaftaran berhasil! Selamat berbelanja.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat pendaftaran: ' . $e->getMessage()])->withInput();
        }
    }

    // Proses Logout Pelanggan
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Kembalikan ke halaman utama e-commerce setelah logout
        return redirect('/')->with('success', 'Anda telah berhasil logout.');
    }
}