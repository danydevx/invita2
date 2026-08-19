<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Businesses\Models\Business;
use Modules\Leads\Models\BusinessLead;
use Modules\Leads\Enums\LeadStatus;
use Modules\Leads\Enums\LeadSource;

class ContactFormSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::find(2);

        if (!$business) {
            $this->command->error('Business with ID 2 (Lavanderia Manolos) not found.');
            return;
        }

        $names = [
            'Juan Perez', 'Maria Garcia', 'Carlos Rodriguez', 'Ana Martinez',
            'Luis Lopez', 'Carmen Sanchez', 'Antonio Fernandez', 'Laura Diaz',
            'Pedro Moreno', 'Isabel Romero', 'Jorge Ruiz', 'Sofia Torres',
            'Miguel Aguilar', 'Paula Vargas', 'Diego Castro', 'Lucía Herrera',
            'Francisco Ortiz', 'Elena Ramos', 'Alejandro Guzman', 'Claudia Mendoza'
        ];

        $emails = [
            'juan.perez@mail.com', 'maria.garcia@mail.com', 'carlos.rodriguez@mail.com', 'ana.martinez@mail.com',
            'luis.lopez@mail.com', 'carmen.sanchez@mail.com', 'antonio.fernandez@mail.com', 'laura.diaz@mail.com',
            'pedro.moreno@mail.com', 'isabel.romero@mail.com', 'jorge.ruiz@mail.com', 'sofia.torres@mail.com',
            'miguel.aguilar@mail.com', 'paula.vargas@mail.com', 'diego.castro@mail.com', 'lucia.herrera@mail.com',
            'francisco.ortiz@mail.com', 'elena.ramos@mail.com', 'alejandro.guzman@mail.com', 'claudia.mendoza@mail.com'
        ];

        $phones = [
            '+54 11 1234-5678', '+54 11 2345-6789', '+54 11 3456-7890', '+54 11 4567-8901',
            '+54 11 5678-9012', '+54 11 6789-0123', '+54 11 7890-1234', '+54 11 8901-2345',
            '+54 11 9012-3456', '+54 11 0123-4567', '+54 11 1111-2222', '+54 11 2222-3333',
            '+54 11 3333-4444', '+54 11 4444-5555', '+54 11 5555-6666', '+54 11 6666-7777',
            '+54 11 7777-8888', '+54 11 8888-9999', '+54 11 9999-0000', '+54 11 0000-1111'
        ];

        $notes = [
            'Me interesa saber mas sobre sus servicios de corte.',
            'Necesito informacion sobre precios y disponibilidad.',
            'Quiero reservar un turno para el sabado.',
            'Consulta por servicios de barba.',
            'Pregunta sobre promociones actuales.',
            'Me gustaria conocer sus instalaciones.',
            'Solicito informacion para evento corporativo.',
            'Consulta por servicios a domicilio.',
            'Necesito corte para boda.',
            'Pregunta por servicio de tintura.',
            'Quiero saber si abren los domingos.',
            'Consulta por membresias mensuales.',
            'Me interesa un paquete para novios.',
            'Solicito presupuesto para 10 personas.',
            'Pregunta sobre garantia de servicio.',
            'Quiero agendar una visita de prueba.',
            'Consulta por metodos de pago.',
            'Me gustaria conocer al equipo de barberos.',
            'Solicito informacion sobre cursos de barberia.',
            'Pregunta por servicio express.'
        ];

        $statuses = [
            LeadStatus::NEW,
            LeadStatus::NEW,
            LeadStatus::CONTACTED,
            LeadStatus::QUALIFIED,
            LeadStatus::NEW,
            LeadStatus::CONTACTED,
            LeadStatus::QUALIFIED,
            LeadStatus::NEW,
            LeadStatus::CONTACTED,
            LeadStatus::NEW,
            LeadStatus::QUALIFIED,
            LeadStatus::CONTACTED,
            LeadStatus::NEW,
            LeadStatus::QUALIFIED,
            LeadStatus::NEW,
            LeadStatus::CONTACTED,
            LeadStatus::QUALIFIED,
            LeadStatus::NEW,
            LeadStatus::CONTACTED,
            LeadStatus::NEW,
        ];

        for ($i = 0; $i < 20; $i++) {
            $createdAt = now()->subDays(rand(1, 60))->subHours(rand(0, 23));

            BusinessLead::updateOrCreate(
                [
                    'business_id' => $business->id,
                    'email' => $emails[$i],
                ],
                [
                    'name' => $names[$i],
                    'phone' => $phones[$i],
                    'notes' => $notes[$i],
                    'source' => LeadSource::WEBSITE,
                    'status' => $statuses[$i],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );
        }

        $this->command->info('Created 20 contact form submissions for business: ' . $business->name);
    }
}
