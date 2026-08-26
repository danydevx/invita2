<?php

namespace Modules\VCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\VCards\Enums\VCardContactSubtype;
use Modules\VCards\Enums\VCardContactType;

class VCardContact extends Model
{
    protected $table = 'vcard_contacts';

    protected $fillable = [
        'vcard_id',
        'type',
        'contact_type',
        'country_code',
        'value',
        'extension',
        'sort_order',
    ];

    protected $casts = [
        'type' => VCardContactType::class,
        'contact_type' => VCardContactSubtype::class,
    ];

    public function vcard(): BelongsTo
    {
        return $this->belongsTo(VCard::class, 'vcard_id');
    }

    public function getDisplayValueAttribute(): string
    {
        if (in_array($this->type->value, ['phone', 'whatsapp'])) {
            $prefix = $this->country_code ? "+{$this->country_code} " : '';
            $ext = $this->extension ? " ext. {$this->extension}" : '';
            return $prefix . $this->value . $ext;
        }

        return $this->value;
    }

    public function getTelLinkAttribute(): ?string
    {
        if ($this->type->value === 'phone' || $this->type->value === 'whatsapp') {
            $number = preg_replace('/[^0-9]/', '', $this->value);
            if ($this->country_code) {
                $number = $this->country_code . $number;
            }
            $type = $this->type->value === 'whatsapp' ? 'wa' : 'tel';
            return "{$type}:+{$number}";
        }

        if ($this->type->value === 'email') {
            return "mailto:{$this->value}";
        }

        return null;
    }
}
