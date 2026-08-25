<?php

namespace Modules\Properties\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Properties\Models\PropertyAmenity;

class PropertyAmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            ['key' => 'pool', 'name' => 'Alberca', 'icon' => 'bi bi-water', 'is_active' => true],
            ['key' => 'gym', 'name' => 'Gimnasio', 'icon' => 'bi bi-activity', 'is_active' => true],
            ['key' => 'security', 'name' => 'Seguridad 24/7', 'icon' => 'bi bi-shield-check', 'is_active' => true],
            ['key' => 'parking', 'name' => 'Estacionamiento', 'icon' => 'bi bi-car-front', 'is_active' => true],
            ['key' => 'elevator', 'name' => 'Elevador', 'icon' => 'bi bi-arrow-up-circle', 'is_active' => true],
            ['key' => 'garden', 'name' => 'Jardín', 'icon' => 'bi bi-flower1', 'is_active' => true],
            ['key' => 'terrace', 'name' => 'Terraza', 'icon' => 'bi bi-sun', 'is_active' => true],
            ['key' => 'rooftop', 'name' => 'Roof garden', 'icon' => 'bi bi-house-up', 'is_active' => true],
            ['key' => 'bbq', 'name' => 'Área de BBQ', 'icon' => 'bi bi-fire', 'is_active' => true],
            ['key' => 'pool_heated', 'name' => 'Alberca climatizada', 'icon' => 'bi bi-thermometer-snow', 'is_active' => true],
            ['key' => 'jacuzzi', 'name' => 'Jacuzzi', 'icon' => 'bi bi-droplet', 'is_active' => true],
            ['key' => 'sauna', 'name' => 'Sauna', 'icon' => 'bi bi-thermometer-sun', 'is_active' => true],
            ['key' => 'steam_room', 'name' => 'Cuarto de vapor', 'icon' => 'bi bi-cloud', 'is_active' => true],
            ['key' => 'spa', 'name' => 'Spa', 'icon' => 'bi bi-stars', 'is_active' => true],
            ['key' => 'tennis_court', 'name' => 'Cancha de tenis', 'icon' => 'bi bi-circle', 'is_active' => true],
            ['key' => 'basketball_court', 'name' => 'Cancha de basketball', 'icon' => 'bi bi-circle-fill', 'is_active' => true],
            ['key' => 'paddle_tennis', 'name' => 'Cancha de paddle tennis', 'icon' => 'bi bi-diamond', 'is_active' => true],
            ['key' => 'game_room', 'name' => 'Sala de juegos', 'icon' => 'bi bi-gamepad', 'is_active' => true],
            ['key' => 'cinema', 'name' => 'Cine', 'icon' => 'bi bi-film', 'is_active' => true],
            ['key' => 'coworking', 'name' => 'Área de coworking', 'icon' => 'bi bi-laptop', 'is_active' => true],
            ['key' => 'pet_friendly', 'name' => 'Pet friendly', 'icon' => 'bi bi-heart', 'is_active' => true],
            ['key' => 'concierge', 'name' => 'Concierge', 'icon' => 'bi bi-person-check', 'is_active' => true],
            ['key' => 'laundry', 'name' => 'Área de lavandería', 'icon' => 'bi bi-droplet-half', 'is_active' => true],
            ['key' => 'storage', 'name' => 'Bodega', 'icon' => 'bi bi-box', 'is_active' => true],
            ['key' => 'bike_storage', 'name' => 'Área de bikes', 'icon' => 'bi bi-bicycle', 'is_active' => true],
            ['key' => 'smart_home', 'name' => 'Smart home', 'icon' => 'bi bi-house-gear', 'is_active' => true],
            ['key' => 'solar_panels', 'name' => 'Paneles solares', 'icon' => 'bi bi-solar', 'is_active' => true],
            ['key' => 'water_reserve', 'name' => 'Cisterna', 'icon' => 'bi bi-water', 'is_active' => true],
            ['key' => 'generator', 'name' => 'Planta de luz', 'icon' => 'bi bi-lightning', 'is_active' => true],
            ['key' => 'building', 'name' => 'Edificio', 'icon' => 'bi bi-building', 'is_active' => true],
            ['key' => 'house', 'name' => 'Casa', 'icon' => 'bi bi-house-door', 'is_active' => true],
            ['key' => 'house_check', 'name' => 'Casa verificada', 'icon' => 'bi bi-house-check', 'is_active' => true],
            ['key' => 'location', 'name' => 'Ubicación', 'icon' => 'bi bi-geo-alt', 'is_active' => true],
            ['key' => 'location_fill', 'name' => 'Ubicación exacta', 'icon' => 'bi bi-geo-alt-fill', 'is_active' => true],
            ['key' => 'cctv', 'name' => 'CCTV', 'icon' => 'bi bi-camera-video', 'is_active' => true],
            ['key' => 'wifi', 'name' => 'WiFi', 'icon' => 'bi bi-wifi', 'is_active' => true],
            ['key' => 'no_wifi', 'name' => 'Sin WiFi', 'icon' => 'bi bi-wifi-off', 'is_active' => true],
            ['key' => 'phone', 'name' => 'Teléfono', 'icon' => 'bi bi-telephone', 'is_active' => true],
            ['key' => 'air_conditioning', 'name' => 'Aire acondicionado', 'icon' => 'bi bi-snow', 'is_active' => true],
            ['key' => 'ventilation', 'name' => 'Ventilación', 'icon' => 'bi bi-wind', 'is_active' => true],
            ['key' => 'heating', 'name' => 'Calefacción', 'icon' => 'bi bi-fire', 'is_active' => true],
            ['key' => 'lock', 'name' => 'Cerradura', 'icon' => 'bi bi-lock', 'is_active' => true],
            ['key' => 'key', 'name' => 'Llave', 'icon' => 'bi bi-key', 'is_active' => true],
            ['key' => 'verified', 'name' => 'Verificado', 'icon' => 'bi bi-check-circle', 'is_active' => true],
            ['key' => 'floor', 'name' => 'Piso', 'icon' => 'bi bi-layers', 'is_active' => true],
            ['key' => 'size', 'name' => 'Metraje', 'icon' => 'bi bi-rulers', 'is_active' => true],
            ['key' => 'dollar', 'name' => 'Precio', 'icon' => 'bi bi-currency-dollar', 'is_active' => true],
            ['key' => 'euro', 'name' => 'Euro', 'icon' => 'bi bi-currency-euro', 'is_active' => true],
            ['key' => 'credit_card', 'name' => 'Tarjeta', 'icon' => 'bi bi-credit-card', 'is_active' => true],
            ['key' => 'child_area', 'name' => 'Área infantil', 'icon' => 'bi bi-emoji-smile', 'is_active' => true],
            ['key' => 'accessibility', 'name' => 'Accesibilidad', 'icon' => 'bi bi-person', 'is_active' => true],
            ['key' => 'ramp', 'name' => 'Rampa', 'icon' => 'bi bi-arrow-right', 'is_active' => true],
            ['key' => 'stairs', 'name' => 'Escaleras', 'icon' => 'bi bi-arrow-up', 'is_active' => true],
            ['key' => 'door', 'name' => 'Puerta', 'icon' => 'bi bi-door-open', 'is_active' => true],
            ['key' => 'maintenance', 'name' => 'Mantenimiento', 'icon' => 'bi bi-wrench', 'is_active' => true],
            ['key' => 'cleaning', 'name' => 'Limpieza', 'icon' => 'bi bi-trash', 'is_active' => true],
            ['key' => 'recycle', 'name' => 'Reciclaje', 'icon' => 'bi bi-recycle', 'is_active' => true],
            ['key' => 'trees', 'name' => 'Árboles', 'icon' => 'bi bi-tree', 'is_active' => true],
            ['key' => 'store', 'name' => 'Tienda', 'icon' => 'bi bi-shop', 'is_active' => true],
            ['key' => 'convenience', 'name' => 'Conveniencia', 'icon' => 'bi bi-cart-check', 'is_active' => true],
            ['key' => 'basket', 'name' => 'Canasta', 'icon' => 'bi bi-basket', 'is_active' => true],
            ['key' => 'medical', 'name' => 'Médico', 'icon' => 'bi bi-hospital', 'is_active' => true],
            ['key' => 'cloud', 'name' => 'Nube', 'icon' => 'bi bi-cloud', 'is_active' => true],
            ['key' => 'brightness', 'name' => 'Iluminación', 'icon' => 'bi bi-brightness-high', 'is_active' => true],
        ];

        $sortOrder = 0;
        foreach ($amenities as $amenity) {
            PropertyAmenity::create(array_merge($amenity, ['sort_order' => $sortOrder++]));
        }
    }
}
