<?php

namespace App\Models;

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
     * Tags allowed through in rich product descriptions. Only staff author
     * this content — the allowlist is defense-in-depth, not a sandbox.
     */
    private const RICH_TEXT_TAGS = '<p><br><strong><em><u><s><a><ul><ol><li><h2><h3><h4><blockquote><pre><code><span>';

    /**
     * The feature list, one entry per non-empty line — accepts both legacy
     * plain text and Quill HTML (paragraphs or bullet lists).
     *
     * @return list<string>
     */
    public function featureList(): array
    {
        return self::htmlToLines($this->features);
    }

    /**
     * @return list<string>
     */
    public function requirementList(): array
    {
        return self::htmlToLines($this->requirements);
    }

    /**
     * The full description as safe HTML: legacy plain text keeps its line
     * breaks; Quill HTML passes through a tag allowlist.
     */
    public function descriptionHtml(): string
    {
        $value = $this->description ?? '';

        if ($value === strip_tags($value)) {
            return nl2br(e($value));
        }

        return strip_tags($value, self::RICH_TEXT_TAGS);
    }

    /**
     * The short description as plain text, for cards and meta tags.
     */
    public function shortDescriptionText(): string
    {
        return trim(html_entity_decode(strip_tags($this->short_description ?? ''), ENT_QUOTES));
    }

    /**
     * @return list<string>
     */
    private static function htmlToLines(?string $value): array
    {
        $text = preg_replace('/<\/(p|li|div)>|<br\s*\/?>/i', "\n", $value ?? '');

        return array_values(array_filter(array_map(
            fn (string $line) => trim(html_entity_decode(strip_tags($line), ENT_QUOTES)),
            explode("\n", $text)
        ), fn (string $line) => $line !== ''));
    }

    public function latestRelease(): ?Release
    {
        return $this->releases->first();
    }
}
