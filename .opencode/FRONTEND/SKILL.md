# SKILL — Frontend UI para Directorio con Blade, BEM y LESS

## Objetivo

Este skill define las reglas obligatorias para diseñar y desarrollar la interfaz pública del directorio.

El frontend público debe construirse principalmente con:

* Laravel
* Blade
* Bootstrap 5.3
* LESS
* CSS Variables
* metodología BEM
* JavaScript vanilla cuando sea necesario

Vue **no debe utilizarse por defecto**.

Solo utilizar componentes Vue cuando se solicite explícitamente.

El objetivo es mantener:

* HTML renderizado desde servidor;
* buen SEO;
* excelente rendimiento;
* estructura semántica;
* componentes reutilizables;
* consistencia visual;
* código fácil de mantener;
* separación entre datos, estructura y presentación;
* una interfaz profesional que no parezca un template genérico ni un diseño generado automáticamente.

---

# 1. Stack obligatorio del frontend público

El frontend público del directorio utiliza:

```text
Laravel
Blade
Bootstrap 5.3
LESS
CSS Variables
BEM
JavaScript Vanilla
```

Vue solamente puede introducirse cuando exista una solicitud explícita.

No utilizar por defecto:

```text
Vue
Inertia
React
Alpine
Tailwind
TypeScript
CSS-in-JS
```

Si una funcionalidad puede resolverse correctamente con:

```text
Blade + HTML + CSS + JavaScript
```

esa debe ser la primera opción.

---

# 2. Regla principal

La arquitectura conceptual del frontend es:

```text
DATA → Laravel

PAGE → Blade Page

SECTION → Blade Partial

COMPONENT → Blade Component

SCHEME → CSS Variables

DESIGN → LESS + BEM

LAYOUT → Bootstrap + LESS

INTERACTION → JavaScript Vanilla

VUE → SOLO SI SE SOLICITA
```

Esta regla debe respetarse en cualquier nueva página o funcionalidad.

---

# 3. Blade es el sistema principal de renderizado

Todo contenido público importante debe existir directamente en el HTML generado desde Laravel.

Blade debe renderizar:

* títulos;
* textos;
* negocios;
* categorías;
* ubicaciones;
* propiedades;
* servicios;
* breadcrumbs;
* paginación;
* enlaces internos;
* información de contacto;
* metadatos importantes;
* información indexable.

No depender de JavaScript para generar contenido necesario para SEO.

Incorrecto:

```html
<div id="businesses"></div>

<script>
fetch('/api/businesses')
    .then(...)
</script>
```

Correcto:

```blade
@foreach ($businesses as $business)

    <x-business-card :business="$business" />

@endforeach
```

El navegador debe recibir el contenido principal desde el primer HTML.

---

# 4. JavaScript es mejora progresiva

JavaScript debe complementar Blade.

Puede utilizarse para:

* dropdowns;
* galerías;
* filtros interactivos;
* búsqueda autocomplete;
* modales;
* offcanvas;
* tabs;
* mapas;
* favoritos;
* controles UI;
* carga adicional opcional;
* pequeñas mejoras de UX.

La página debe seguir teniendo una estructura comprensible sin JavaScript siempre que sea razonable.

---

# 5. Uso de Vue

No introducir Vue automáticamente.

Vue solamente debe utilizarse cuando el usuario indique explícitamente algo como:

```text
haz esto con Vue
usa un componente Vue
esta sección será interactiva con Vue
quiero Vue para este buscador
```

No decidir por cuenta propia que una interfaz necesita Vue.

Si se utiliza Vue, debe integrarse como una pequeña isla interactiva dentro de una página Blade.

Ejemplo:

```blade
<section class="directory-section">

    <div class="container">

        <h2>Buscar negocios</h2>

        <div
            id="business-search"
            data-endpoint="{{ route('api.business.search') }}"
        ></div>

    </div>

</section>
```

El resto de la página continúa siendo Blade.

---

# 6. Arquitectura de vistas

Organizar las vistas aproximadamente así:

```text
resources/views/

layouts/
    app.blade.php
    directory.blade.php

pages/
    home.blade.php
    search.blade.php
    category.blade.php
    location.blade.php
    business.blade.php

sections/
    hero.blade.php
    categories.blade.php
    featured-businesses.blade.php
    locations.blade.php
    recent-businesses.blade.php
    cta.blade.php

components/
    section-header.blade.php
    business-card.blade.php
    category-card.blade.php
    location-card.blade.php
    rating.blade.php
    badge.blade.php
    breadcrumb.blade.php
    pagination.blade.php
    empty-state.blade.php
```

No colocar toda una página compleja en un único archivo Blade.

---

# 7. Jerarquía obligatoria

Cada página debe seguir aproximadamente:

```text
Layout
└── Page
    └── Section
        └── Component
```

Ejemplo:

```text
directory.blade.php

    home.blade.php

        hero.blade.php

        categories.blade.php
            category-card.blade.php

        featured-businesses.blade.php
            business-card.blade.php

        locations.blade.php
            location-card.blade.php
```

---

# 8. Pages

Una Page representa una URL pública.

Ejemplos:

```text
/
 /buscar
 /categorias/restaurantes
 /guadalajara
 /negocio/restaurante-manolos
```

Las pages deben encargarse principalmente de ordenar sections.

Ejemplo:

```blade
@extends('layouts.directory')

@section('content')

    @include('sections.hero')

    @include('sections.categories', [
        'categories' => $categories,
    ])

    @include('sections.featured-businesses', [
        'businesses' => $featuredBusinesses,
    ])

    @include('sections.locations', [
        'locations' => $locations,
    ])

@endsection
```

Evitar colocar cientos de líneas de markup dentro de la página si pueden separarse conceptualmente.

---

# 9. Sections

Toda zona visual importante debe existir como una section.

Ejemplo:

```blade
<section class="directory-section directory-section--featured">

    <div class="container">

        ...

    </div>

</section>
```

Las sections controlan:

* fondo;
* espaciado vertical;
* contexto visual;
* scheme;
* encabezado;
* organización general del contenido.

---

# 10. Estructura de una Section

Estructura recomendada:

```blade
<section class="directory-section directory-section--featured section-scheme--light">

    <div class="container">

        <header class="section-header">

            <span class="section-header__eyebrow">
                Directorio
            </span>

            <h2 class="section-header__title">
                Negocios destacados
            </h2>

            <p class="section-header__description">
                Descubre negocios recomendados.
            </p>

        </header>

        <div class="directory-section__content">

            ...

        </div>

    </div>

</section>
```

No todas las sections necesitan todos estos elementos.

---

# 11. Sections como partials

Preferir partials Blade para bloques completos de página.

Ejemplo:

```blade
@include('sections.featured-businesses', [
    'businesses' => $featuredBusinesses,
    'scheme' => 'light',
])
```

No es necesario convertir cada section en un Blade Component.

Utilizar:

```text
@include
```

cuando el bloque representa principalmente una composición de página.

---

# 12. Blade Components

Utilizar Blade Components para elementos pequeños, repetibles y autocontenidos.

Ejemplos:

```blade
<x-business-card :business="$business" />

<x-category-card :category="$category" />

<x-rating :value="$business->rating" />

<x-badge variant="featured">
    Destacado
</x-badge>
```

Los Blade Components son especialmente apropiados para:

```text
cards
badges
buttons especializados
ratings
breadcrumbs
empty states
pagination
metadata
avatares
componentes repetitivos
```

---

# 13. No convertir todo en Blade Component

Evitar abstracción excesiva.

Incorrecto:

```text
<x-row>
<x-column>
<x-wrapper>
<x-section-content>
<x-heading-wrapper>
```

si Bootstrap y HTML normal ya resuelven correctamente esa estructura.

Crear componentes cuando exista:

* reutilización real;
* identidad propia;
* comportamiento propio;
* markup repetitivo;
* suficiente complejidad.

---

# 14. BEM obligatorio

Las clases propias del proyecto deben seguir BEM.

Formato:

```text
.block
.block__element
.block--modifier
.block__element--modifier
```

Ejemplo:

```blade
<article class="business-card business-card--featured">

    <div class="business-card__media">

        <img
            src="{{ $business->image }}"
            alt="{{ $business->name }}"
            class="business-card__image"
        >

    </div>

    <div class="business-card__body">

        <h3 class="business-card__title">
            {{ $business->name }}
        </h3>

        <div class="business-card__meta">
            ...
        </div>

    </div>

</article>
```

---

# 15. Nombres de clases

Nombrar componentes por su función conceptual.

Correcto:

```text
business-card
category-card
location-card
search-form
filter-panel
section-header
directory-header
directory-footer
```

Incorrecto:

```text
home-card
blue-box
box-1
left-container
pretty-section
second-block
```

Nunca nombrar algo según su posición actual.

---

# 16. No utilizar nombres ligados a una página

Evitar:

```text
home-business-card
home-title
home-category-box
```

si el mismo elemento puede aparecer posteriormente en:

```text
search
category
location
featured
```

Preferir:

```text
business-card
section-header
category-card
```

---

# 17. Bootstrap

Bootstrap debe utilizarse principalmente para infraestructura.

Está permitido utilizar:

```text
.container
.container-fluid
.row
.col-*
.d-flex
.flex-*
.align-items-*
.justify-content-*
.d-none
.d-md-block
.g-*
```

También pueden utilizarse utilidades puntuales cuando simplifiquen el markup sin perjudicar consistencia.

---

# 18. Bootstrap no define la identidad visual

Evitar depender de una combinación excesiva de utilities para diseñar componentes.

Evitar:

```blade
<div class="card border-0 shadow-lg rounded-4 p-4 bg-light">
```

Preferir:

```blade
<article class="business-card">
```

y definir:

```less
.business-card {
    ...
}
```

Bootstrap resuelve infraestructura.

LESS + BEM resuelven diseño.

---

# 19. LESS obligatorio

Los estilos propios deben escribirse en LESS.

Estructura recomendada:

```text
resources/less/

    directory.less

    abstracts/
        variables.less
        mixins.less
        breakpoints.less

    base/
        reset.less
        typography.less
        global.less

    layout/
        header.less
        footer.less
        directory.less

    sections/
        section.less
        hero.less
        categories.less
        featured-businesses.less
        locations.less
        cta.less

    components/
        section-header.less
        business-card.less
        category-card.less
        location-card.less
        search-form.less
        filter-panel.less
        rating.less
        badge.less
        pagination.less
```

---

# 20. Entry point LESS

Mantener un archivo principal.

Ejemplo:

```less
@import 'abstracts/variables';
@import 'abstracts/mixins';
@import 'abstracts/breakpoints';

@import 'base/reset';
@import 'base/typography';
@import 'base/global';

@import 'layout/header';
@import 'layout/footer';
@import 'layout/directory';

@import 'sections/section';
@import 'sections/hero';
@import 'sections/categories';
@import 'sections/featured-businesses';
@import 'sections/locations';

@import 'components/section-header';
@import 'components/business-card';
@import 'components/category-card';
@import 'components/search-form';
@import 'components/filter-panel';
```

No importar estilos individualmente desde las vistas Blade.

---

# 21. LESS nesting

Utilizar nesting moderado.

Correcto:

```less
.business-card {

    &__media {
        position: relative;
    }

    &__image {
        width: 100%;
    }

    &__body {
        padding: 1.25rem;
    }

    &__title {
        margin-bottom: .5rem;
    }

    &--featured {
        border-color: var(--brand-primary);
    }

}
```

Evitar selectores profundamente anidados.

Incorrecto:

```less
.page {

    .section {

        .container {

            .row {

                .card {

                    .body {

                        h3 {
                        }

                    }

                }

            }

        }

    }

}
```

---

# 22. No estilizar por estructura HTML

No utilizar selectores frágiles.

Evitar:

```less
.directory-section div div h3 {
}
```

Evitar:

```less
.row > div > div > a {
}
```

Preferir:

```less
.business-card__title {
}

.business-card__link {
}
```

---

# 23. CSS Variables

Utilizar CSS Variables para valores del theme y valores que puedan cambiar según branding.

Ejemplo:

```less
:root {

    --brand-primary: #000;
    --brand-secondary: #333;

    --color-background: #fff;

    --color-surface: #fff;
    --color-surface-muted: #f6f6f6;

    --color-heading: #111;
    --color-text: #333;
    --color-text-light: #737373;

    --color-border: #e5e5e5;

}
```

No hardcodear colores repetidamente.

---

# 24. LESS vs CSS Variables

Utilizar LESS para:

```text
estructura
imports
mixins
media queries
organización
código reutilizable
```

Utilizar CSS Variables para:

```text
branding
colores
schemes
fondos
texto
bordes
radios configurables
espaciados configurables
```

---

# 25. Schemes

Las sections pueden cambiar de contexto visual mediante schemes.

Schemes iniciales:

```text
section-scheme--light
section-scheme--neutral
section-scheme--dark
section-scheme--brand
```

Ejemplo:

```blade
<section class="directory-section section-scheme--dark">
```

---

# 26. Variables de Scheme

Cada scheme debe modificar variables semánticas.

Ejemplo:

```less
.section-scheme--light {

    --section-bg: var(--color-background);
    --section-heading: var(--color-heading);
    --section-text: var(--color-text);
    --section-muted: var(--color-text-light);
    --section-link: var(--brand-primary);
    --section-border: var(--color-border);

}
```

Section:

```less
.directory-section {
    background: var(--section-bg);
    color: var(--section-text);
}
```

---

# 27. Los componentes deben respetar el contexto

Siempre que sea razonable, los componentes deben consumir variables semánticas del contexto.

Ejemplo:

```less
.section-header {

    &__title {
        color: var(--section-heading);
    }

    &__description {
        color: var(--section-muted);
    }

}
```

Evitar que una card o título tenga colores arbitrarios que rompan el scheme.

---

# 28. Section base

Crear una clase común para todas las sections.

Ejemplo:

```less
.directory-section {

    position: relative;

    padding-block: var(--section-spacing);

    background: var(--section-bg);

    color: var(--section-text);

}
```

---

# 29. Modifiers de Section

Posibles modifiers:

```text
directory-section--hero
directory-section--featured
directory-section--categories
directory-section--locations
directory-section--cta

directory-section--compact
directory-section--dense
directory-section--flush
```

Los modifiers describen función o comportamiento.

---

# 30. Espaciado

Mantener un sistema de spacing consistente.

Ejemplo:

```less
:root {

    --space-xs: .5rem;
    --space-sm: .75rem;
    --space-md: 1rem;
    --space-lg: 1.5rem;
    --space-xl: 2rem;
    --space-2xl: 3rem;

    --section-spacing: 5rem;

}
```

Responsive:

```less
@media (max-width: 767.98px) {

    :root {
        --section-spacing: 3rem;
    }

}
```

No inventar nuevos espaciados arbitrarios constantemente.

---

# 31. Section Header

Utilizar una estructura consistente para encabezados de section.

Ejemplo:

```blade
<header class="section-header">

    @if (!empty($eyebrow))

        <span class="section-header__eyebrow">
            {{ $eyebrow }}
        </span>

    @endif

    <h2 class="section-header__title">
        {{ $title }}
    </h2>

    @if (!empty($description))

        <p class="section-header__description">
            {{ $description }}
        </p>

    @endif

</header>
```

Modifiers permitidos:

```text
section-header--center
section-header--compact
section-header--inverse
```

---

# 32. Home del directorio

La Home debe funcionar principalmente como herramienta de descubrimiento.

Orden conceptual inicial:

```text
Hero / Search
↓
Categorías
↓
Negocios destacados
↓
Explorar por ubicación
↓
Negocios recientes
↓
Contenido complementario
↓
CTA
```

No considerar este orden obligatorio si el contenido del proyecto requiere otra jerarquía.

---

# 33. Hero

El Hero debe priorizar la función principal del directorio.

Normalmente:

```text
eyebrow
heading
description
search
ubicación
búsquedas frecuentes
```

La búsqueda debe tener alta jerarquía.

No crear un hero enorme únicamente decorativo.

---

# 34. Ejemplo de Hero

```blade
<section class="directory-hero section-scheme--light">

    <div class="container">

        <div class="directory-hero__content">

            <span class="directory-hero__eyebrow">
                Directorio local
            </span>

            <h1 class="directory-hero__title">
                Encuentra negocios y profesionales cerca de ti
            </h1>

            <p class="directory-hero__description">
                Busca por servicio, categoría o ubicación.
            </p>

            @include('sections.partials.search-form')

        </div>

    </div>

</section>
```

---

# 35. Formularios de búsqueda

Los formularios principales deben utilizar HTML real.

Ejemplo:

```blade
<form
    action="{{ route('directory.search') }}"
    method="GET"
    class="directory-search"
>

    <div class="directory-search__field">

        <label
            for="search-query"
            class="visually-hidden"
        >
            ¿Qué estás buscando?
        </label>

        <input
            id="search-query"
            type="search"
            name="q"
            value="{{ request('q') }}"
            class="form-control directory-search__input"
            placeholder="Dentistas, restaurantes, abogados..."
        >

    </div>

    <button
        type="submit"
        class="btn btn-primary directory-search__submit"
    >
        Buscar
    </button>

</form>
```

La búsqueda debe funcionar sin JavaScript siempre que sea posible.

Autocomplete puede añadirse posteriormente como progressive enhancement.

---

# 36. Business Card

Las cards de negocio deben priorizar información.

Orden recomendado:

```text
imagen
categoría
nombre
rating
ubicación
descripción
metadata
acción
```

No necesariamente deben contener todos esos elementos.

---

# 37. Ejemplo de Business Card

```blade
<article class="business-card">

    <a
        href="{{ route('business.show', $business) }}"
        class="business-card__media"
    >

        <img
            src="{{ $business->image_url }}"
            alt="{{ $business->name }}"
            class="business-card__image"
            loading="lazy"
        >

    </a>

    <div class="business-card__body">

        @if ($business->category)

            <span class="business-card__category">
                {{ $business->category->name }}
            </span>

        @endif

        <h3 class="business-card__title">

            <a
                href="{{ route('business.show', $business) }}"
                class="business-card__title-link"
            >
                {{ $business->name }}
            </a>

        </h3>

        @if ($business->city)

            <div class="business-card__location">
                {{ $business->city->name }}
            </div>

        @endif

    </div>

</article>
```

---

# 38. Card completa vs enlaces internos

No envolver automáticamente toda una card con:

```html
<a>
```

si contiene otros enlaces o botones internos.

Evitar HTML inválido o interacciones confusas.

Si la card tiene:

```text
teléfono
WhatsApp
guardar
categoría
perfil
```

mantener enlaces independientes.

---

# 39. Modifiers de Business Card

Utilizar modifiers cuando cambie presentación.

Ejemplo:

```text
business-card--default
business-card--compact
business-card--horizontal
business-card--featured
```

Evitar crear inmediatamente:

```text
business-card-featured.blade.php
business-card-small.blade.php
business-card-home.blade.php
```

si solamente cambia CSS.

---

# 40. Crear otra vista solo si cambia realmente el markup

Si dos diseños requieren estructuras completamente distintas, entonces sí pueden existir:

```text
components/business-card.blade.php
components/business-card-horizontal.blade.php
```

No forzar una mega-card llena de condiciones.

---

# 41. Categorías

Las categorías deben ser enlaces HTML reales.

Ejemplo:

```blade
<a
    href="{{ route('category.show', $category) }}"
    class="category-card"
>

    <span class="category-card__title">
        {{ $category->name }}
    </span>

    @if ($category->businesses_count)

        <span class="category-card__count">
            {{ $category->businesses_count }} negocios
        </span>

    @endif

</a>
```

Esto facilita:

* SEO;
* navegación;
* accesibilidad;
* crawling;
* progressive enhancement.

---

# 42. Ubicaciones

Las ubicaciones relevantes también deben generar rutas indexables.

Ejemplos:

```text
/guadalajara
/zapopan
/guadalajara/restaurantes
/zapopan/dentistas
```

Cuando corresponda al modelo SEO del proyecto.

Los links deben ser `<a href="">`.

---

# 43. Página de resultados

Debe mostrar claramente:

```text
query
ubicación
total de resultados
filtros activos
orden
resultados
paginación
```

Ejemplo conceptual:

```text
Dentistas en Guadalajara

127 resultados

[Filtros]                        [Ordenar]

--------------------------------------------

Business
Business
Business

--------------------------------------------

Pagination
```

---

# 44. Filtros

Los filtros deben funcionar inicialmente mediante GET siempre que sea viable.

Ejemplo:

```text
/buscar?q=dentista&city=guadalajara&rating=4
```

Esto permite:

* compartir URL;
* volver atrás;
* mantener estado;
* progressive enhancement;
* debugging sencillo.

JavaScript puede mejorar la interacción después.

---

# 45. Query strings

Para filtros temporales o combinaciones de búsqueda utilizar query strings.

Ejemplo:

```text
?category=dentistas
?city=guadalajara
?rating=4
?sort=rating
```

Para páginas SEO importantes considerar rutas limpias cuando corresponda.

---

# 46. Paginación

Utilizar paginación Laravel del lado del servidor.

No cargar automáticamente miles de negocios en el frontend.

Preferir:

```php
Business::query()
    ->paginate(24);
```

y Blade:

```blade
{{ $businesses->links() }}
```

Se puede personalizar la vista de paginación para adaptarla al diseño.

---

# 47. SEO

Todo contenido SEO crítico debe estar presente en el HTML inicial.

Debe ser posible inspeccionar:

```text
View Source
```

y encontrar:

```text
H1
H2
textos
negocios
categorías
links
breadcrumbs
contenido principal
```

No depender del DOM creado posteriormente por JavaScript.

---

# 48. Títulos

Cada página debe tener un único H1 principal.

Ejemplo:

```blade
<h1 class="page-header__title">
    Dentistas en Guadalajara
</h1>
```

Las sections principales utilizan normalmente:

```html
<h2>
```

Las cards:

```html
<h3>
```

según jerarquía documental real.

No elegir headings por tamaño visual.

El tamaño se controla con CSS.

---

# 49. HTML semántico

Utilizar:

```text
header
nav
main
section
article
aside
footer
address
```

cuando correspondan.

Ejemplo:

```blade
<main class="directory-main">

    <section>
        ...
    </section>

</main>
```

---

# 50. Breadcrumbs

Utilizar breadcrumbs en páginas internas relevantes.

Ejemplo:

```text
Inicio
→ Jalisco
→ Guadalajara
→ Restaurantes
→ Restaurante Manolos
```

Utilizar enlaces reales.

Considerar posteriormente structured data correspondiente.

---

# 51. URLs

Nunca utilizar JavaScript como navegación principal.

Incorrecto:

```html
<div onclick="window.location='/business/1'">
```

Correcto:

```blade
<a href="{{ route('business.show', $business) }}">
```

---

# 52. No utilizar botones para navegación

Un link navega.

Un botón ejecuta una acción.

Correcto:

```html
<a href="/restaurantes">
    Ver restaurantes
</a>
```

Correcto:

```html
<button type="submit">
    Buscar
</button>
```

Incorrecto:

```html
<button onclick="location.href='/restaurantes'">
```

---

# 53. Página de detalle

Una ficha de negocio debe dividirse en sections.

Ejemplo:

```text
Business Hero
↓
Información
↓
Servicios
↓
Galería
↓
Horario
↓
Ubicación / mapa
↓
Reviews
↓
Negocios relacionados
```

No intentar colocar toda la información en el hero.

---

# 54. Business Hero

Debe contener solamente la información de mayor jerarquía.

Ejemplo:

```text
nombre
categoría
rating
ubicación
imagen o logo
estado
CTA principal
```

El resto puede distribuirse en sections.

---

# 55. CTA

No saturar las páginas con muchos botones primarios.

Definir jerarquía.

Ejemplo:

```text
Primary:
Contactar

Secondary:
Ver teléfono

Text link:
Visitar sitio web
```

Solo debe existir un número limitado de acciones visualmente dominantes.

---

# 56. Diseño de directorio vs landing

Un directorio necesita mayor densidad de información que una landing convencional.

Evitar:

* padding gigantes;
* headlines enormes;
* bloques casi vacíos;
* demasiado espacio entre resultados;
* cards excesivamente grandes.

El usuario debe poder comparar información rápidamente.

---

# 57. Densidad

Las sections pueden disponer de modifiers:

```text
directory-section--normal
directory-section--compact
directory-section--dense
```

Usarlos con intención.

---

# 58. Tipografía

Mantener jerarquía consistente:

```text
Page title
Section title
Card title
Metadata
Supporting text
```

Los títulos de las cards nunca deben competir con el H1 o H2.

---

# 59. Imágenes

Las imágenes de contenido generado por usuarios deben normalizarse visualmente.

Ejemplo:

```less
.business-card {

    &__image {

        width: 100%;

        aspect-ratio: 4 / 3;

        object-fit: cover;

    }

}
```

No depender de dimensiones originales.

---

# 60. Lazy loading

Las imágenes que estén fuera del viewport inicial pueden utilizar:

```html
loading="lazy"
```

No aplicar lazy loading automáticamente a la imagen principal LCP del hero.

Para la imagen principal visible inmediatamente, evaluar:

```html
fetchpriority="high"
```

cuando realmente corresponda.

---

# 61. Layout Shift

Definir dimensiones o `aspect-ratio` para imágenes siempre que sea posible.

Evitar que la página cambie de tamaño conforme cargan imágenes.

---

# 62. Radius

Mantener un sistema limitado de radios.

Ejemplo:

```less
:root {

    --radius-sm: .375rem;
    --radius-md: .75rem;
    --radius-lg: 1.25rem;
    --radius-pill: 999px;

}
```

No inventar radios distintos para cada componente.

---

# 63. Shadows

Las sombras deben ser moderadas.

Preferir inicialmente:

```text
borders
surface contrast
spacing
hover
```

Ejemplo:

```less
.business-card {

    border: 1px solid var(--color-border);

    background: var(--color-surface);

}
```

Hover:

```less
.business-card {

    transition:
        transform 180ms ease,
        box-shadow 180ms ease;

    &:hover {

        transform: translateY(-2px);

        box-shadow: var(--shadow-sm);

    }

}
```

---

# 64. Animaciones

Utilizar animación principalmente para feedback.

Permitido:

```text
hover
focus
dropdown
modal
offcanvas
accordion
gallery
menu
```

Evitar animaciones constantes o decorativas.

No hacer que cada section aparezca con una animación diferente.

---

# 65. No diseñar como IA genérica

Evitar por defecto:

```text
gradientes morado/azul
glassmorphism
blobs
background grid decorativo
text gradient
iconos gigantes
pills por todos lados
cards flotando
sombras exageradas
radios gigantes
hero enorme
decoración sin función
```

Una decisión visual debe resolver una necesidad.

---

# 66. No abusar de Cards

No envolver todo en una card.

Incorrecto:

```text
Section
└── Card
    └── Card
        └── Card
```

Utilizar cards solamente cuando representen unidades de información separables.

Ejemplos apropiados:

```text
Business
Category
Promotion
Location
Review
```

---

# 67. Responsive

El diseño debe revisarse al menos en:

```text
Mobile
Tablet
Desktop
Large Desktop
```

Bootstrap no sustituye la revisión responsive.

---

# 68. Mobile-first

La estructura debe funcionar desde móvil.

En móvil priorizar:

```text
búsqueda
resultados
categorías
contacto
filtros
navegación
```

No limitarse a convertir:

```text
3 columnas → 1 columna
```

Analizar la interacción completa.

---

# 69. Filtros móviles

Cuando los filtros sean numerosos, considerar Bootstrap Offcanvas.

Ejemplo:

```text
desktop:
sidebar

mobile:
botón "Filtros"
↓
offcanvas
```

Esto puede resolverse sin Vue.

---

# 70. Desktop

Aprovechar desktop para comparación.

Ejemplo:

```text
Filters     Results

            Card Card Card
            Card Card Card
```

Para algunos casos:

```text
Results | Map
```

pero un mapa interactivo solo debe agregarse cuando sea parte de los requisitos.

---

# 71. Grids

Preferir Bootstrap Grid.

Ejemplo:

```blade
<div class="row g-4">

    @foreach ($businesses as $business)

        <div class="col-12 col-md-6 col-xl-4">

            <x-business-card
                :business="$business"
            />

        </div>

    @endforeach

</div>
```

No crear CSS Grid cuando Bootstrap ya resuelve el problema adecuadamente.

---

# 72. CSS Grid

CSS Grid puede utilizarse cuando represente claramente una mejor solución.

Ejemplos:

```text
gallery layouts
masonry-like compositions
complex detail layouts
map/result composition
```

No existe una prohibición contra CSS Grid.

Simplemente evitar duplicar funciones de Bootstrap sin necesidad.

---

# 73. Flex

Bootstrap Flex Utilities pueden utilizarse ampliamente para micro-layout.

Ejemplo:

```blade
<div class="d-flex align-items-center gap-2">
```

No es obligatorio crear clases BEM para cada distribución trivial.

---

# 74. Utilidades Bootstrap

Las utilities son apropiadas cuando expresan comportamiento genérico.

Ejemplo:

```text
d-none
d-md-flex
align-items-center
justify-content-between
g-4
```

Para identidad visual utilizar clases propias.

---

# 75. Estado vacío

Toda section basada en datos debe contemplar ausencia de registros.

Ejemplo:

```blade
@if ($businesses->isNotEmpty())

    ...

@else

    <x-empty-state
        title="No encontramos negocios"
        description="Prueba cambiando tus filtros."
    />

@endif
```

Nunca dejar una section vacía sin explicación.

---

# 76. Estados

Cuando corresponda contemplar:

```text
default
empty
error
disabled
active
loading
```

Blade debe manejar estados del servidor.

JavaScript maneja estados únicamente relacionados con interacción cliente.

---

# 77. Skeletons

No utilizar skeletons automáticamente.

Si una página está completamente renderizada por Blade, probablemente no sean necesarios.

Utilizarlos solamente en interfaces que realmente realizan consultas asíncronas.

---

# 78. Accesibilidad

Siempre considerar:

* labels;
* alt;
* focus;
* teclado;
* contraste;
* semántica;
* aria cuando sea necesario;
* tamaño de targets;
* orden de navegación.

---

# 79. Focus

Nunca eliminar simplemente:

```less
outline: none;
```

Si se personaliza el focus, proporcionar siempre:

```less
&:focus-visible {
    ...
}
```

---

# 80. Formularios

Todo input debe tener label real o accesible.

Correcto:

```blade
<label for="city">
    Ciudad
</label>

<select
    id="city"
    name="city"
    class="form-select"
>
```

Puede utilizarse:

```text
visually-hidden
```

si el diseño no requiere mostrar visualmente el label.

---

# 81. Iconos

Si se utilizan Bootstrap Icons, emplearlos como apoyo visual.

No depender solamente del icono para comunicar una acción importante.

Incorrecto:

```html
<button>
    <i class="bi bi-trash"></i>
</button>
```

sin texto accesible.

---

# 82. Datos del backend

Los Controllers o Services preparan los datos.

Blade representa esos datos.

Evitar consultas complejas directamente dentro de vistas.

Incorrecto:

```blade
@foreach (Business::where(...)->get() as $business)
```

Correcto:

```php
return view('pages.home', [
    'businesses' => $businesses,
]);
```

---

# 83. No lógica de negocio en Blade

Blade puede manejar:

```text
foreach
if
unless
isset
empty states
modifiers visuales
```

No debe contener lógica compleja de negocio.

Mover esa lógica a:

```text
Models
Services
Actions
ViewModels
Controllers
```

según corresponda.

---

# 84. View Models cuando ayuden

Si una vista comienza a contener demasiadas transformaciones, considerar preparar datos antes de Blade.

Ejemplo:

```php
$businessCard = [
    'name' => $business->name,
    'url' => route(...),
    'location' => ...,
    'image' => ...,
];
```

Pero no introducir ViewModels innecesariamente para casos simples.

---

# 85. Modules vs Views

Separar siempre:

```text
MODULE → DATA

VIEW → PRESENTATION
```

Un módulo nunca debe quedar acoplado a una sola apariencia.

Ejemplo:

```text
Businesses
```

puede alimentar:

```text
Business Grid
Featured Businesses
Nearby Businesses
Business Carousel
Search Results
Related Businesses
```

---

# 86. Una fuente de datos, varias vistas

Ejemplo conceptual:

```text
Business Module

    ↓

featured-businesses.blade.php

    ↓

business-card--featured
```

También:

```text
Business Module

    ↓

search-results.blade.php

    ↓

business-card--horizontal
```

La entidad no debe contener información específica del layout.

---

# 87. Variantes de sección

Cuando un mismo módulo tenga diferentes layouts, organizar por sección.

Ejemplo:

```text
sections/businesses/

    grid.blade.php
    featured.blade.php
    horizontal.blade.php
    compact.blade.php
```

No duplicar lógica de obtención de datos.

---

# 88. Layout Builder futuro

La arquitectura debe permitir que posteriormente una página pueda describirse mediante configuración.

Ejemplo conceptual:

```json
{
    "type": "businesses",
    "view": "featured",
    "scheme": "light"
}
```

Blade decide qué partial renderizar.

---

# 89. No hacer includes arbitrarios desde input sin validar

Nunca utilizar directamente:

```blade
@include($section->view)
```

si `$section->view` proviene de datos editables por usuario.

Utilizar un mapa controlado.

Ejemplo:

```php
$views = [
    'featured' => 'sections.businesses.featured',
    'grid' => 'sections.businesses.grid',
    'compact' => 'sections.businesses.compact',
];
```

---

# 90. Theme

El diseño debe poder cambiar valores visuales sin modificar todos los componentes.

Utilizar variables del theme para:

```text
branding
typography
background
text
links
buttons
sections
borders
radius
```

Los componentes consumen esas variables.

---

# 91. Fonts

La tipografía global debe definirse desde la capa de theme/fonts.

Evitar:

```less
.business-card {
    font-family: Arial;
}
```

si la fuente pertenece al sistema global.

---

# 92. Theme vs Component

Theme:

```text
qué color tiene primary
qué tipografía utiliza el sitio
qué radio global existe
qué color utiliza una sección dark
```

Component:

```text
cómo se estructura business-card
qué espacios internos utiliza
cómo cambia en horizontal
```

No mezclar responsabilidades.

---

# 93. CSS específico de página

Evitar estilos con IDs de página.

Ejemplo no deseado:

```less
#home .business-card {
}
```

Preferir modifiers:

```less
.business-card--featured {
}
```

o contexto semántico solo cuando realmente sea necesario.

---

# 94. Especificidad

Mantener especificidad baja.

Preferir:

```less
.business-card__title {
}
```

Evitar:

```less
body .directory-page .container .row .business-card .business-card__title {
}
```

No utilizar `!important` salvo caso excepcional y documentado.

---

# 95. Inline styles

No utilizar estilos inline.

Evitar:

```blade
<div style="background: {{ $color }}">
```

Preferir clases o variables CSS controladas.

Si existe branding dinámico, definir variables en un punto central.

---

# 96. Datos visuales dinámicos

Si colores provienen de configuración del negocio, pueden exponerse como CSS Variables.

Ejemplo conceptual:

```blade
<style>
    :root {
        --brand-primary: {{ $theme->primary }};
        --brand-secondary: {{ $theme->secondary }};
    }
</style>
```

Esto debe generarse desde una capa controlada y sanitizada.

No repartir estilos inline por toda la página.

---

# 97. Navegación

La navegación principal debe ser Blade.

El menú debe existir en el HTML.

JavaScript solamente controla:

```text
open
close
dropdown
offcanvas
```

No cargar la navegación desde API.

---

# 98. Header

El header debe tener un bloque propio.

Ejemplo:

```text
directory-header
directory-header__brand
directory-header__navigation
directory-header__actions
directory-header__toggle
```

No utilizar selectores globales como:

```less
header a {
}
```

---

# 99. Footer

El footer debe ser semántico y contener enlaces útiles.

Ejemplo:

```text
categorías principales
ubicaciones
información
contacto
legal
```

Cuando estos enlaces sean importantes para navegación y SEO, deben existir como enlaces HTML reales.

---

# 100. Performance

Evitar introducir dependencias frontend sin necesidad.

Antes de agregar una librería preguntar conceptualmente:

```text
¿puede hacerse con HTML?
¿puede hacerlo Bootstrap?
¿puede hacerse con unas líneas de JS?
```

Si la respuesta es sí, no instalar otra dependencia.

---

# 101. Dependencias JS

No instalar automáticamente:

```text
Swiper
AOS
GSAP
jQuery
Vue
Alpine
```

solo por conveniencia.

Agregar librerías únicamente cuando exista una necesidad concreta.

---

# 102. Bootstrap JavaScript

Cuando Bootstrap ya incluya comportamiento necesario, utilizarlo.

Ejemplos:

```text
Modal
Dropdown
Offcanvas
Collapse
Tabs
Toast
```

No recrearlos manualmente salvo que exista una razón.

---

# 103. AOS

No utilizar AOS automáticamente.

Las sections no necesitan aparecer animadas para verse modernas.

Si se solicita animación explícitamente, entonces evaluar la solución.

---

# 104. jQuery

No introducir jQuery en código nuevo salvo requerimiento explícito o dependencia legacy inevitable.

Preferir JavaScript moderno.

---

# 105. JavaScript modular

Organizar JS por responsabilidad.

Ejemplo:

```text
resources/js/directory/

    search.js
    filters.js
    gallery.js
    favorites.js
```

No crear un enorme:

```text
directory.js
```

con toda la lógica del sitio mezclada si comienza a crecer.

---

# 106. Progressive Enhancement

Ejemplo de búsqueda:

Sin JS:

```text
form GET
→ Laravel
→ página de resultados
```

Con JS opcional:

```text
autocomplete
suggestions
location detection
```

La base sigue funcionando.

---

# 107. URLs compartibles

Filtros relevantes deben producir estados representables mediante URL siempre que sea práctico.

Evitar que el usuario configure filtros y luego la URL siga siendo:

```text
/buscar
```

sin reflejar ningún estado.

---

# 108. Estados hover

Los elementos interactivos deben tener feedback.

Ejemplo:

```less
.category-card {

    transition:
        border-color 180ms ease,
        transform 180ms ease;

    &:hover {

        border-color: var(--brand-primary);

        transform: translateY(-2px);

    }

}
```

Mantener efectos discretos.

---

# 109. Botones

Usar Bootstrap como base cuando sea conveniente.

Ejemplo:

```blade
<a
    href="{{ route('business.contact', $business) }}"
    class="btn btn-primary"
>
    Contactar
</a>
```

Puede añadirse BEM:

```blade
class="btn btn-primary business-hero__contact"
```

No es necesario reimplementar completamente `.btn`.

---

# 110. Design tokens

Mantener tokens globales para:

```text
colors
spacing
radius
shadow
font sizes
container widths
section spacing
```

No crear valores arbitrarios sin revisar primero si existe un token.

---

# 111. Hover no debe ocultar funcionalidad

No colocar información necesaria únicamente en:

```text
:hover
```

porque no existe de la misma forma en dispositivos táctiles.

---

# 112. Contenido generado por usuarios

Prepararse para textos impredecibles.

Considerar:

```text
nombres largos
ciudades largas
categorías largas
descripciones largas
sin imagen
sin rating
sin sitio web
sin teléfono
```

El diseño no debe romperse.

---

# 113. Text wrapping

No asumir que nombres de negocios cabrán en una línea.

No utilizar:

```less
white-space: nowrap;
```

sin motivo.

Puede utilizarse truncation cuando sea una decisión deliberada.

---

# 114. Imagen fallback

Los componentes que dependen de imágenes deben contemplar ausencia de imagen.

Ejemplo:

```blade
@if ($business->image_url)

    <img ...>

@else

    <div class="business-card__placeholder">
        ...
    </div>

@endif
```

---

# 115. Empty image no es error

No mostrar iconos de imagen rota ni URLs inexistentes.

El backend debe proporcionar imagen válida o null.

---

# 116. Datos opcionales

Evitar bloques vacíos.

Ejemplo:

```blade
@if ($business->phone)

    <a href="tel:{{ $business->phone }}">
        {{ $business->phone }}
    </a>

@endif
```

---

# 117. Reutilización

Antes de crear un nuevo componente revisar:

```text
¿existe uno similar?
¿puede extenderse con modifier?
¿puede resolverse con una prop Blade?
¿puede resolverse con slot?
```

No duplicar markup.

---

# 118. No sobreabstraer

También revisar:

```text
¿esta abstracción realmente simplifica?
```

No crear un sistema genérico demasiado complejo para dos casos triviales.

Primero claridad.

Luego reutilización.

---

# 119. Diseño coherente

Todas las páginas deben compartir:

```text
header
footer
containers
spacing
typography
buttons
forms
cards
section header
schemes
radius
borders
interaction
```

Una nueva página no debe parecer otro sitio.

---

# 120. Nueva Section

Antes de crear una nueva section, determinar:

1. qué objetivo tiene;
2. qué datos utiliza;
3. si ya existe una section similar;
4. si ya existen los componentes internos;
5. qué scheme utiliza;
6. qué layout requiere;
7. qué ocurre en móvil;
8. qué ocurre sin datos;
9. qué links debe generar;
10. cuál es su jerarquía semántica.

Después escribir código.

---

# 121. Nuevo componente

Antes de crear un componente:

1. definir su responsabilidad;
2. revisar componentes existentes;
3. definir elementos BEM;
4. definir modifiers;
5. contemplar contenido largo;
6. contemplar datos faltantes;
7. revisar responsive;
8. revisar accesibilidad.

---

# 122. Nuevo LESS

Antes de agregar CSS:

1. revisar tokens existentes;
2. revisar variables del theme;
3. revisar componente existente;
4. revisar modifier existente;
5. revisar si Bootstrap ya resuelve estructura;
6. evitar selector contextual innecesario;
7. mantener BEM;
8. evitar `!important`.

---

# 123. Regla sobre diseños enviados como referencia

Si se proporciona:

* screenshot;
* mockup;
* Figma;
* imagen;
* sitio de referencia;

no copiar código ni estructura ciegamente.

Interpretar:

```text
jerarquía
proporciones
densidad
layout
tipografía
spacing
interacción
```

y adaptarlo al sistema existente.

Siempre respetar:

```text
Blade
Bootstrap
LESS
BEM
Sections
Components
Theme
```

---

# 124. No cambiar stack por conveniencia

Aunque un diseño de referencia parezca realizado con:

```text
Tailwind
React
Next.js
Webflow
```

no cambiar el stack.

Reproducir el concepto utilizando:

```text
Blade
Bootstrap 5.3
LESS
BEM
```

---

# 125. SEO no debe sacrificar UX

SSR y Blade no significan crear páginas estáticas rudimentarias.

Puede existir:

```text
autocomplete
offcanvas
modals
interactive maps
favorites
AJAX filters
infinite interactions
```

cuando realmente sean necesarias.

Pero el contenido principal debe permanecer accesible y renderizado adecuadamente.

---

# 126. SEO técnico

Al desarrollar una página pública considerar:

```text
title
meta description
canonical
robots
Open Graph
Twitter metadata
structured data
breadcrumbs
semantic headings
internal links
image alt
```

La implementación concreta puede pertenecer al módulo SEO.

No duplicar metadata manualmente en cada vista si ya existe un sistema central.

---

# 127. Structured Data

No generar Schema.org indiscriminadamente.

Agregar únicamente schemas que correspondan realmente al contenido.

Ejemplos posibles:

```text
BreadcrumbList
LocalBusiness
Organization
ItemList
WebSite
SearchAction
```

La información debe coincidir con el contenido visible.

---

# 128. Home y SEO

La Home debe comunicar claramente:

```text
qué es el directorio
qué se puede encontrar
en qué ubicación opera
categorías relevantes
negocios relevantes
```

No llenar la Home solamente con elementos visuales sin contenido textual útil.

---

# 129. Categoría SEO

Una página de categoría puede seguir:

```text
H1
introducción breve
resultados
categorías relacionadas
ubicaciones relacionadas
contenido complementario
```

No agregar párrafos artificiales exclusivamente para meter keywords.

---

# 130. Ubicación SEO

Una página de ubicación puede incluir:

```text
H1
descripción
negocios
categorías populares
zonas relacionadas
```

Siempre que esos datos sean reales y útiles.

---

# 131. Negocio SEO

Una ficha debe presentar HTML único basado en datos reales.

Evitar generar cientos de páginas con contenido prácticamente idéntico salvo el nombre del negocio.

---

# 132. Diseño visual

La interfaz debe transmitir:

```text
claridad
confianza
facilidad de búsqueda
información
utilidad
profesionalismo
```

Evitar convertir el directorio en una colección de efectos visuales.

---

# 133. Prioridad de decisiones

Cuando exista duda, utilizar este orden:

```text
1. Utilidad
2. Semántica
3. SEO
4. Accesibilidad
5. Reutilización
6. Responsive
7. Rendimiento
8. Apariencia
9. Animación
```

Una animación nunca debe complicar los primeros puntos.

---

# 134. Orden técnico para implementar una vista

Cuando se solicite una nueva interfaz:

```text
1. Analizar datos necesarios
2. Definir estructura semántica
3. Definir Page
4. Identificar Sections
5. Identificar Components
6. Escribir Blade
7. Aplicar Bootstrap para layout
8. Crear BEM
9. Escribir LESS
10. Revisar responsive
11. Revisar estados vacíos
12. Revisar SEO
13. Revisar accesibilidad
14. Agregar JS únicamente si hace falta
```

---

# 135. No inventar funcionalidad

El agente no debe agregar por cuenta propia:

```text
Vue
maps
carousels
animations
chatbots
favorites
logins
filters avanzados
infinite scroll
AJAX
```

si no forman parte del requerimiento.

Puede sugerirlos, pero no implementarlos automáticamente.

---

# 136. Vue bajo solicitud explícita

Esta regla tiene prioridad:

> No utilizar Vue en el frontend público salvo que el usuario solicite explícitamente que una funcionalidad sea desarrollada con Vue.

Incluso si Vue ya está instalado en el proyecto.

No convertir una página Blade en SPA.

No montar un componente Vue simplemente porque resulte cómodo.

---

# 137. Cuando sí se solicite Vue

Si se solicita un componente Vue:

* mantener Blade como shell;
* montar únicamente la isla necesaria;
* no duplicar contenido SEO;
* evitar que la página dependa totalmente de Vue;
* utilizar Vue 3;
* utilizar Composition API;
* preferir `<script setup>`;
* respetar Bootstrap;
* respetar las clases BEM existentes;
* respetar variables del theme.

Ejemplo:

```text
Blade Page
├── Blade Hero
├── Blade Categories
├── Vue Map Search
├── Blade Results
└── Blade Footer
```

---

# 138. Regla de modificación

Cuando se solicite modificar una pantalla existente:

no reconstruirla completamente sin necesidad.

Primero:

```text
leer estructura actual
reutilizar componentes
reutilizar sections
respetar clases
respetar theme
```

Modificar solamente lo necesario.

---

# 139. Regla de refactor

Si durante una implementación se detecta duplicación evidente, puede proponerse refactor.

Pero evitar cambiar arquitectura fuera del alcance de la solicitud si no es necesario.

---

# 140. Entrega de código

Cuando se genere código para una interfaz, indicar claramente archivos sugeridos.

Ejemplo:

```text
resources/views/pages/home.blade.php
resources/views/sections/featured-businesses.blade.php
resources/views/components/business-card.blade.php
resources/less/components/business-card.less
```

No entregar todo mezclado en un archivo ficticio salvo que se pida un prototipo.

---

# 141. Comentarios

No llenar HTML y LESS de comentarios obvios.

Evitar:

```html
<!-- Start section -->
<!-- Title -->
<!-- Card -->
```

Utilizar comentarios solamente cuando expliquen una decisión no evidente.

---

# 142. Código limpio

Preferir:

```blade
<x-business-card :business="$business" />
```

sobre cientos de líneas repetidas.

Pero también preferir HTML directo sobre abstracciones innecesarias.

Buscar equilibrio.

---

# 143. Formato

Mantener código legible.

Ejemplo:

```blade
<a
    href="{{ route('business.show', $business) }}"
    class="business-card__link"
>
    {{ $business->name }}
</a>
```

Para atributos largos, utilizar múltiples líneas.

---

# 144. Naming de archivos

Utilizar nombres descriptivos y consistentes.

Ejemplo:

```text
business-card.blade.php
featured-businesses.blade.php
category-grid.blade.php
search-form.blade.php
```

Evitar:

```text
box.blade.php
section2.blade.php
component-new.blade.php
test-card.blade.php
```

---

# 145. Principio fundamental del proyecto

Todo frontend público debe cumplir:

```text
SERVER FIRST
BLADE FIRST
SEO FIRST

SECTION BASED
COMPONENT BASED

BOOTSTRAP FOR STRUCTURE
LESS + BEM FOR DESIGN

CSS VARIABLES FOR THEMING

JAVASCRIPT ONLY WHEN NEEDED

VUE ONLY WHEN EXPLICITLY REQUESTED
```

---

# 146. Checklist antes de considerar una interfaz terminada

* [ ] El contenido principal está renderizado desde Blade.
* [ ] No depende de Vue.
* [ ] No depende innecesariamente de JavaScript.
* [ ] Existe un único H1 coherente.
* [ ] Los headings siguen una jerarquía lógica.
* [ ] Los links importantes son enlaces HTML reales.
* [ ] La página utiliza sections.
* [ ] Los componentes repetibles están separados correctamente.
* [ ] Se utiliza BEM.
* [ ] Los estilos están en LESS.
* [ ] Los colores utilizan variables del theme.
* [ ] Bootstrap se utiliza principalmente para estructura.
* [ ] No existen estilos inline innecesarios.
* [ ] No existen selectores CSS frágiles.
* [ ] No existe nesting LESS excesivo.
* [ ] No existen `!important` evitables.
* [ ] Se revisó mobile.
* [ ] Se revisó tablet.
* [ ] Se revisó desktop.
* [ ] Se contemplaron datos faltantes.
* [ ] Se contemplaron resultados vacíos.
* [ ] Las imágenes mantienen proporción.
* [ ] No se aplicó lazy loading al LCP sin analizarlo.
* [ ] Existe feedback hover/focus apropiado.
* [ ] La navegación funciona mediante enlaces reales.
* [ ] Los formularios funcionan sin JS cuando sea razonable.
* [ ] Los filtros pueden conservarse en URL cuando corresponde.
* [ ] La página tiene HTML semántico.
* [ ] Se consideró accesibilidad.
* [ ] Se consideró SEO.
* [ ] No se introdujeron dependencias innecesarias.
* [ ] No se introdujo Vue sin solicitud explícita.
* [ ] La página mantiene la identidad visual del resto del directorio.
* [ ] El diseño parece una herramienta útil, no una colección de efectos.
* [ ] La solución reutiliza el sistema existente antes de inventar uno nuevo.

---

# Regla maestra para el agente

Ante cualquier solicitud de interfaz para el frontend público del directorio:

```text
No empieces diseñando componentes aislados.

Primero entiende la página.

Después divide la página en Sections.

Después identifica los Components reutilizables.

Renderiza el contenido con Laravel + Blade.

Utiliza Bootstrap para layout y utilidades estructurales.

Utiliza LESS + BEM para la identidad visual.

Utiliza CSS Variables para theme y schemes.

Utiliza JavaScript únicamente cuando aporte interacción real.

No utilices Vue salvo que yo lo solicite explícitamente.

No cambies el stack por conveniencia.

No hagas una SPA.

No sacrifiques SEO, semántica, rendimiento o mantenibilidad por efectos visuales.
```

# Arquitectura resumida

```text
Laravel
│
├── DATA
│
├── Blade Layout
│   │
│   └── Blade Page
│       │
│       ├── Section
│       │   ├── Blade Component
│       │   ├── Blade Component
│       │   └── Blade Component
│       │
│       ├── Section
│       │   └── Blade Component
│       │
│       └── Section
│
├── Bootstrap
│   └── Layout / Grid / Utilities
│
├── LESS + BEM
│   └── Visual Design
│
├── CSS Variables
│   └── Theme / Schemes
│
└── JavaScript
    └── Progressive Enhancement

Vue
└── Únicamente bajo solicitud explícita
```

# Fórmula del proyecto

```text
MODULE
    ↓
DATA
    ↓
BLADE PAGE
    ↓
SECTION
    ↓
COMPONENT
    ↓
BEM + LESS
    ↓
THEME / SCHEME
```

El módulo define los datos.

Blade genera el HTML.

La Section define el contexto y composición.

El Component representa una unidad reutilizable.

BEM define la estructura de clases.

LESS define la interfaz.

CSS Variables definen theme y schemes.

Bootstrap resuelve la infraestructura del layout.

JavaScript mejora la interacción.

Vue es una excepción explícita, no la regla.

```
```

