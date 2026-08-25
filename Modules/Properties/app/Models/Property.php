<?php

namespace Modules\Properties\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Listings\Models\Listing;

class Property extends Model
{
    protected $table = 'properties';

    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_RENTED = 'rented';
    public const STATUS_SOLD = 'sold';
    public const STATUS_TRANSFERRED = 'transferred';
    public const STATUS_ARCHIVED = 'archived';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PUBLISHED,
        self::STATUS_PAUSED,
        self::STATUS_RENTED,
        self::STATUS_SOLD,
        self::STATUS_TRANSFERRED,
        self::STATUS_ARCHIVED,
    ];

    public const OPERATION_SALE = 'sale';
    public const OPERATION_RENT = 'rent';
    public const OPERATION_TRANSFER = 'transfer';

    public const OPERATIONS = [
        self::OPERATION_SALE,
        self::OPERATION_RENT,
        self::OPERATION_TRANSFER,
    ];

    public const CURRENCY_MXN = 'MXN';
    public const CURRENCY_USD = 'USD';

    public const CURRENCIES = [
        self::CURRENCY_MXN,
        self::CURRENCY_USD,
    ];

    public const PERIOD_SINGLE = 'single';
    public const PERIOD_MONTHLY = 'monthly';
    public const PERIOD_WEEKLY = 'weekly';
    public const PERIOD_DAILY = 'daily';

    public const PERIODS = [
        self::PERIOD_SINGLE,
        self::PERIOD_MONTHLY,
        self::PERIOD_WEEKLY,
        self::PERIOD_DAILY,
    ];

    protected $fillable = [
        'listing_id',
        'property_type_id',
        'title',
        'slug',
        'property_code',
        'description',
        'operation_type',
        'price',
        'currency',
        'price_period',
        'main_image',
        'status',
        'is_featured',
        'is_public',
        'published_at',
        'country',
        'state',
        'state_code',
        'city',
        'municipality',
        'colony',
        'postal_code',
        'street',
        'exterior_number',
        'interior_number',
        'references',
        'latitude',
        'longitude',
        'show_exact_location',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'published_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'show_exact_location' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(PropertyValue::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function mainImage(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->where('is_main', true);
    }

    public function amenities(): HasMany
    {
        return $this->hasMany(PropertyAmenityProperty::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', self::STATUS_DRAFT);
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOfType($query, string $typeKey)
    {
        return $query->whereHas('propertyType', function ($q) use ($typeKey) {
            $q->where('key', $typeKey);
        });
    }

    public function scopeOfOperation($query, string $operation)
    {
        return $query->where('operation_type', $operation);
    }

    public function scopeInCity($query, ?string $city)
    {
        if ($city) {
            return $query->where('city', $city);
        }
        return $query;
    }

    public function scopeInState($query, ?string $state)
    {
        if ($state) {
            return $query->where('state_code', $state);
        }
        return $query;
    }

    public function scopePriceRange($query, $min, $max)
    {
        if ($min !== null) {
            $query->where('price', '>=', $min);
        }
        if ($max !== null) {
            $query->where('price', '<=', $max);
        }
        return $query;
    }

    public function getDynamicValues(): array
    {
        $values = [];
        foreach ($this->values as $value) {
            $fieldKey = $value->propertyField->field_key ?? null;
            if ($fieldKey) {
                $values[$fieldKey] = $this->getValueByType($value);
            }
        }
        return $values;
    }

    protected function getValueByType(PropertyValue $value): mixed
    {
        if ($value->value_text !== null) {
            return $value->value_text;
        }
        if ($value->value_number !== null) {
            return $value->value_number;
        }
        if ($value->value_boolean !== null) {
            return $value->value_boolean;
        }
        if ($value->value_date !== null) {
            return $value->value_date;
        }
        if ($value->value_json !== null) {
            return $value->value_json;
        }
        return null;
    }

    public function getFormattedPrice(): string
    {
        $formatted = number_format($this->price, 0);
        $currencyLabel = $this->currency === 'USD' ? 'USD' : 'MXN';
        $periodLabel = match ($this->price_period) {
            'monthly' => ' mensuales',
            'weekly' => ' semanales',
            'daily' => ' diarios',
            default => '',
        };
        return "\${$formatted} {$currencyLabel}{$periodLabel}";
    }

    public function getOperationLabel(): string
    {
        return match ($this->operation_type) {
            'sale' => 'Venta',
            'rent' => 'Renta',
            'transfer' => 'Traspaso',
            default => ucfirst($this->operation_type),
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'draft' => 'Borrador',
            'published' => 'Publicada',
            'paused' => 'Pausada',
            'rented' => 'Rentada',
            'sold' => 'Vendida',
            'transferred' => 'Traspasada',
            'archived' => 'Archivada',
            default => ucfirst($this->status),
        };
    }
}
