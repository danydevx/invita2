<?php

namespace Modules\VCards\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VCardFieldResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vcard_id' => $this->vcard_id,
            'field_type_key' => $this->field_type_key,
            'label' => $this->label,
            'config' => $this->config,
            'display_value' => $this->display_value,
            'action_url' => $this->action_url,
            'sort_order' => $this->sort_order,
            'active' => $this->active,
            'field_type_definition' => $this->field_type_definition,
        ];
    }
}
