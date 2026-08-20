<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Listings\Models\Listing;
use Modules\Listings\Enums\ListingType;
use Modules\ListingLocations\Models\ListingLocation;
use Modules\ListingServices\Models\ListingService;
use Modules\ListingProducts\Models\ListingProduct;
use Modules\ListingGallery\Models\ListingGalleryImage;
use Modules\ListingLeads\Models\ListingLead;
use Modules\ListingAppointments\Models\ListingAppointment;
use Modules\ListingAppointments\Models\ListingAppointmentSlot;
use Modules\ListingAppointments\Enums\AppointmentStatus;
use Modules\ListingReviews\Models\ListingReview;

class BusinessTestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'member@test.com'],
            [
                'name' => 'Test Member',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $user->assignRole('member');

        $planBusiness = Plan::where('slug', 'business')->first();
        if (!$planBusiness) {
            $this->command->error("Plan 'business' not found.");
            return;
        }

        $subscription = Subscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'plan_id' => $planBusiness->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
            ]
        );

        $business = Listing::updateOrCreate(
            ['slug' => 'barberia-el-corte'],
            [
                'user_id' => $user->id,
                'name' => 'Barberia El Corte',
                'listing_type' => ListingType::BARBER_SHOP,
                'description' => 'La mejor barberia del barrio. Ofrecemos cortes klasicos y modernos.',
                'phone' => '+54 11 1234 5678',
                'email' => 'contacto@barberiaelcorte.com',
                'website' => 'https://barberiaelcorte.com',
                'timezone' => 'America/Argentina/Buenos_Aires',
                'currency' => 'ARS',
                'is_active' => true,
                'is_published' => true,
            ]
        );

        $business->syncAllModules();

        $location1 = BusinessLocation::updateOrCreate(
            [
                'listing_id' => $business->id,
                'name' => 'Sucursal Centro',
            ],
            [
                'address_line_1' => 'Av. Rivadavia 1234',
                'city' => 'CABA',
                'state' => 'CABA',
                'postal_code' => 'C1001',
                'country' => 'Argentina',
                'phone' => '+54 11 1234 5678',
                'email' => 'centro@barberiaelcorte.com',
                'is_primary' => true,
                'is_active' => true,
            ]
        );

        $location2 = BusinessLocation::updateOrCreate(
            [
                'listing_id' => $business->id,
                'name' => 'Sucursal Norte',
            ],
            [
                'address_line_1' => 'Av. Santa Fe 2345',
                'city' => 'CABA',
                'state' => 'CABA',
                'postal_code' => 'C1425',
                'country' => 'Argentina',
                'phone' => '+54 11 2345 6789',
                'email' => 'norte@barberiaelcorte.com',
                'is_primary' => false,
                'is_active' => true,
            ]
        );

        ListingService::updateOrCreate(
            [
                'listing_id' => $business->id,
                'slug' => 'corte-klasico',
            ],
            [
                'name' => 'Corte Klasico',
                'description' => 'Corte tradicional con tijera y maquina',
                'duration_minutes' => 30,
                'price' => 1500,
                'allows_online_booking' => true,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        ListingService::updateOrCreate(
            [
                'listing_id' => $business->id,
                'slug' => 'corte-moderno',
            ],
            [
                'name' => 'Corte Moderno',
                'description' => 'Corte con desainado y texturas',
                'duration_minutes' => 45,
                'price' => 2000,
                'allows_online_booking' => true,
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        ListingService::updateOrCreate(
            [
                'listing_id' => $business->id,
                'slug' => 'barba-completa',
            ],
            [
                'name' => 'Barba Completa',
                'description' => 'Corte de barba con toalla caliente',
                'duration_minutes' => 30,
                'price' => 1000,
                'allows_online_booking' => true,
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        ListingProduct::updateOrCreate(
            [
                'listing_id' => $business->id,
                'slug' => 'pomada-cola',
            ],
            [
                'name' => 'Pomada Cola',
                'description' => 'Pomada para peinado klassico',
                'price' => 800,
                'sku' => 'POM-001',
                'quantity' => 50,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ]
        );

        ListingProduct::updateOrCreate(
            [
                'listing_id' => $business->id,
                'slug' => 'aceite-barba',
            ],
            [
                'name' => 'Aceite para Barba',
                'description' => 'Aceite nutritivo para barba',
                'price' => 1200,
                'sku' => 'ACE-001',
                'quantity' => 30,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 2,
            ]
        );

ListingLead::updateOrCreate(
            ['listing_id' => $business->id, 'email' => 'juan@mail.com'],
            [
                'name' => 'Juan Perez',
                'phone' => '+54 11 5555 1234',
                'notes' => 'Me gustaria reservar un turno para corte',
                'source' => 'website',
                'status' => 'new',
            ]
        );

        ListingLead::updateOrCreate(
            ['listing_id' => $business->id, 'email' => 'marcos@mail.com'],
            [
                'name' => 'Marcos Garcia',
                'phone' => '+54 11 5555 5678',
                'notes' => 'Consulta por servicios de barba',
                'source' => 'website',
                'status' => 'contacted',
            ]
        );

        ListingLead::updateOrCreate(
            ['listing_id' => $business->id, 'email' => 'laura@mail.com'],
            [
                'name' => 'Laura Fernandez',
                'phone' => '+54 11 5555 4321',
                'notes' => 'Quiero saber precios para boda de mi hijo',
                'source' => 'referral',
                'status' => 'qualified',
            ]
        );

        ListingLead::updateOrCreate(
            ['listing_id' => $business->id, 'email' => 'diego@mail.com'],
            [
                'name' => 'Diego Martinez',
                'phone' => '+54 11 5555 8765',
                'notes' => 'Me recomendaron por un amigo',
                'source' => 'referral',
                'status' => 'new',
            ]
        );

        ListingAppointment::updateOrCreate(
            [
                'listing_id' => $business->id,
                'appointment_date' => now()->addDays(1)->toDateString(),
                'start_time' => '10:00',
            ],
            [
                'business_location_id' => $location1->id,
                'business_service_id' => $business->services()->first()->id,
                'customer_name' => 'Carlos Lopez',
                'customer_email' => 'carlos@mail.com',
                'customer_phone' => '+54 11 5555 9999',
                'end_time' => '10:30',
                'status' => AppointmentStatus::CONFIRMED,
            ]
        );

        ListingAppointment::updateOrCreate(
            [
                'listing_id' => $business->id,
                'appointment_date' => now()->addDays(2)->toDateString(),
                'start_time' => '14:00',
            ],
            [
                'business_location_id' => $location1->id,
                'business_service_id' => $business->services()->skip(1)->first()->id,
                'customer_name' => 'Pedro Gomez',
                'customer_email' => 'pedro@mail.com',
                'customer_phone' => '+54 11 5555 8888',
                'end_time' => '14:45',
                'status' => AppointmentStatus::PENDING,
            ]
        );

        $service1 = $business->services()->where('slug', 'corte-klasico')->first();
        $service2 = $business->services()->where('slug', 'corte-moderno')->first();
        $service3 = $business->services()->where('slug', 'barba-completa')->first();

        if ($service1) {
            ListingAppointmentSlot::updateOrCreate(
                [
                    'listing_id' => $business->id,
                    'business_service_id' => $service1->id,
                    'specific_date' => now()->addDays(1)->toDateString(),
                    'start_time' => '09:00',
                ],
                [
                    'end_time' => '10:00',
                    'is_available' => true,
                    'slots_available' => 2,
                ]
            );
            ListingAppointmentSlot::updateOrCreate(
                [
                    'listing_id' => $business->id,
                    'business_service_id' => $service1->id,
                    'specific_date' => now()->addDays(1)->toDateString(),
                    'start_time' => '10:00',
                ],
                [
                    'end_time' => '11:00',
                    'is_available' => true,
                    'slots_available' => 2,
                ]
            );
            ListingAppointmentSlot::updateOrCreate(
                [
                    'listing_id' => $business->id,
                    'business_service_id' => $service1->id,
                    'specific_date' => now()->addDays(1)->toDateString(),
                    'start_time' => '14:00',
                ],
                [
                    'end_time' => '15:00',
                    'is_available' => true,
                    'slots_available' => 1,
                ]
            );
            ListingAppointmentSlot::updateOrCreate(
                [
                    'listing_id' => $business->id,
                    'business_service_id' => $service1->id,
                    'day_of_week' => 1,
                    'start_time' => '09:00',
                ],
                [
                    'end_time' => '17:00',
                    'is_available' => true,
                    'slots_available' => 3,
                ]
            );
        }

        if ($service2) {
            ListingAppointmentSlot::updateOrCreate(
                [
                    'listing_id' => $business->id,
                    'business_service_id' => $service2->id,
                    'specific_date' => now()->addDays(2)->toDateString(),
                    'start_time' => '10:00',
                ],
                [
                    'end_time' => '11:30',
                    'is_available' => true,
                    'slots_available' => 1,
                ]
            );
            ListingAppointmentSlot::updateOrCreate(
                [
                    'listing_id' => $business->id,
                    'business_service_id' => $service2->id,
                    'specific_date' => now()->addDays(3)->toDateString(),
                    'start_time' => '15:00',
                ],
                [
                    'end_time' => '16:30',
                    'is_available' => true,
                    'slots_available' => 2,
                ]
            );
        }

        if ($service3) {
            ListingAppointmentSlot::updateOrCreate(
                [
                    'listing_id' => $business->id,
                    'business_service_id' => $service3->id,
                    'specific_date' => now()->addDays(1)->toDateString(),
                    'start_time' => '11:00',
                ],
                [
                    'end_time' => '12:00',
                    'is_available' => true,
                    'slots_available' => 1,
                ]
            );
            ListingAppointmentSlot::updateOrCreate(
                [
                    'listing_id' => $business->id,
                    'business_service_id' => $service3->id,
                    'day_of_week' => 3,
                    'start_time' => '14:00',
                ],
                [
                    'end_time' => '18:00',
                    'is_available' => true,
                    'slots_available' => 2,
                ]
            );
        }

        ListingReview::updateOrCreate(
            ['listing_id' => $business->id, 'client_name' => 'Juan Perez'],
            [
                'business_location_id' => $location1->id,
                'comment' => 'Excelente atencion y muy profesional. El corte quedo perfecto.',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        ListingReview::updateOrCreate(
            ['listing_id' => $business->id, 'client_name' => 'Maria Lopez'],
            [
                'business_location_id' => $location1->id,
                'comment' => 'Muy buena barberia, me dejaron barba excelente.',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        ListingReview::updateOrCreate(
            ['listing_id' => $business->id, 'client_name' => 'Carlos Garcia'],
            [
                'business_location_id' => $location2->id,
                'comment' => 'Buen servicio, lo recomiendo.',
                'rating' => 4,
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        ListingReview::updateOrCreate(
            ['listing_id' => $business->id, 'client_name' => 'Pedro Rodriguez'],
            [
                'business_location_id' => null,
                'comment' => 'Muy conforme con el corte. Volvere pronto.',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 4,
            ]
        );

        ListingReview::updateOrCreate(
            ['listing_id' => $business->id, 'client_name' => 'Ana Martinez'],
            [
                'business_location_id' => $location1->id,
                'comment' => 'Buen ambiente y atencion.',
                'rating' => 4,
                'is_active' => false,
                'sort_order' => 5,
            ]
        );

        $this->command->info("===========================================");
        $this->command->info("NEGOCIO DE PRUEBA CARGADO");
        $this->command->info("===========================================");
        $this->command->info("Usuario: member@test.com / password123");
        $this->command->info("Plan: {$planBusiness->name}");
        $this->command->info("Negocio: {$business->name}");
        $this->command->info("URL: http://saas.local/b/barberia-el-corte");
        $this->command->info("-------------------------------------------");
        $this->command->info("Modulos activos:");
        foreach ($business->modules as $module) {
            if ($module->is_enabled) {
                $status = $module->is_enabled ? 'ON' : 'OFF';
                $this->command->info("  - {$module->moduleDefinition?->name}: {$status}");
            }
        }
        $this->command->info("-------------------------------------------");
        $this->command->info("Datos cargados:");
        $this->command->info("  - Ubicaciones: {$business->locations()->count()}");
        $this->command->info("  - Servicios: {$business->services()->count()}");
        $this->command->info("  - Productos: {$business->products()->count()}");
        $this->command->info("  - Leads: {$business->leads()->count()}");
        $this->command->info("  - Turnos: {$business->appointments()->count()}");
        $this->command->info("  - Slots: {$business->appointmentSlots()->count()}");
        $this->command->info("  - Reviews: {$business->reviews()->count()}");
        $this->command->info("===========================================");
    }
}
