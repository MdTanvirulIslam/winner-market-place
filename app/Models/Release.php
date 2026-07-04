<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Release extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'version',
        'notes',
        'file_path',
        'file_size',
        'download_count',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'released_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function fileSizeForHumans(): string
    {
        $bytes = (int) $this->file_size;

        if ($bytes >= 1_073_741_824) {
            return number_format($bytes / 1_073_741_824, 2) . ' GB';
        }

        if ($bytes >= 1_048_576) {
            return number_format($bytes / 1_048_576, 1) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024) . ' KB';
        }

        return $bytes . ' B';
    }
}
