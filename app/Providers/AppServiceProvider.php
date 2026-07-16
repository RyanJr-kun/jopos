<?php

namespace App\Providers;

use App\Listeners\CheckDeviceLogin;
use App\Models\PurchasePayment;
use App\Models\SalePayment;
use App\Models\User;
use App\Observers\PurchaseObserver;
use App\Observers\PurchasePaymentObserver;
use App\Observers\SaleObserver;
use App\Observers\SalePaymentObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Inventory\Models\Purchase;
use Modules\POS\Models\Sale;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    Event::listen(Login::class, CheckDeviceLogin::class);

    Paginator::defaultView('vendor.pagination.custom');
    Blade::directive('money', function ($expression) {
      return "<?php echo 'Rp ' . number_format($expression, 0, ',', '.'); ?>";
    });

    View::composer('layouts.contentNavbarLayout', function ($view) {
      $notifications = collect([]);
      $unreadCount = 0;

      if (Auth::check()) {
        /** @var User $user */
        $user = Auth::user();
        $notifications = $user->notifications()->take(5)->get();
        $unreadCount = $user->unreadNotifications()->count();
      }

      $view->with('notifications', $notifications)->with('unreadCount', $unreadCount);
    });

    ResetPassword::createUrlUsing(function ($user, string $token) {
      // Cek apakah email ini milik pelanggan
      $isCustomer = DB::table('customers')->where('email', $user->email)->exists();

      if ($isCustomer) {
        // Arahkan ke rute reset password khusus Customer
        return route('customer.password.reset', [
          'token' => $token,
          'email' => $user->email,
        ]);
      }

      // Jika karyawan, arahkan ke rute reset password Karyawan
      return route('password.reset', [
        'token' => $token,
        'email' => $user->email,
      ]);
    });

    SalePayment::observe(SalePaymentObserver::class);
    PurchasePayment::observe(PurchasePaymentObserver::class);
    Sale::observe(SaleObserver::class);
    Purchase::observe(PurchaseObserver::class);
  }
}
