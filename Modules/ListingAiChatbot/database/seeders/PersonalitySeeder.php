<?php

namespace Modules\ListingAiChatbot\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ListingAiChatbot\Models\ChatbotPersonality;

class PersonalitySeeder extends Seeder
{
    public function run(): void
    {
        $personalities = [
            [
                'key' => 'professional',
                'display_name' => 'Profesional',
                'description' => 'Tono formal y experto. Ideal para soporte técnico, consultas profesionales y servicios B2B.',
                'system_prompt_hint' => 'Usa un lenguaje formal, preciso y técnico. Mantén un tono respetuoso y profesional en todo momento.',
                'default_temperature' => 0.60,
                'default_response_length' => 'medium',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'friendly',
                'display_name' => 'Amigable',
                'description' => 'Cercano y cálido sin ser informal. Perfecto para tiendas, servicios al cliente y reservas.',
                'system_prompt_hint' => 'Sé amable, cercano y servicial. Usa un tono cálido pero profesional.',
                'default_temperature' => 0.80,
                'default_response_length' => 'medium',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'formal',
                'display_name' => 'Formal',
                'description' => 'Muy estructurado y serio. Recomendado para instituciones, consultas médicas y legales.',
                'system_prompt_hint' => 'Mantén un tono muy formal y estructurado. Sé preciso y evita coloquialismos.',
                'default_temperature' => 0.50,
                'default_response_length' => 'long',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'casual',
                'display_name' => 'Casual',
                'description' => 'Desenfadado y conversacional. Ideal para moda, lifestyle, restaurants y marcas personales.',
                'system_prompt_hint' => 'Usa un tono casual y conversacional. Puedes usar expresiones coloquiales con moderación.',
                'default_temperature' => 0.90,
                'default_response_length' => 'short',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($personalities as $personality) {
            if (!ChatbotPersonality::where('key', $personality['key'])->exists()) {
                ChatbotPersonality::create($personality);
            }
        }
    }
}
