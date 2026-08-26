<?php

namespace Modules\VCards\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\VCards\Models\VCardFieldType;

class UpdateVCardFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $fieldTypeKey = $this->input('field_type_key');
        $definition = VCardFieldType::getDefinition($fieldTypeKey);

        $rules = [
            'label' => ['nullable', 'string', 'max:255'],
            'active' => ['boolean'],
        ];

        if ($this->has('config_file')) {
            $rules['config_file'] = ['file', 'mimes:pdf', 'max:10240'];
            $rules['config'] = ['nullable', 'string'];
        } else {
            $rules['config'] = ['required', 'array'];
            $rules['config.show_in_hero'] = ['nullable', 'boolean'];

            if ($definition && isset($definition['schema'])) {
                foreach ($definition['schema'] as $schemaField) {
                    $fieldName = $schemaField['name'];
                    $fieldRules = [];

                    if ($schemaField['required'] ?? false) {
                        $fieldRules[] = 'required';
                    } else {
                        $fieldRules[] = 'nullable';
                    }

                    switch ($schemaField['type']) {
                        case 'email':
                            $fieldRules[] = 'email';
                            break;
                        case 'url':
                            $fieldRules[] = 'url';
                            break;
                        case 'tel':
                            $fieldRules[] = 'string';
                            break;
                        case 'file':
                            $fieldRules[] = 'file';
                            $fieldRules[] = 'mimes:pdf';
                            $fieldRules[] = 'max:10240';
                            break;
                        default:
                            $fieldRules[] = 'string';
                            break;
                    }

                    $rules["config.{$fieldName}"] = $fieldRules;
                }
            }
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('config') && is_string($this->config)) {
            $this->merge([
                'config' => json_decode($this->config, true),
            ]);
        }
    }
}
