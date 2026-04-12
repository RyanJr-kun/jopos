<?php

namespace App\View\Components;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class marketHeader extends Component
{
    public $kategoris;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->kategoris = Category::all();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.marketHeader');
    }
}
