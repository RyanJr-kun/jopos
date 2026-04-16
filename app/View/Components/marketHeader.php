<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class MarketHeader extends Component
{
    /**
     * Daftar kategori utama (parent_id = null) beserta sub-kategorinya.
     * Data ini diteruskan dari controller via prop, bukan di-query ulang di sini.
     */
    public Collection $kategoris;

    /**
     * Create a new component instance.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $kategoris
     */
    public function __construct(Collection $kategoris)
    {
        $this->kategoris = $kategoris;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.market-header');
    }
}
