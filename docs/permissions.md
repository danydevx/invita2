# Permisos y RBAC

## Convencion
- Formato: `modulo.accion`
- Ejemplos: `users.view`, `support.reply`, `api-keys.manage`

## Roles
- `super-admin`: acceso total
- `admin`: acceso operativo
- `member`: acceso a area member

## Spatie Permission
- Permisos definidos en `RolesAndPermissionsSeeder`
- Asignacion via roles o permisos directos

## Middleware vs Policy
- Middleware protege rutas generales
- Policy valida acceso por recurso

## Agregar permisos
1. Definir en `RolesAndPermissionsSeeder`
2. Asignar al rol correcto
3. Proteger rutas con `permission` o `permission_or_user`
4. Agregar policy si aplica
