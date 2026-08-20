<?php

namespace Modules\ListingContactForm\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ListingContactForm\Models\ListingContactForm;

class ListingContactFormController extends Controller
{
    public function show(string $shortcode)
    {
        $form = ListingContactForm::where('shortcode', $shortcode)->first();

        if (!$form) {
            return response()->json(['error' => 'Formulario no encontrado'], 404);
        }

        return response()->json([
            'id' => $form->id,
            'name' => $form->name,
            'shortcode' => $form->shortcode,
            'success_message' => $form->success_message,
            'show_phone' => $form->show_phone,
            'show_email' => $form->show_email,
            'fields' => $form->activeFields->map(fn ($field) => [
                'id' => $field->id,
                'name' => $field->field_name,
                'type' => $field->field_type,
                'label' => $field->label,
                'placeholder' => $field->placeholder,
                'is_required' => $field->is_required,
                'options' => $field->options ?? [],
            ]),
        ]);
    }
}
