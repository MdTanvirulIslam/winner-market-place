<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\WithDataTable;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    use WithDataTable;

    public function index(Request $request): View
    {
        $query = Coupon::query()
            ->when($request->filled('q'), fn ($query) => $query->where('code', 'like', '%' . $request->q . '%'));

        $coupons = $this->dataTable(
            $request,
            $query,
            ['code', 'value', 'used_count', 'expires_at', 'created_at'],
            fn ($query) => $query->orderByDesc('created_at')
        );

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create(): View
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Coupon::create($this->validated($request));

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($this->validated($request, $coupon));

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        // Orders keep their coupon_code snapshot — history stays intact.
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted.');
    }

    private function validated(Request $request, ?Coupon $coupon = null): array
    {
        $data = $request->validate([
            'code' => 'required|string|max:30|regex:/^[A-Za-z0-9-]{3,30}$/|unique:coupons,code' . ($coupon ? ',' . $coupon->id : ''),
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0.01|' . ($request->input('type') === 'percent' ? 'max:100' : 'max:9999999'),
            'expires_at' => 'nullable|date',
            'max_uses' => 'nullable|integer|min:1',
        ]);

        $data['code'] = strtoupper($data['code']);
        $data['active'] = $request->boolean('active');

        return $data;
    }
}
