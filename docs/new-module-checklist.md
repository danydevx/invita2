# Checklist para nuevos modulos

## Backend
- Definir rutas y usar `module:clave`.
- Crear permisos en `RolesAndPermissionsSeeder`.
- Implementar policies si hay recursos.
- Agregar validaciones de acceso en controladores.
- Registrar activity log si aplica.

## Frontend
- Crear vistas Inertia en `resources/js/Pages`.
- Usar `AdminLayout` o `MemberLayout`.
- Ocultar acciones segun permisos y modulos.

## Configuracion
- Definir `system_modules` si es un modulo nuevo.
- Agregar feature flags si hay funciones internas.

## Documentacion
- Actualizar `docs/modules.md`.
- Agregar notas en `docs/architecture.md` si aplica.
