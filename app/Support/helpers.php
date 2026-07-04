<?php

use App\Models\Setting;

if (! function_exists('currency_symbol')) {
    function currency_symbol(): string
    {
        return match (Setting::current()->currency) {
            'USD' => '$',
            default => '৳',
        };
    }
}

if (! function_exists('format_price')) {
    function format_price(float|string|null $amount): string
    {
        return currency_symbol() . number_format((float) $amount, 2);
    }
}
