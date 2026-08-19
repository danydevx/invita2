<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Appointment;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id' => 'sometimes|exists:services,id',
            'customer_name' => 'sometimes|required|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_email' => 'nullable|email|max:255',
            'starts_at' => 'sometimes|required|date',
            'status' => ['sometimes', 'required', 'in:' . implode(',', [
                Appointment::STATUS_PENDING,
                Appointment::STATUS_CONFIRMED,
                Appointment::STATUS_COMPLETED,
                Appointment::STATUS_CANCELLED,
                Appointment::STATUS_NO_SHOW,
            ])],
            'notes' => 'nullable|string',
            'cancellation_reason' => 'nullable|string',
        ];
    }
}
