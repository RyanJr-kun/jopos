<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmployeeProfile;
use App\Enums\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Modules\Inventory\Models\Product;

class SettingController extends Controller
{
    // =========================================================================
    // HELPER: Resolusi user + guard akses
    // =========================================================================

    /**
     * Temukan user berdasarkan username dan pastikan yang mengakses
     * adalah user itu sendiri atau admin.
     */
    private function resolveUser(string $username): User
    {
        $user = User::query()->where('username', $username)
            ->with(['employee.store', 'roles'])
            ->firstOrFail();

        // Hanya diri sendiri atau admin yang boleh mengakses
        if (Auth::id() !== $user->id && !Auth::user()->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki akses ke pengaturan user ini.');
        }

        return $user;
    }

    // =========================================================================
    // INDEX — Halaman Pengaturan
    // =========================================================================

    public function index(string $username)
    {
        $user     = $this->resolveUser($username);
        $employee = $user->employee;

        $notifSettings = $user->notification_settings ?? [
            'low_stock'          => true,
            'unregistered_serial' => true,
            'system'             => true,
        ];

        $recentNotifs = $user->notifications()->latest()->take(10)->get();

        // Cek apakah user yang profilnya sedang dibuka ini punya akses gudang/admin
        $canViewInventoryNotifs = $user->hasRole('admin') || $user->hasRole('gudang'); // Sesuaikan nama role Anda

        return view('content.hrd.setting.index', compact(
            'user',
            'employee',
            'notifSettings',
            'recentNotifs',
            'canViewInventoryNotifs'
        ));
    }

    // =========================================================================
    // UPDATE PROFIL
    // =========================================================================

    public function updateProfile(Request $request, string $username)
    {
        $user = $this->resolveUser($username);

        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:100'],
            'email'            => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'kontak'           => ['nullable', 'string', 'max:20', Rule::unique('employee_profiles', 'kontak')->ignore($user->employee?->id)],
            'alamat'           => ['nullable', 'string', 'max:500'],
            'jabatan'          => ['nullable', Rule::in(array_column(Jabatan::cases(), 'value'))],
            'avatar'           => ['nullable', 'string'], // filename temp dari FilePond
            'remove_avatar'    => ['nullable', 'boolean'],
        ]);

        // --- Update data akun ---
        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        // --- Update / buat employee profile ---
        $employeeData = [
            'kontak'  => $validated['kontak'] ?? null,
            'alamat'  => $validated['alamat'] ?? null,
            'jabatan' => $validated['jabatan'] ?? null,
        ];

        // Handle avatar
        $employee = $user->employee ?? new EmployeeProfile(['user_id' => $user->id]);

        if ($request->boolean('remove_avatar') && $employee->avatar) {
            Storage::disk('public')->delete($employee->avatar);
            $employeeData['avatar'] = null;
        }

        if (!empty($validated['avatar'])) {
            // Hapus avatar lama jika ada
            if ($employee->avatar) {
                Storage::disk('public')->delete($employee->avatar);
            }

            // Pindahkan dari temp ke permanen
            $tempPath = 'tmp/' . $validated['avatar'];
            $finalPath = 'avatars/' . $validated['avatar'];

            if (Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->move($tempPath, $finalPath);
                $employeeData['avatar'] = $finalPath;
            }
        }

        $user->employee()->updateOrCreate(
            ['user_id' => $user->id],
            $employeeData
        );

        return redirect()
            ->route('setting.index', $user->username)
            ->with('success', 'Profil berhasil diperbarui.');
    }

    // =========================================================================
    // UPDATE PASSWORD
    // =========================================================================

    public function updatePassword(Request $request, string $username)
    {
        $user = $this->resolveUser($username);

        $validated = $request->validate([
            'current_password'      => ['required', 'string'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);

        // Verifikasi password lama
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password saat ini tidak cocok.'])
                ->withInput();
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        return redirect()
            ->route('setting.index', $user->username)
            ->with('success', 'Password berhasil diperbarui.')
            ->with('active_tab', 'security');
    }

    // =========================================================================
    // UPDATE NOTIFICATION SETTINGS
    // =========================================================================

    public function updateNotifications(Request $request, string $username)
    {
        $user = $this->resolveUser($username);

        $validated = $request->validate([
            'low_stock'           => ['nullable', 'boolean'],
            'unregistered_serial' => ['nullable', 'boolean'],
            'system'              => ['nullable', 'boolean'],
        ]);

        $user->update([
            'notification_settings' => [
                'low_stock'           => (bool) ($validated['low_stock'] ?? false),
                'unregistered_serial' => (bool) ($validated['unregistered_serial'] ?? false),
                'system'              => (bool) ($validated['system'] ?? false),
            ],
        ]);

        return redirect()
            ->route('setting.index', $user->username)
            ->with('success', 'Preferensi notifikasi berhasil disimpan.')
            ->with('active_tab', 'notifications');
    }

    // =========================================================================
    // ALL NOTIFICATIONS — Halaman notifikasi lengkap (fleksibel)
    // =========================================================================

    public function allNotifications()
    {
        $authUser      = Auth::user();
        $notifSettings = $authUser->notification_settings ?? [
            'low_stock'           => true,
            'unregistered_serial' => true,
            'system'              => true,
        ];

        $sections = [];
        $isGudangOrAdmin = $authUser->hasRole('admin') || $authUser->hasRole('gudang');

        // ----- Seksi 1: Stok Rendah (Hanya jalan jika user admin/gudang & toggle ON) -----
        if ($isGudangOrAdmin && ($notifSettings['low_stock'] ?? true)) {
            $lowStockProducts = Product::select('products.*');
            $lowStockProducts = Product::select('products.*')
                ->selectSub(function ($query) {
                    $query->selectRaw('COALESCE(SUM(qty), 0)')
                        ->from('product_stocks')
                        ->whereColumn('product_stocks.product_id', 'products.id');
                }, 'total_qty')
                ->whereRaw('(SELECT COALESCE(SUM(qty), 0) FROM product_stocks WHERE product_stocks.product_id = products.id) <= products.stok_minimum')
                ->orderBy('total_qty', 'asc')
                ->get();

            $sections[] = [
                'type'        => 'low_stock',
                'title'       => 'Stok Rendah',
                'description' => 'Produk yang stoknya di bawah atau sama dengan batas minimum.',
                'icon'        => 'bx-package',
                'badge_color' => 'danger',
                'action_text' => 'Kelola Stok',
                'action_icon' => 'bx-edit',
                'action_btn'  => 'btn-outline-info',
                'items'       => $lowStockProducts->map(fn($p) => [
                    'id'          => $p->id,
                    'name'        => $p->name_product,
                    'image'       => $p->image_url,
                    'slug'        => $p->slug,
                    'meta'        => [
                        ['label' => 'Stok Minimum', 'value' => $p->stok_minimum, 'class' => ''],
                        ['label' => 'Sisa Stok',    'value' => $p->total_qty,     'class' => 'text-danger fw-bold'],
                    ],
                    'action_url'  => route('produk.edit', $p->slug),
                ]),
                'empty_text'  => '👍 Bagus! Tidak ada produk dengan stok rendah saat ini.',
            ];
        }

        // ----- Seksi 2: Produk butuh nomor seri (jika diaktifkan) -----
       if ($isGudangOrAdmin && ($notifSettings['unregistered_serial'] ?? true)) {
            $productsNeedingSerials = Product::select('products.*')
                ->where('wajib_seri', true)
                ->withSum('stocks as total_stok', 'qty')
                ->withCount(['serialNumbers as sn_tercatat_count' => fn($q) => $q->whereNotIn('status', ['Terjual', 'Hilang'])])
                ->whereRaw('(SELECT COALESCE(SUM(qty), 0) FROM product_stocks WHERE product_stocks.product_id = products.id) > (SELECT COUNT(*) FROM serial_numbers WHERE serial_numbers.product_id = products.id AND status NOT IN ("Terjual", "Hilang"))')
                ->orderBy('updated_at', 'desc')
                ->get();

            $sections[] = [
                'type'        => 'unregistered_serial',
                'title'       => 'Butuh Pendaftaran Nomor Seri',
                'description' => 'Produk wajib seri yang jumlah stoknya belum sesuai dengan nomor seri terdaftar.',
                'icon'        => 'bx-barcode',
                'badge_color' => 'warning',
                'action_text' => 'Kelola SN',
                'action_icon' => 'bx-upc-scan',
                'action_btn'  => 'btn-outline-primary',
                'items'       => $productsNeedingSerials->map(fn($p) => [
                    'id'          => $p->id,
                    'name'        => $p->name_product,
                    'image'       => $p->image_url,
                    'slug'        => $p->slug,
                    'meta'        => [
                        ['label' => 'Stok Fisik',      'value' => $p->total_stok,       'class' => ''],
                        ['label' => 'SN Tercatat',     'value' => $p->sn_tercatat_count, 'class' => ''],
                        ['label' => 'Butuh',           'value' => ($p->total_stok - $p->sn_tercatat_count) . ' SN', 'class' => 'text-danger fw-bold'],
                    ],
                    'action_url'  => route('serialNumber.index', ['produk_slug' => $p->slug]),
                ]),
                'empty_text'  => '🎉 Hebat! Semua produk sudah memiliki nomor seri yang sesuai.',
            ];
        }

        // ----- Seksi 3: Notifikasi sistem dari database Laravel -----
        if ($notifSettings['system'] ?? true) {
            $dbNotifications = $authUser->notifications()->latest()->take(20)->get();

            $sections[] = [
                'type'        => 'system',
                'title'       => 'Notifikasi Sistem',
                'description' => 'Aktivitas dan pemberitahuan sistem untuk akun Anda.',
                'icon'        => 'bx-bell',
                'badge_color' => 'primary',
                'action_text' => null, // tidak ada single action
                'items'       => $dbNotifications,
                'empty_text'  => '🔔 Tidak ada notifikasi sistem.',
            ];
        }

        // Hitung total unread (untuk badge di header)
        $totalUnread = $authUser->unreadNotifications()->count();

        return view('content.hrd.setting.all', compact('sections', 'totalUnread', 'notifSettings'));
    }
}
