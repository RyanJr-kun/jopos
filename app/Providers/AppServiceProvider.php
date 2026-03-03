<?php

namespace App\Providers;

use App\Listeners\CheckDeviceLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;


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
        Event::listen(
            Login::class,
            CheckDeviceLogin::class
        );

        Paginator::useBootstrapFour();
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

            $view->with('notifications', $notifications)
                ->with('unreadCount', $unreadCount);
        });
    }
}
