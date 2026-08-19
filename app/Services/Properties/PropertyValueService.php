<?php

namespace App\Services\Properties;

use Modules\Properties\Models\Property;
use Modules\Properties\Models\PropertyField;
use Modules\Properties\Models\PropertyValue;

class PropertyValueService
{
    public function saveValues(Property $property, array $values): void
    {
        foreach ($values as $fieldKey => $value) {
            $field = PropertyField::where('field_key', $fieldKey)
                ->where('property_type_id', $property->property_type_id)
                ->first();

            if (! $field) {
                continue;
            }

            $this->saveValue($property, $field, $value);
        }
    }

    public function saveValue(Property $property, PropertyField $field, mixed $value): PropertyValue
    {
        $valueData = $this->prepareValueByType($field, $value);

        return PropertyValue::updateOrCreate(
            [
                'property_id' => $property->id,
                'property_field_id' => $field->id,
            ],
            $valueData
        );
    }

    protected function prepareValueByType(PropertyField $field, mixed $value): array
    {
        $data = [
            'value_text' => null,
            'value_number' => null,
            'value_boolean' => null,
            'value_date' => null,
            'value_json' => null,
        ];

        if ($value === null || $value === '') {
            return $data;
        }

        switch ($field->field_type) {
            case PropertyField::TYPE_NUMBER:
                $data['value_number'] = is_numeric($value) ? (int) $value : null;
                break;

            case PropertyField::TYPE_DECIMAL:
            case PropertyField::TYPE_PRICE:
                $data['value_number'] = is_numeric($value) ? (float) $value : null;
                break;

            case PropertyField::TYPE_BOOLEAN:
                $data['value_boolean'] = (bool) $value;
                break;

            case PropertyField::TYPE_DATE:
                $data['value_date'] = $value;
                break;

            case PropertyField::TYPE_MULTISELECT:
            case PropertyField::TYPE_CHECKBOX:
                $data['value_json'] = is_array($value) ? $value : json_decode($value, true);
                break;

            case PropertyField::TYPE_NUMBER:
            case PropertyField::TYPE_DECIMAL:
            case PropertyField::TYPE_PRICE:
                $data['value_number'] = is_numeric($value) ? $value : null;
                break;

            default:
                $data['value_text'] = is_array($value) ? json_encode($value) : (string) $value;
                break;
        }

        return $data;
    }

    public function deleteValuesForField(Property $property, int $fieldId): void
    {
        PropertyValue::where('property_id', $property->id)
            ->where('property_field_id', $fieldId)
            ->delete();
    }

    public function getValue(Property $property, string $fieldKey): mixed
    {
        $field = PropertyField::where('field_key', $fieldKey)
            ->where('property_type_id', $property->property_type_id)
            ->first();

        if (! $field) {
            return null;
        }

        $value = PropertyValue::where('property_id', $property->id)
            ->where('property_field_id', $field->id)
            ->first();

        return $value?->getValue();
    }
}
