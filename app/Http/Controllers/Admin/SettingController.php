<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        // Integration credentials live in .env and are never displayed;
        // the page only reports whether each one is configured.
        $integrations = [
            'License Manager URL' => filled(config('marketplace.license_manager.url')),
            'License Manager API token' => filled(config('marketplace.license_manager.token')),
            'SSLCommerz store ID' => filled(config('marketplace.sslcommerz.store_id')),
            'SSLCommerz store password' => filled(config('marketplace.sslcommerz.store_password')),
        ];

        return view('admin.settings.edit', [
            'setting' => Setting::current(),
            'integrations' => $integrations,
            'sandbox' => (bool) config('marketplace.sslcommerz.sandbox'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'store_name' => 'required|string|max:255',
            'support_email' => 'nullable|email|max:255',
            'currency' => 'required|string|max:10',
        ]);

        $data['support_email'] = $data['support_email'] ?? '';

        Setting::current()->update($data);

        return redirect()->route('admin.settings.edit')->with('success', 'Settings saved.');
    }
}
