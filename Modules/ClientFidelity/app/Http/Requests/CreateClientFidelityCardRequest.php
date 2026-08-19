<?php

namespace Modules\ClientFidelity\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateClientFidelityCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_phone' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'max_visits' => 'required|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'client_name.required' => 'El nombre del cliente es requerido.',
            'client_email.email' => 'El email debe ser un email valido.',
            'max_visits.required' => 'El numero de visitas es requerido.',
            'max_visits.min' => 'El numero de visitas debe ser al menos 1.',
            'max_visits.max' => 'El numero de visitas no puede exceder 100.',
        ];
    }
}
