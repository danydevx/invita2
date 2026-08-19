# Instalacion local

## Pasos
1. Configurar `.env`
2. `php artisan migrate`
3. `php artisan saas:install`
4. `php artisan saas:create-superadmin`
5. `php artisan queue:work`
6. `php artisan schedule:work`

## Correo local
Configurar `MAIL_MAILER=log` para pruebas rapidas.

## Stripe local
- Usar `STRIPE_SECRET` y `STRIPE_WEBHOOK_SECRET`
- Configurar webhook local en Stripe CLI si aplica

## Storage
Ejecutar `php artisan storage:link` si se usa storage publico.
