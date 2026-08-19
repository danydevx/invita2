# Settings, Feature Flags y Modulos

## Settings (configuracion global)
Persisten valores del sistema. Se gestionan con `SettingService`.
Ejemplos: `app.name`, `billing.currency`, `mail.from_address`.

## Feature Flags
Habilitan funciones dentro de un modulo.
Ejemplos: `features.api_enabled`, `can_use_api`, `can_upload_files`.
Se consultan con `FeatureService`.

Nota: si un flag no existe o esta inactivo y la key empieza con `features.`,
`FeatureService` usa `SettingService` como fallback.

## Modulos
Habilitan o deshabilitan funcionalidades completas.
Se consultan con `ModuleService` y se aplican con `module:clave`.

## Regla practica
Acceso final = permiso/rol + feature flag + modulo activo.
