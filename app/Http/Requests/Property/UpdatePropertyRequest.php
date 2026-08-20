<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Properties\Models\Property;
use Modules\Properties\Models\PropertyField;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $business = $this->route('business');
        $property = $this->route('property');

        $rules = [
            'property_type_id' => [
                'sometimes',
                'exists:property_types,id',
                Rule::exists('property_types', 'id')->where('is_active', true),
            ],
            'title' => ['sometimes', 'required', 'string', 'max:180'],
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:180',
                Rule::unique('properties')->where('listing_id', $business?->id)->ignore($property?->id),
            ],
            'description' => ['nullable', 'string', 'max:5000'],
            'operation_type' => [
                'sometimes',
                'required',
                Rule::in(Property::OPERATIONS),
            ],
            'price' => ['sometimes', 'required', 'numeric', 'min:0', 'max:999999999.99'],
            'currency' => [
                'sometimes',
                'required',
                Rule::in(Property::CURRENCIES),
            ],
            'price_period' => [
                'sometimes',
                'required_if:operation_type,' . Property::OPERATION_RENT,
                Rule::in(Property::PERIODS),
            ],
            'main_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'status' => [
                'nullable',
                Rule::in(Property::STATUSES),
            ],
            'is_featured' => ['nullable', 'boolean'],
            'is_public' => ['nullable', 'boolean'],
            'dynamic_values' => ['nullable', 'array'],
            'remove_main_image' => ['nullable', 'boolean'],
            'amenity_ids' => ['nullable', 'array'],
            'amenity_ids.*' => ['integer', 'exists:property_amenities,id'],
            'country' => ['nullable', 'string', 'size:2'],
            'state' => ['nullable', 'string', 'max:100'],
            'state_code' => ['nullable', 'string', 'max:10'],
            'city' => ['nullable', 'string', 'max:100'],
            'municipality' => ['nullable', 'string', 'max:100'],
            'colony' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'street' => ['nullable', 'string', 'max:255'],
            'exterior_number' => ['nullable', 'string', 'max:20'],
            'interior_number' => ['nullable', 'string', 'max:20'],
            'references' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'show_exact_location' => ['nullable', 'boolean'],
        ];

        $propertyTypeId = $this->input('property_type_id') ?: ($property?->property_type_id);
        $rules = $this->addDynamicFieldRules($rules, $propertyTypeId);

        return $rules;
    }

    protected function addDynamicFieldRules(array $rules, ?int $propertyTypeId): array
    {
        if (! $propertyTypeId) {
            return $rules;
        }

        $mainFields = [
            'title', 'description', 'operation_type', 'price', 'currency',
            'price_period', 'status', 'is_featured', 'is_public', 'main_image',
            'property_type_id', 'slug', 'remove_main_image',
        ];

        $fields = PropertyField::where('property_type_id', $propertyTypeId)
            ->where('is_active', true)
            ->get();

        foreach ($fields as $field) {
            if (in_array($field->field_key, $mainFields)) {
                continue;
            }
            $fieldRules = $this->getFieldValidationRules($field);
            if (! empty($fieldRules)) {
                $rules["dynamic_values.{$field->field_key}"] = $fieldRules;
            }
        }

        return $rules;
    }

    protected function getFieldValidationRules(PropertyField $field): array
    {
        $rules = [];

        if ($field->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        switch ($field->field_type) {
            case 'text':
            case 'textarea':
                $rules[] = 'string';
                $rules[] = 'max:255';
                break;
            case 'email':
                $rules[] = 'email';
                break;
            case 'url':
                $rules[] = 'url';
                break;
            case 'phone':
                $rules[] = 'string';
                break;
            case 'number':
            case 'integer':
                $rules[] = 'numeric';
                break;
            case 'decimal':
            case 'price':
                $rules[] = 'numeric';
                break;
            case 'boolean':
                $rules[] = 'in:0,1,true,false';
                break;
            case 'date':
                $rules[] = 'date_format:Y-m-d';
                break;
            case 'select':
            case 'multiselect':
            case 'radio':
                $options = $field->fieldOptions->pluck('value')->toArray();
                if (! empty($options)) {
                    $rules[] = Rule::in($options);
                }
                break;
            case 'checkbox':
                $rules[] = 'array';
                break;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'property_type_id.exists' => 'El tipo de propiedad seleccionado no es válido.',
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no puede exceder 180 caracteres.',
            'operation_type.required' => 'El tipo de operación es obligatorio.',
            'operation_type.in' => 'El tipo de operación no es válido.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio no puede ser negativo.',
            'currency.required' => 'La moneda es obligatoria.',
            'currency.in' => 'La moneda seleccionada no es válida.',
            'price_period.required_if' => 'La periodicidad es obligatoria para rentas.',
            'main_image.image' => 'La imagen debe ser un archivo de imagen.',
            'main_image.mimes' => 'La imagen debe ser JPG, PNG o WebP.',
            'main_image.max' => 'La imagen no puede exceder 5MB.',
            'dynamic_values.*.required' => 'Este campo es obligatorio.',
            'dynamic_values.*.email' => 'El formato del email no es válido.',
            'dynamic_values.*.url' => 'El formato de la URL no es válido.',
            'dynamic_values.*.numeric' => 'El valor debe ser numérico.',
            'dynamic_values.*.date_format' => 'El formato de fecha debe ser AAAA-MM-DD.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('price') && is_string($this->input('price'))) {
            $price = str_replace([',', '$', ' '], '', $this->input('price'));
            $this->merge(['price' => $price]);
        }

        if ($this->has('dynamic_values') && is_string($this->input('dynamic_values'))) {
            $decoded = json_decode($this->input('dynamic_values'), true);
            if (is_array($decoded)) {
                $this->merge(['dynamic_values' => $decoded]);
            }
        }
    }
}
