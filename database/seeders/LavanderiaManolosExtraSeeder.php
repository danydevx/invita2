<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Businesses\Models\Business;
use Modules\About\Models\BusinessAbout;
use Modules\SocialMedia\Models\BusinessSocialNetwork;
use Modules\Features\Models\BusinessFeature;
use Modules\Promotions\Models\BusinessPromotion;
use Modules\Reviews\Models\BusinessReview;

class LavanderiaManolosExtraSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::where('slug', 'lavanderia-manolos')->first();
        if (!$business) {
            $this->command->error('Business lavanderia-manolos not found.');
            return;
        }

        $this->about($business);
        $this->socialNetworks($business);
        $this->features($business);
        $this->promotions($business);
        $this->reviews($business);

        $this->command->info("===========================================");
        $this->command->info("LAVANDERIA MANOLOS - EXTRA DATA CARGADO");
        $this->command->info("===========================================");
        $this->command->info("About: " . BusinessAbout::where('business_id', $business->id)->count());
        $this->command->info("Redes: " . BusinessSocialNetwork::where('business_id', $business->id)->count());
        $this->command->info("Features: " . BusinessFeature::where('business_id', $business->id)->count());
        $this->command->info("Promos: " . BusinessPromotion::where('business_id', $business->id)->count());
        $this->command->info("Reviews: " . BusinessReview::where('business_id', $business->id)->count());
        $this->command->info("===========================================");
    }

    private function about(Business $business): void
    {
        BusinessAbout::updateOrCreate(
            ['business_id' => $business->id],
            [
                'title' => 'Lavanderia Manolos',
                'subtitle' => 'Mas de 20 anos cuidando tu ropa',
                'description' => 'Somos una lavanderia familiar con mas de 20 anos de experiencia en el rubro. Ofrecemos servicios de lavado, planchado, tintoreria y Lavado seco para particulares y empresas. Nuestro compromiso es la calidad, la puntualidad y el cuidado de cada prenda. Contamos con maquinas de ultima generacion y productos de primera calidad para garantizar los mejores resultados. Atendemos particulares, hoteles, restaurantes y empresas con contratos personalizados.',
                'image_path' => 'https://picsum.photos/seed/lavanderia-about/1200/600',
                'logo_path' => null,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );
    }

    private function socialNetworks(Business $business): void
    {
        $networks = [
            ['platform' => 'instagram', 'url' => 'https://instagram.com/lavanderia_manolos', 'username' => '@lavanderia_manolos', 'hero' => true, 'footer' => true, 'contact' => true, 'sort' => 1],
            ['platform' => 'facebook', 'url' => 'https://facebook.com/lavanderiamanolos', 'username' => 'Lavanderia Manolos', 'hero' => true, 'footer' => true, 'contact' => true, 'sort' => 2],
            ['platform' => 'whatsapp', 'url' => 'https://wa.me/5491145678901', 'username' => '+54 11 4567 8901', 'hero' => false, 'footer' => true, 'contact' => true, 'sort' => 3],
            ['platform' => 'tiktok', 'url' => 'https://tiktok.com/@lavanderia_manolos', 'username' => '@lavanderia_manolos', 'hero' => false, 'footer' => true, 'contact' => false, 'sort' => 4],
        ];

        foreach ($networks as $n) {
            BusinessSocialNetwork::updateOrCreate(
                ['business_id' => $business->id, 'platform' => $n['platform']],
                [
                    'url' => $n['url'],
                    'username' => $n['username'],
                    'icon_class' => \Modules\SocialMedia\Models\BusinessSocialNetwork::$platforms[$n['platform']]['icon'],
                    'is_active' => true,
                    'show_on_hero' => $n['hero'],
                    'show_on_footer' => $n['footer'],
                    'show_on_contact' => $n['contact'],
                    'sort_order' => $n['sort'],
                ]
            );
        }
    }

    private function features(Business $business): void
    {
        $featureIds = \DB::connection()->getPdo()->query("SELECT id FROM features WHERE category_id = 9 AND business_id IS NULL LIMIT 8")->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($featureIds as $i => $featureId) {
            BusinessFeature::updateOrCreate(
                ['business_id' => $business->id, 'feature_id' => $featureId],
                [
                    'is_active' => true,
                    'sort_order' => $i + 1,
                ]
            );
        }
    }

    private function promotions(Business $business): void
    {
        $promos = [
            [
                'name' => 'Descuento Primera Vez',
                'slug' => 'descuento-primera-vez',
                'description' => '20% de descuento en tu primera visita. Presentando este coupon en el local.',
                'regular_price' => 0,
                'promotion_price' => 20,
                'coupon_code' => 'PRIMERA20',
                'starts' => now(),
                'expires' => now()->addMonths(3),
                'sort' => 1,
            ],
            [
                'name' => 'Lavado + Planchado Promo',
                'slug' => 'lavado-plancha-promo',
                'description' => 'Lleva 5 prendas o mas y paga solo $600/kg en servicio de Lavado + Planchado. No incluye tintoreria.',
                'regular_price' => 800,
                'promotion_price' => 600,
                'coupon_code' => 'LAVAPLANCHA5',
                'starts' => now(),
                'expires' => now()->addMonth(),
                'sort' => 2,
            ],
            [
                'name' => 'Pack Empresas',
                'slug' => 'pack-empresas',
                'description' => 'Descuento del 15% para empresas con contrato mensual. Servicio de recogida y entrega sin cargo.',
                'regular_price' => 0,
                'promotion_price' => 15,
                'coupon_code' => 'EMPRESA15',
                'starts' => now(),
                'expires' => now()->addMonths(6),
                'sort' => 3,
            ],
            [
                'name' => 'Sabado de Promo',
                'slug' => 'sabado-promo',
                'description' => 'Todos los sabados: 2x1 en lavado de sabanas y toallas. Solo en servicio normal.',
                'regular_price' => 950,
                'promotion_price' => 475,
                'coupon_code' => 'SABADO2X1',
                'starts' => now(),
                'expires' => now()->addMonths(2),
                'sort' => 4,
            ],
            [
                'name' => 'Pack Edredones',
                'slug' => 'pack-edredones',
                'description' => 'Lavado de edredon + planchado a vapor por solo $1800. Ahorra $400.',
                'regular_price' => 2200,
                'promotion_price' => 1800,
                'coupon_code' => 'EDREDON1800',
                'starts' => now(),
                'expires' => now()->addMonth(),
                'sort' => 5,
            ],
        ];

        foreach ($promos as $p) {
            BusinessPromotion::updateOrCreate(
                ['business_id' => $business->id, 'slug' => $p['slug']],
                [
                    'name' => $p['name'],
                    'business_location_id' => null,
                    'description' => $p['description'],
                    'regular_price' => $p['regular_price'],
                    'promotion_price' => $p['promotion_price'],
                    'coupon_code' => $p['coupon_code'],
                    'starts_at' => $p['starts'],
                    'expires_at' => $p['expires'],
                    'is_active' => true,
                    'sort_order' => $p['sort'],
                ]
            );
        }
    }

    private function reviews(Business $business): void
    {
        $reviews = [
            ['name' => 'Maria Gonzalez', 'company' => null, 'comment' => 'Excelente servicio. Deje un traje para Lavado en seco y quedo impecable. La atencion es muy buena y los precios son razonables para la calidad que ofrecen.', 'rating' => 5, 'sort' => 1, 'active' => true],
            ['name' => 'Carlos Rodriguez', 'company' => 'Hotel Central', 'comment' => 'Contratamos el servicio de pack empresas para nuestro hotel. La recogida y entrega es puntual, las prendas siempre vuelven en perfecto estado. Muy recomendables.', 'rating' => 5, 'sort' => 2, 'active' => true],
            ['name' => 'Laura Fernandez', 'company' => null, 'comment' => 'Lleve sabanas y toallas. El lavado quedo perfecto, suave todo. El planchado esta includio y las devolvieron en menos de 24hs. Volvere seguro.', 'rating' => 4, 'sort' => 3, 'active' => true],
            ['name' => 'Pedro Martinez', 'company' => null, 'comment' => 'Buen servicio de tintoreria. Me tinaron un sakó de gala y el color quedo uniforme. El unico detalle es que tardaron un poco mas de lo esperado.', 'rating' => 4, 'sort' => 4, 'active' => true],
            ['name' => 'Ana Lopez', 'company' => null, 'comment' => 'El servicio de lavado express es muy util cuando uno tiene urgencia. En 2 horas estuvo listo. Un poco caro pero vale la pena por la rapidez.', 'rating' => 4, 'sort' => 5, 'active' => true],
            ['name' => 'Javier Perez', 'company' => 'Restaurante Don Jose', 'comment' => 'Somos clientes hace 3 anos. El servicio de manteleria para restaurantes es excelente. Planchan perfecto y siempre entregan a tiempo.', 'rating' => 5, 'sort' => 6, 'active' => true],
            ['name' => 'Sofia Ruiz', 'company' => null, 'comment' => 'Muy conformes con el tratamiento anti manchas. Tenia una camisa con una mancha de vino que parecia imposible y la dejaron como nueva.', 'rating' => 5, 'sort' => 7, 'active' => true],
            ['name' => 'Diego Sanchez', 'company' => null, 'comment' => 'Buen local, buena atencion. El unico problema es que a veces hay que esperar porque siempre estan con mucho trabajo. Pero vale la pena.', 'rating' => 4, 'sort' => 8, 'active' => false],
        ];

        foreach ($reviews as $r) {
            BusinessReview::updateOrCreate(
                ['business_id' => $business->id, 'client_name' => $r['name']],
                [
                    'business_location_id' => null,
                    'company' => $r['company'],
                    'comment' => $r['comment'],
                    'rating' => $r['rating'],
                    'google_link' => null,
                    'is_active' => $r['active'],
                    'sort_order' => $r['sort'],
                ]
            );
        }
    }
}
