# Despliegue basico

## Pasos sugeridos
1. `php artisan migrate --force`
2. `php artisan config:cache`
3. `php artisan route:cache`
4. `php artisan view:cache`
5. `php artisan queue:work`
6. `php artisan schedule:work`

## Storage
Ejecutar `php artisan storage:link` si se requieren archivos publicos.

## Stripe / Webhooks
Actualizar URL de webhook en Stripe y validar `STRIPE_WEBHOOK_SECRET`.

## Mail
Verificar proveedor SMTP y `mail.from`.

## Health
Endpoint de salud: `/up`.
