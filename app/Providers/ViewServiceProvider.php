<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Composer untuk market header (sudah ada)
        View::composer('components.MarketHeader', function ($view) {
            $kategoris = Category::whereHas('products')
                ->orderBy('name')
                ->get();
            $view->with('kategoris', $kategoris);
        });

        // Composer untuk market footer (baru)
        View::composer('components.MarketFooter', function ($view) {
            $bestSellingCategories = Category::select('categories.name', 'categories.slug')
                ->join('products', 'categories.id', '=', 'products.category_id')
                ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
                ->groupBy('categories.id', 'categories.name', 'categories.slug')
                ->orderByRaw('SUM(sale_items.jumlah) DESC')
                ->limit(5) // Ambil 4 kategori teratas
                ->get();
            $view->with('bestSellingCategories', $bestSellingCategories);
        });
    }
}
