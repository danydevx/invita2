# Arquitectura

## Estructura general
- `app/Http/Controllers/Admin`: modulos admin
- `app/Http/Controllers/Member`: modulos member
- `app/Http/Controllers/Auth`: autenticacion y flujos publicos
- `app/Services`: servicios reutilizables
- `app/Policies`: policies por recurso
- `app/Http/Middleware`: middlewares de acceso y modulos
- `app/Jobs`: ejecuciones en cola
- `app/Notifications`: notificaciones por email
- `resources/js/Layouts`: layouts admin/member

## Separacion de areas
- Admin: gestion operativa y configuracion global
- Member: uso por clientes finales
- Public/Auth: registro, login, reset y verificacion

## Servicios clave
- `SettingService`: configuracion persistente
- `FeatureService`: feature flags
- `ModuleService`: modulos activables
- `AccessService`: acceso combinando permisos + features + modulos
- `TemplateRenderService`: plantillas de texto
- `AutomationService`: automatizaciones
- `NotificationPreferenceService`: preferencias de notificacion
- `SecurityService`: eventos de seguridad

## Policies y middleware
- Policies validan acceso por recurso
- Middleware `module:clave` bloquea modulos desactivados
- Middlewares de rol y permiso via Spatie

## Inertia shared props
Se comparten para frontend:
- `auth.user`, `auth.roles`, `auth.permissions`
- `features`
- `modules`
- `systemAnnouncements`

## Modulos activables
- Tabla `system_modules`
- `ModuleService::isEnabled('clave')`
- Rutas deben usar `module:clave`
