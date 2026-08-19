<?php

namespace Modules\Features\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Features\Models\Feature;
use Modules\Features\Models\FeatureCategory;

class DefaultFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            'hotel' => [
                ['title' => 'Wifi', 'description' => 'Conexion a internet wifi gratuita', 'icon' => 'bi bi-wifi'],
                ['title' => 'Estacionamiento', 'description' => 'Estacionamiento gratuito para huespedes', 'icon' => 'bi bi-p-circle'],
                ['title' => 'Piscina', 'description' => 'Piscina al aire libre', 'icon' => 'bi bi-water'],
                ['title' => 'Gimnasio', 'description' => 'Sala de ejercicios equipada', 'icon' => 'bi bi-person-bounding-box'],
                ['title' => 'Restaurante', 'description' => 'Restaurante en el establecimiento', 'icon' => 'bi bi-cup-hot'],
                ['title' => 'Bar', 'description' => 'Bar y salon de cocteles', 'icon' => 'bi bi-glass'],
                ['title' => 'Spa', 'description' => 'Centro de spa y bienestar', 'icon' => 'bi bi-droplet'],
                ['title' => 'Lavanderia', 'description' => 'Servicio de lavanderia', 'icon' => 'bi bi-droplet-half'],
                ['title' => 'Servicio de habitaciones', 'description' => 'Servicio a la habitacion 24/7', 'icon' => 'bi bi-bell'],
                ['title' => 'Aire acondicionado', 'description' => 'Climatizacion en todas las areas', 'icon' => 'bi bi-snow'],
                ['title' => 'Seguridad 24/7', 'description' => 'Vigilancia y seguridad permanente', 'icon' => 'bi bi-shield-check'],
                ['title' => 'Mascotas permitidas', 'description' => 'Aceptamos mascotas', 'icon' => 'bi bi-github'],
            ],
            'spa' => [
                ['title' => 'Masajes', 'description' => 'Servicio de masajes relajantes', 'icon' => 'bi bi-hand-index'],
                ['title' => 'Vapor', 'description' => 'Sauna de vapor', 'icon' => 'bi bi-cloudy'],
                ['title' => 'Sauna', 'description' => 'Sauna seco', 'icon' => 'bi bi-thermometer-sun'],
                ['title' => 'Jacuzzi', 'description' => 'Bano de hidromasaje', 'icon' => 'bi bi-droplet'],
                ['title' => 'Tratamientos faciales', 'description' => 'Cuidado y tratamientos para el rostro', 'icon' => 'bi bi-emoji-smile'],
                ['title' => 'Manicura', 'description' => 'Servicio de manicura profesional', 'icon' => 'bi bi-hand-thumbs-up'],
                ['title' => 'Pedicura', 'description' => 'Servicio de pedicura', 'icon' => 'bi bi-foot'],
                ['title' => 'Depilacion', 'description' => 'Servicios de depilacion', 'icon' => 'bi bi-scissors'],
                ['title' => 'Bodyscrub', 'description' => 'Exfoliacion corporal', 'icon' => 'bi bi-stars'],
                ['title' => 'Aromaterapia', 'description' => 'Terapia con aceites esenciales', 'icon' => 'bi bi-flower1'],
            ],
            'barberia' => [
                ['title' => 'Corte de cabello', 'description' => 'Cortes modernos y clasicos', 'icon' => 'bi bi-scissors'],
                ['title' => 'Afeitado', 'description' => 'Afeitado con navaja', 'icon' => 'bi bi-dash-circle'],
                ['title' => 'Barba', 'description' => 'Diseño y recorte de barba', 'icon' => 'bi bi-person'],
                ['title' => 'Tintura', 'description' => 'Coloracion capilar', 'icon' => 'bi bi-palette'],
                ['title' => 'Peinado', 'description' => 'Servicios de peinado', 'icon' => 'bi bi-stars'],
                ['title' => 'Tratamientos capilares', 'description' => 'Cuidado del cabello', 'icon' => 'bi bi-droplet-half'],
                ['title' => 'Cejas', 'description' => 'Diseno de cejas', 'icon' => 'bi bi-eye'],
                ['title' => 'Maquillaje', 'description' => 'Maquillaje profesional', 'icon' => 'bi bi-brush'],
            ],
            'salon-de-belleza' => [
                ['title' => 'Corte de cabello', 'description' => 'Cortes para damas y caballeros', 'icon' => 'bi bi-scissors'],
                ['title' => 'Tintura', 'description' => 'Coloracion y mechas', 'icon' => 'bi bi-palette'],
                ['title' => 'Peinado', 'description' => 'Peinados para eventos', 'icon' => 'bi bi-stars'],
                ['title' => 'Maquillaje', 'description' => 'Maquillaje social y profesional', 'icon' => 'bi bi-brush'],
                ['title' => 'Manicura', 'description' => 'Manicura francesa y mas', 'icon' => 'bi bi-hand-thumbs-up'],
                ['title' => 'Pedicura', 'description' => 'Cuidado de pies', 'icon' => 'bi bi-foot'],
                ['title' => 'Extension de pestanas', 'description' => 'Pestanas postizas', 'icon' => 'bi bi-eye'],
                ['title' => 'Depilacion', 'description' => 'Depilacion con cera', 'icon' => 'bi bi-scissors'],
                ['title' => 'Tratamientos faciales', 'description' => 'Limpieza y cuidado facial', 'icon' => 'bi bi-emoji-smile'],
                ['title' => 'Bodyscrub', 'description' => 'Exfoliacion corporal', 'icon' => 'bi bi-droplet'],
            ],
            'gimnasio' => [
                ['title' => 'Cardio', 'description' => 'Equipos de cardio', 'icon' => 'bi bi-heart-pulse'],
                ['title' => 'Peso libre', 'description' => 'Mancuernas y barras', 'icon' => 'bi bi-circle-fill'],
                ['title' => 'Maquinas', 'description' => 'Equipos de fuerza', 'icon' => 'bi bi-gear'],
                ['title' => 'Clases grupales', 'description' => 'Spinning, yoga, etc', 'icon' => 'bi bi-people'],
                ['title' => 'Vestuarios', 'description' => 'Vestuarios con lockers', 'icon' => 'bi bi-door-open'],
                ['title' => 'Duchas', 'description' => 'Duchas individuales', 'icon' => 'bi bi-droplet'],
                ['title' => 'Sauna', 'description' => 'Sauna seco', 'icon' => 'bi bi-thermometer-sun'],
                ['title' => 'Estacionamiento', 'description' => 'Estacionamiento gratuito', 'icon' => 'bi bi-p-circle'],
                ['title' => 'Wifi', 'description' => 'Conexion wifi gratuita', 'icon' => 'bi bi-wifi'],
                ['title' => 'Aire acondicionado', 'description' => 'Climatizacion', 'icon' => 'bi bi-snow'],
            ],
            'restaurante' => [
                ['title' => 'Wifi', 'description' => 'Conexion wifi gratuita', 'icon' => 'bi bi-wifi'],
                ['title' => 'Estacionamiento', 'description' => 'Estacionamiento gratuito', 'icon' => 'bi bi-p-circle'],
                ['title' => 'Terraza', 'description' => 'Area al aire libre', 'icon' => 'bi bi-sun'],
                ['title' => 'Musica en vivo', 'description' => 'Shows en vivo', 'icon' => 'bi bi-music-note'],
                ['title' => 'Area fumadores', 'description' => 'Zona para fumar', 'icon' => 'bi bi-cloudy'],
                ['title' => 'Reservaciones', 'description' => 'Aceptamos reservaciones', 'icon' => 'bi bi-calendar-check'],
                ['title' => 'Delivery', 'description' => 'Servicio a domicilio', 'icon' => 'bi bi-truck'],
                ['title' => 'Menu infantil', 'description' => 'Opciones para ninos', 'icon' => 'bi bi-emoji-smile'],
                ['title' => 'Aire acondicionado', 'description' => 'Climatizacion', 'icon' => 'bi bi-snow'],
                ['title' => 'Acceso minusvalidos', 'description' => 'Instalaciones accesibles', 'icon' => 'bi bi-wheelchair'],
            ],
            'salon-de-eventos' => [
                ['title' => 'Estacionamiento', 'description' => 'Amplio estacionamiento', 'icon' => 'bi bi-p-circle'],
                ['title' => 'Wifi', 'description' => 'Conexion wifi gratuita', 'icon' => 'bi bi-wifi'],
                ['title' => 'Sonido', 'description' => 'Equipo de sonido profesional', 'icon' => 'bi bi-speaker'],
                ['title' => 'Iluminacion', 'description' => 'Iluminacion ambiental', 'icon' => 'bi bi-lightbulb'],
                ['title' => 'Proyector', 'description' => 'Pantalla y proyector', 'icon' => 'bi bi-display'],
                ['title' => 'Catering', 'description' => 'Servicio de catering incluido', 'icon' => 'bi bi-cup-hot'],
                ['title' => 'Decoracion', 'description' => 'Decoracion personalizada', 'icon' => 'bi bi-palette'],
                ['title' => 'Seguridad', 'description' => 'Personal de seguridad', 'icon' => 'bi bi-shield-check'],
                ['title' => 'Aire acondicionado', 'description' => 'Climatizacion', 'icon' => 'bi bi-snow'],
                ['title' => 'Capacidad', 'description' => 'Hasta 500 personas', 'icon' => 'bi bi-people'],
            ],
            'consultorio-medico' => [
                ['title' => 'Estacionamiento', 'description' => 'Estacionamiento gratuito', 'icon' => 'bi bi-p-circle'],
                ['title' => 'Wifi', 'description' => 'Conexion wifi gratuita', 'icon' => 'bi bi-wifi'],
                ['title' => 'Acceso minusvalidos', 'description' => 'Instalaciones accesibles', 'icon' => 'bi bi-wheelchair'],
                ['title' => 'Laboratorio', 'description' => 'Laboratorio de analisis', 'icon' => 'bi bi-flask'],
                ['title' => 'Farmacia', 'description' => 'Farmacia en el lugar', 'icon' => 'bi bi-bandaid'],
                ['title' => 'Rayos X', 'description' => 'Equipo de radiografia', 'icon' => 'bi bi-display'],
                ['title' => 'Sala de espera', 'description' => 'Area comoda de espera', 'icon' => 'bi bi-lamp'],
                ['title' => 'Urgencias', 'description' => 'Servicio de urgencias', 'icon' => 'bi bi-exclamation-triangle'],
                ['title' => 'Seguros aceptados', 'description' => 'Aceptamos seguros medicos', 'icon' => 'bi bi-shield-check'],
                ['title' => 'Citas online', 'description' => 'Agenda tus citas en linea', 'icon' => 'bi bi-calendar-check'],
            ],
            'lavanderia' => [
                ['title' => 'Wifi', 'description' => 'Conexion wifi gratuita', 'icon' => 'bi bi-wifi'],
                ['title' => 'Estacionamiento', 'description' => 'Estacionamiento gratuito', 'icon' => 'bi bi-p-circle'],
                ['title' => 'Entrega a domicilio', 'description' => 'Delivery sin costo', 'icon' => 'bi bi-truck'],
                ['title' => 'Lavado express', 'description' => 'Servicio en 1 hora', 'icon' => 'bi bi-lightning'],
                ['title' => 'Tintoreria', 'description' => 'Servicio de tintoreria', 'icon' => 'bi bi-palette'],
                ['title' => 'Planchado', 'description' => 'Servicio de planchado', 'icon' => 'bi bi-iron'],
                ['title' => 'Lavado seco', 'description' => 'Lavado sin agua', 'icon' => 'bi bi-droplet-half'],
                ['title' => 'Alfombras', 'description' => 'Lavado de alfombras', 'icon' => 'bi bi-grid'],
            ],
            'veterinaria' => [
                ['title' => 'Estacionamiento', 'description' => 'Estacionamiento gratuito', 'icon' => 'bi bi-p-circle'],
                ['title' => 'Urgencias 24/7', 'description' => 'Servicio de urgencias', 'icon' => 'bi bi-exclamation-triangle'],
                ['title' => 'Cirugia', 'description' => 'Quirfano equipado', 'icon' => 'bi bi-scissors'],
                ['title' => 'Laboratorio', 'description' => 'Analisis clinicos', 'icon' => 'bi bi-flask'],
                ['title' => 'Radiografia', 'description' => 'Rayos X digitales', 'icon' => 'bi bi-display'],
                ['title' => 'Vacunacion', 'description' => 'Esquema completo de vacunas', 'icon' => 'bi bi-shield-check'],
                ['title' => 'Peluqueria', 'description' => 'Corte y banio', 'icon' => 'bi bi-scissors'],
                ['title' => 'Hospitalizacion', 'description' => 'Area de internacion', 'icon' => 'bi bi-hospital'],
                ['title' => 'Tienda', 'description' => 'Alimentos y accesorios', 'icon' => 'bi bi-shop'],
                ['title' => 'Pago electronico', 'description' => 'Aceptamos tarjetas', 'icon' => 'bi bi-credit-card'],
            ],
        ];

        foreach ($features as $slug => $categoryFeatures) {
            $category = FeatureCategory::where('slug', $slug)->first();
            if (!$category) {
                continue;
            }

            foreach ($categoryFeatures as $index => $featureData) {
                Feature::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'title' => $featureData['title'],
                    ],
                    [
                        'business_id' => null,
                        'source_feature_id' => null,
                        'description' => $featureData['description'],
                        'icon' => $featureData['icon'],
                        'image_path' => null,
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
