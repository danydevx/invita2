<?php

namespace Modules\VCards\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVCardContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'type' => ['required', 'in:phone,email,whatsapp'],
            'contact_type' => ['in:personal,work,home'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'value' => ['required', 'string', 'max:255'],
            'extension' => ['nullable', 'string', 'max:20'],
        ];

        if ($this->input('type') === 'email') {
            $rules['value'] = ['required', 'email', 'max:255'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'type.required' => 'El tipo de contacto es requerido.',
            'type.in' => 'El tipo de contacto debe ser phone, email o whatsapp.',
            'value.required' => 'El valor del contacto es requerido.',
            'value.email' => 'El email debe ser una dirección de correo válida.',
        ];
    }
}
