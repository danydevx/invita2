# Modulos disponibles

Cada modulo se controla desde `system_modules` y puede activarse/desactivarse.

## Lista base
- **users**: usuarios y cuentas
- **roles**: roles del sistema
- **permissions**: permisos del sistema
- **settings**: configuracion global
- **billing**: planes, pagos, suscripciones, cupones, facturas
- **support**: tickets y help center
- **exports**: exportaciones de datos
- **media**: archivos del usuario
- **api**: API keys
- **webhooks**: webhooks salientes
- **automations**: automatizaciones
- **legal**: documentos legales
- **notifications**: notificaciones internas
- **announcements**: anuncios del sistema
- **feature-flags**: feature flags
- **integrations**: portal de integraciones

## Dependencias
- `roles` depende de `users`
- `permissions` depende de `roles`
- `integrations` depende de `api` y `webhooks`

## Regla
- Si un modulo esta desactivado, sus rutas deben tener `module:clave`.
