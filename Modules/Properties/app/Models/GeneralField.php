<?php

namespace Modules\Properties\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneralField extends Model
{
    use HasFactory;

    protected $table = 'general_fields';

    public const TYPE_TEXT = 'text';
    public const TYPE_TEXTAREA = 'textarea';
    public const TYPE_NUMBER = 'number';
    public const TYPE_DECIMAL = 'decimal';
    public const TYPE_PRICE = 'price';
    public const TYPE_SELECT = 'select';
    public const TYPE_MULTISELECT = 'multiselect';
    public const TYPE_RADIO = 'radio';
    public const TYPE_CHECKBOX = 'checkbox';
    public const TYPE_DATE = 'date';
    public const TYPE_URL = 'url';
    public const TYPE_EMAIL = 'email';
    public const TYPE_PHONE = 'phone';
    public const TYPE_IMAGE = 'image';
    public const TYPE_BOOLEAN = 'boolean';

    public const FIELD_TYPES = [
        self::TYPE_TEXT,
        self::TYPE_TEXTAREA,
        self::TYPE_NUMBER,
        self::TYPE_DECIMAL,
        self::TYPE_PRICE,
        self::TYPE_SELECT,
        self::TYPE_MULTISELECT,
        self::TYPE_RADIO,
        self::TYPE_CHECKBOX,
        self::TYPE_DATE,
        self::TYPE_URL,
        self::TYPE_EMAIL,
        self::TYPE_PHONE,
        self::TYPE_IMAGE,
        self::TYPE_BOOLEAN,
    ];

    protected $fillable = [
        'general_field_section_id',
        'field_key',
        'field_type',
        'label',
        'description',
        'help_text',
        'placeholder',
        'default_value',
        'options',
        'validation_rules',
        'is_required',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(GeneralFieldSection::class, 'general_field_section_id');
    }

    public function fieldOptions(): HasMany
    {
        return $this->hasMany(GeneralFieldOption::class)->orderBy('sort_order');
    }

    public function activeFieldOptions(): HasMany
    {
        return $this->hasMany(GeneralFieldOption::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getValidationRulesArray(): array
    {
        $rules = $this->validation_rules ?? [];
        $result = [];

        if ($this->is_required) {
            $result[] = 'required';
        } else {
            $result[] = 'nullable';
        }

        foreach ($rules as $rule => $value) {
            switch ($rule) {
                case 'max':
                    $result[] = "max:{$value}";
                    break;
                case 'min':
                    $result[] = "min:{$value}";
                    break;
                case 'email':
                    $result[] = 'email';
                    break;
                case 'url':
                    $result[] = 'url';
                    break;
            }
        }

        if (in_array($this->field_type, [self::TYPE_NUMBER, self::TYPE_DECIMAL, self::TYPE_PRICE])) {
            $result[] = 'numeric';
        }

        return $result;
    }
}
