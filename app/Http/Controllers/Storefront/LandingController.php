<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View
    {
        $featured = MenuItem::query()
            ->active()
            ->ordered()
            ->limit(6)
            ->get();

        return view('storefront.landing', [
            'featured' => $featured,
        ]);
    }
}
