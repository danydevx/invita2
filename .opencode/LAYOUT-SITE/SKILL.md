
# Skill: MiniWebs Layout Builder Architecture

## Objetivo

Diseñar un **Layout Builder desacoplado** donde los módulos únicamente administren datos y las secciones de la landing page sean responsables únicamente de la presentación.

El objetivo es que un mismo módulo pueda renderizarse mediante múltiples vistas sin duplicar lógica ni código.

Ejemplo:

El módulo **Locations** puede mostrarse como:

- Cards
- Lista
- Mapa
- Mapa + Cards
- Carousel
- Cobertura
- Timeline

Todos utilizando exactamente los mismos datos.

---

# Principios

Separar completamente estos conceptos:

```
Datos
↓
Módulo
↓
Data Provider
↓
Sección
↓
Theme
↓
Vista
```

Cada responsabilidad debe estar aislada.

---

# Arquitectura General

```
Business
│
├── Modules
│   ├── Hero
│   ├── Locations
│   ├── Services
│   ├── Products
│   ├── Gallery
│   ├── Reviews
│   ├── Promotions
│   ├── FAQ
│   └── SEO
│
└── Pages
    │
    └── Sections
        ├── Hero / Split
        ├── Services / Grid
        ├── Locations / Cards
        ├── Locations / Map
        ├── Products / Carousel
        ├── Reviews / Masonry
        └── Contact / Minimal
```

Los módulos NO conocen las vistas.

Las vistas NO conocen la base de datos.

---

# Conceptos

## Module

Es dueño de los datos.

Ejemplo:

Locations

Contiene:

- Nombre
- Dirección
- Teléfono
- Email
- Coordenadas
- Horarios

Nada relacionado con diseño.

---

## Section

Representa un bloque dentro de una página.

Ejemplo

```
Locations
```

pero únicamente indica

- qué módulo consume
- qué variante utiliza
- qué configuración posee

---

## Variant

Es la forma visual de una sección.

Ejemplo

```
Locations

Cards

List

Map

Map + Cards

Carousel

Minimal
```

Todas utilizan exactamente los mismos datos.

---

## Theme

El Theme únicamente cambia la apariencia.

No modifica:

- consultas
- lógica
- providers

Solo HTML + Blade + CSS.

---

# Flujo completo

```
Business

↓

Landing Page

↓

Section

↓

Section Data Provider

↓

Normalización

↓

Theme View

↓

HTML
```

---

# Base de Datos

## business_pages

```
id

business_id

name

slug

page_type

status

is_home

settings (json)

created_at

updated_at
```

---

## business_page_sections

```
id

business_page_id

section_type

variant

sort_order

title

subtitle

is_active

settings (json)

filters (json)

content (json)

created_at

updated_at
```

---

# section_type

Ejemplos

```
hero

about

locations

services

products

gallery

reviews

contact

social

faq

promotions

cta

custom
```

---

# variant

Ejemplos

```
cards

grid

list

carousel

map

map_cards

minimal

split

featured
```

---

# settings

Configuración visual.

Ejemplo

```json
{
    "columns": 3,
    "show_phone": true,
    "show_email": false,
    "background": "primary",
    "spacing": "large"
}
```

---

# filters

Filtros para obtener información.

```json
{
    "featured": true,
    "active": true,
    "category": 3
}
```

---

# content

Contenido propio de la sección.

```json
{
    "title": "Nuestras sucursales",
    "subtitle": "Siempre cerca de ti",
    "button_text": "Ver todas"
}
```

---

# Nunca mezclar

Incorrecto

```
Locations

↓

show_phone

show_map

columns
```

Eso pertenece a la vista.

---

Correcto

Locations únicamente almacena

```
Nombre

Dirección

Email

Teléfono

Latitud

Longitud
```

La vista decide

```
Mostrar teléfono

Mostrar email

Mostrar mapa

Mostrar horario

Columnas

Espaciado
```

---

# Data Providers

Cada módulo tendrá un proveedor.

```
LocationsSectionDataProvider

ServicesSectionDataProvider

ProductsSectionDataProvider

GallerySectionDataProvider

ReviewsSectionDataProvider
```

Cada provider tiene una única responsabilidad.

Obtener datos.

Jamás renderizar HTML.

---

Ejemplo

```
Locations Provider

↓

Obtiene ubicaciones

↓

Aplica filtros

↓

Ordena

↓

Devuelve colección
```

---

# SectionDataResolver

Responsable de decidir qué provider utilizar.

```
Section

↓

Locations

↓

LocationsProvider

↓

Resultado
```

Nunca usar múltiples IF dentro de Blade.

---

# Normalización

Todas las vistas deben recibir exactamente la misma estructura.

Ejemplo

```json
{
    "section": {
        "type": "locations",
        "variant": "cards",
        "title": "Sucursales"
    },

    "items": [

    ],

    "meta": {
        "total": 5
    }
}
```

Nunca entregar modelos Eloquent directamente a Blade.

Utilizar Resources.

---

# Resources

Ejemplo

LocationResource

Debe devolver

```json
{
    "id": 1,

    "title": "Sucursal Centro",

    "address": "...",

    "phone": "...",

    "email": "...",

    "coordinates": {
        "lat": 20.5,
        "lng": -103.3
    }
}
```

Así las vistas son independientes de la base de datos.

---

# Themes

Estructura recomendada

```
resources/views/themes/

modern/

sections/

locations/

cards.blade.php

list.blade.php

map.blade.php

map_cards.blade.php

services/

grid.blade.php

carousel.blade.php

products/

carousel.blade.php
```

---

Siempre debe existir un fallback.

```
resources/views/sections/default/
```

Si un Theme no implementa una variante, utilizar la Default.

---

# Section Catalog

Nunca hardcodear las variantes dentro de Vue.

Crear un catálogo.

Ejemplo

```
Locations

↓

Cards

↓

Map

↓

List

↓

Carousel
```

Cada variante incluye

- nombre
- preview
- icono
- configuración disponible

---

# Settings Schema

Cada variante declara los campos configurables.

Ejemplo

```
Columns

↓

Tipo

Select

↓

Valores

1

2

3

4
```

Otro

```
Show Phone

↓

Switch

↓

Default

true
```

Vue únicamente renderiza el schema.

No se crean formularios manualmente.

---

# Builder UI

Diseño recomendado

```
+-------------------------------------------------------------+

Sections

Preview

Properties

Hero

Landing Preview

Title

Services

Variant

Locations

Columns

Products

Spacing

Reviews

Show Phone

Contact

Background

+-------------------------------------------------------------+
```

---

# Repetición de Secciones

Una misma sección puede existir múltiples veces.

Ejemplo

```
Hero

Servicios

Locations (Mapa)

Productos

Locations (Lista)

Reviews

Contacto
```

Todas utilizan el mismo módulo.

---

# Selección Manual

Una sección puede mostrar únicamente ciertos registros.

Ejemplo

```
Modo

Manual

↓

Productos

4

7

12
```

O automático

```
Featured

true

↓

Category

3

↓

Limit

6
```

---

# Nunca guardar HTML

Nunca almacenar

```
html

blade

css
```

en la base de datos.

Solo guardar

```
settings

filters

content
```

---

# Section Definition

Cada sección debe implementarse mediante una clase.

Ejemplo

```
LocationsSectionDefinition
```

Responsabilidades

- nombre
- módulo asociado
- variantes disponibles
- schema
- provider

---

# Secciones con datos

```
Hero

About

Locations

Services

Products

Gallery

Reviews

Promotions

FAQ

Contact

Restaurant Menu

Appointments
```

---

# Secciones sin módulo

```
Spacer

Heading

Text

Image

Video

Divider

CTA

Custom HTML
```

Estas únicamente utilizan contenido propio.

---

# API Administración

```
GET

/api/v1/admin/businesses/{business}/pages

POST

/api/v1/admin/businesses/{business}/pages

GET

/api/v1/admin/pages/{page}

PUT

/api/v1/admin/pages/{page}

DELETE

/api/v1/admin/pages/{page}
```

---

## Secciones

```
GET

/api/v1/admin/pages/{page}/sections

POST

/api/v1/admin/pages/{page}/sections

PUT

/api/v1/admin/sections/{section}

DELETE

/api/v1/admin/sections/{section}
```

---

## Orden

```
PUT

/api/v1/admin/pages/{page}/sections/order
```

---

## Catálogo

```
GET

/api/v1/admin/section-catalog
```

---

# Preview

```
GET

/api/v1/admin/pages/{page}/preview
```

O mediante un iframe.

---

# Page Assembler

Crear un servicio central.

Responsabilidades

- obtener la página
- recorrer secciones
- llamar al provider correspondiente
- normalizar datos
- resolver la vista
- devolver estructura completa

---

Flujo

```
Page

↓

Sections

↓

Resolver

↓

Provider

↓

Resource

↓

Theme

↓

Blade
```

---

# Beneficios

## Desacoplamiento

Los módulos desconocen las vistas.

---

## Reutilización

Un mismo módulo alimenta infinitas variantes.

---

## Escalabilidad

Agregar una nueva variante no requiere modificar el módulo.

---

## Themes

Cada Theme puede implementar únicamente las vistas que necesite.

---

## Bajo mantenimiento

Toda la lógica de consultas vive en Providers.

Toda la lógica visual vive en Blade.

---

## Builder Genérico

Vue solo consume:

- catálogo
- schemas
- preview

Nunca contiene reglas específicas de negocio.

---

# Fórmula Final

```
Landing

=

Page

+

Sections

+

Section Definition

+

Data Provider

+

Resources

+

Theme

+

Blade
```

O expresado de otra forma

```
Sección

=

Fuente de Datos

+

Variante

+

Configuración

+

Theme
```

Ejemplo

```
Locations

+

Map + Cards

+

Mostrar Teléfono

+

Theme Modern
```

Todos utilizando exactamente el mismo módulo de datos.