<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\WithDataTable;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    use WithDataTable;

    public function index(Request $request): View
    {
        $query = User::where('role', 'customer')
            ->withCount('orders')
            ->when($request->filled('q'), fn ($query) => $query->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('email', 'like', '%' . $request->q . '%');
            }));

        $customers = $this->dataTable(
            $request,
            $query,
            ['name', 'email', 'orders_count', 'created_at'],
            fn ($query) => $query->orderByDesc('created_at')
        );

        return view('admin.customers.index', compact('customers'));
    }
}
