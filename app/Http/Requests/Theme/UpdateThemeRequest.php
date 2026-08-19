<?php

namespace App\Http\Requests\Theme;

use Illuminate\Foundation\Http\FormRequest;

class UpdateThemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'primary_color' => 'sometimes|required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'sometimes|required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'background_color' => 'sometimes|required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_color' => 'sometimes|required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'font_family' => 'nullable|string|max:100',
            'layout' => 'sometimes|required|in:classic,modern,minimal',
            'button_style' => 'sometimes|required|in:rounded,pill,square',
            'show_products' => 'boolean',
            'show_reviews' => 'boolean',
            'show_map' => 'boolean',
            'custom_css' => 'nullable|string',
        ];
    }
}
