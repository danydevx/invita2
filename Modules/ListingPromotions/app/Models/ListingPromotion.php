<?php

namespace Modules\ListingPromotions\Models;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ListingPromotion extends Model
{

    protected $table = 'listing_promotions';

    protected $fillable = [
        'listing_id',
        'business_location_id',
        'name',
        'slug',
        'description',
        'image',
        'regular_price',
        'promotion_price',
        'coupon_code',
        'qr_code_path',
        'starts_at',
        'expires_at',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'regular_price' => 'decimal:2',
        'promotion_price' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($promotion) {
            $promotion->generateQrCode();
        });

        static::updated(function ($promotion) {
            if ($promotion->isDirty('coupon_code') && $promotion->qr_code_path) {
                $promotion->regenerateQrCode();
            }
        });
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingLocations\Models\ListingLocation::class, 'business_location_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ListingPromotionImage::class, 'promotion_id')->orderBy('sort_order');
    }

    public function getFirstImageAttribute(): ?string
    {
        $path = $this->images->first()?->path;
        if (!$path) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return '/storage/' . $path;
    }

    public function getImageAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        return '/storage/' . $value;
    }

    public function getQrCodePathAttribute(): ?string
    {
        $path = $this->attributes['qr_code_path'] ?? null;
        if (!$path) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return '/storage/' . $path;
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }

        return true;
    }

    public function getDiscountPercentage(): ?float
    {
        if (!$this->regular_price || !$this->promotion_price) {
            return null;
        }

        return round((($this->regular_price - $this->promotion_price) / $this->regular_price) * 100, 1);
    }

    public function generateQrCode(): void
    {
        if (!$this->coupon_code) {
            return;
        }

        try {
            $slug = $this->listing->slug ?? 'listing-' . $this->listing_id;
            $verifyUrl = url('/b/' . $slug . '/verify/' . $this->id . '/' . $this->coupon_code);

            $qrCode = new QrCode($verifyUrl);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $directory = 'promotions/' . $this->listing_id;
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            $filename = 'qr_' . $this->id . '_' . time() . '.png';
            $path = $directory . '/' . $filename;

            Storage::disk('public')->put($path, $result->getString());

            $this->updateQuietly(['qr_code_path' => $path]);
        } catch (\Exception $e) {
            \Log::error('QR code generation failed: ' . $e->getMessage());
        }
    }

    public function regenerateQrCode(): void
    {
        $this->generateQrCode();
    }
}
