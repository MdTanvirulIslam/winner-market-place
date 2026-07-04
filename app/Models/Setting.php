<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'store_name',
        'support_email',
        'currency',
    ];

    private static ?self $cached = null;

    /**
     * The single settings row, created with defaults on first access and
     * memoized for the rest of the request.
     */
    public static function current(): self
    {
        return static::$cached ??= static::firstOrCreate(['id' => 1], [
            'store_name' => config('app.name'),
            'support_email' => '',
            'currency' => 'BDT',
        ]);
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::$cached = null);
    }
}
