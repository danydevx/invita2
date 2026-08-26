<?php

namespace Modules\VCards\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'listing_id' => $this->listing_id,
            'vcard_team_id' => $this->vcard_team_id,
            'type' => $this->type->value,
            'name' => $this->name,
            'slug' => $this->slug,
            'active' => $this->active,
            'design' => $this->design->value,
            'primary_color' => $this->primary_color,
            'shape' => $this->shape ?? 'circle',
            'image_x' => $this->image_x ?? 0,
            'image_y' => $this->image_y ?? 0,
            'font' => $this->font,
            'profile_photo' => $this->profile_photo,
            'hero_background_image' => $this->hero_background_image,
            'background_type' => $this->background_type ?? 'solid',
            'gradient_direction' => $this->gradient_direction ?? '135deg',
            'pattern_key' => $this->pattern_key,
            'hero_image_alpha' => $this->hero_image_alpha ?? 100,
            'body_background_type' => $this->body_background_type ?? 'solid',
            'body_primary_color' => $this->body_primary_color ?? '#ffffff',
            'body_gradient_direction' => $this->body_gradient_direction ?? '135deg',
            'body_pattern_key' => $this->body_pattern_key,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'zip' => $this->zip,
            'logo' => $this->logo,
            'badge' => $this->badge,
            'prefix' => $this->prefix,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'accreditations' => $this->accreditations,
            'preferred_name' => $this->preferred_name,
            'pronouns' => $this->pronouns?->value,
            'title' => $this->title,
            'department' => $this->department,
            'company' => $this->company,
            'headline' => $this->headline,
            'views' => $this->views,
            'public_url' => $this->public_url,
            'qr_code_url' => $this->qr_code_url,
            'team' => $this->whenLoaded('team'),
            'contacts' => $this->relationLoaded('contacts') ? VCardContactResource::collection($this->contacts)->resolve($request) : [],
            'fields' => $this->relationLoaded('fields') ? VCardFieldResource::collection($this->fields)->resolve($request) : [],
            'active_fields' => $this->relationLoaded('activeFields') ? VCardFieldResource::collection($this->activeFields)->resolve($request) : [],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
