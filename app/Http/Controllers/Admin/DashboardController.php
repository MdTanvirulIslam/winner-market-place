<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'customerCount' => User::where('role', 'customer')->count(),
            'adminCount' => User::whereIn('role', ['staff', 'super_admin'])->count(),
            'productCount' => Product::count(),
            'publishedCount' => Product::published()->count(),
        ]);
    }
}
