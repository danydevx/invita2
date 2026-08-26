<?php

namespace Modules\VCards\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VCardContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vcard_id' => $this->vcard_id,
            'type' => $this->type->value,
            'contact_type' => $this->contact_type->value,
            'country_code' => $this->country_code,
            'value' => $this->value,
            'extension' => $this->extension,
            'display_value' => $this->display_value,
            'tel_link' => $this->tel_link,
            'sort_order' => $this->sort_order,
        ];
    }
}
