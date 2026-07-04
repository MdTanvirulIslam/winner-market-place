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

    /**
     * The single settings row, created with defaults on first access.
     */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'store_name' => config('app.name'),
            'support_email' => '',
            'currency' => 'BDT',
        ]);
    }
}
