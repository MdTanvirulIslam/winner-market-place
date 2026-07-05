<?php

namespace App\Models;

use App\Support\RichText;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'features',
        'requirements',
        'demo_url',
        'price',
        'sale_price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function releases(): HasMany
    {
        return $this->hasMany(Release::class)->orderByDesc('released_at')->orderByDesc('id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isOnSale(): bool
    {
        return $this->sale_price !== null && (float) $this->sale_price < (float) $this->price;
    }

    /**
     * The price a buyer actually pays.
     */
    public function effectivePrice(): float
    {
        return $this->isOnSale() ? (float) $this->sale_price : (float) $this->price;
    }

    /**
     * The feature list, one entry per non-empty line — accepts both legacy
     * plain text and Quill HTML (paragraphs or bullet lists).
     *
     * @return list<string>
     */
    public function featureList(): array
    {
        return RichText::lines($this->features);
    }

    /**
     * @return list<string>
     */
    public function requirementList(): array
    {
        return RichText::lines($this->requirements);
    }

    /**
     * The full description as safe HTML: legacy plain text keeps its line
     * breaks; Quill HTML passes through a tag allowlist.
     */
    public function descriptionHtml(): string
    {
        return RichText::html($this->description);
    }

    /**
     * The short description as plain text, for cards and meta tags.
     */
    public function shortDescriptionText(): string
    {
        return RichText::text($this->short_description);
    }

    public function latestRelease(): ?Release
    {
        return $this->releases->first();
    }
}
