<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Widget - Test</title>
    @vite(['resources/js/booking-widget.js'])
    <style>
        body {
            background: #f6f8fa;
            min-height: 100vh;
            padding: 2rem 0;
        }
        .dev-test-page {
            max-width: 800px;
            margin: 0 auto;
        }
        .dev-test-page h1 {
            color: #0d6efd;
            font-size: 1.75rem;
        }
    </style>
</head>
<body>
    <div class="dev-test-page">
        <h1 class="mb-4">Booking Widget Test</h1>
        <p class="text-muted mb-4">
            Página de prueba del componente público de reservas.
            Seleccioná un negocio activo, servicio, fecha y horario para crear una cita.
        </p>

        <div data-booking-widget data-api-base="/api/book"></div>

        <hr class="my-5">

        <div class="alert alert-info small">
            <strong>Endpoints consumidos:</strong>
            <ul class="mb-0 mt-2">
                <li><code>GET /api/book/businesses/active</code> - Lista negocios activos</li>
                <li><code>GET /api/book/business/{slug}/services</code> - Servicios y ubicaciones</li>
                <li><code>GET /api/book/business/{slug}/slots?date=X&service_id=Y</code> - Horarios disponibles</li>
                <li><code>POST /api/book/business/{slug}</code> - Crear cita</li>
            </ul>
        </div>
    </div>
</body>
</html>