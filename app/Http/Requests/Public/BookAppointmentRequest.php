<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class BookAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id' => ['nullable', 'integer', 'exists:listing_services,id'],
            'location_id' => ['nullable', 'integer', 'exists:listing_locations,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.exists' => 'El servicio seleccionado no existe.',
            'appointment_date.required' => 'Debe seleccionar una fecha.',
            'appointment_date.after_or_equal' => 'La fecha debe ser hoy o posterior.',
            'start_time.required' => 'Debe seleccionar un horario.',
            'start_time.date_format' => 'El formato del horario es inválido.',
            'customer_name.required' => 'El nombre es obligatorio.',
            'customer_email.required' => 'El email es obligatorio.',
            'customer_email.email' => 'El email no tiene un formato válido.',
        ];
    }
}