
# Módulo Analytics

Siguiendo estrictamente la arquitectura, convenciones, estructura de carpetas, permisos, rutas, controladores, servicios, modelos, componentes Vue, formularios y patrones utilizados por los demás módulos existentes del SaaS, crear un nuevo módulo llamado:

**Analytics**

Antes de implementar cualquier cosa, revisar módulos existentes del proyecto para reutilizar patrones y componentes.

No crear una arquitectura paralela.

El módulo debe ser completamente **multitenant**.

Toda información perteneciente a un miembro/listing debe estar relacionada mediante:

`listing_id`

Un miembro nunca debe poder consultar estadísticas, configuraciones o eventos pertenecientes a otro `listing_id`.

---

# 1. Objetivo

El módulo Analytics tendrá dos funciones principales:

1. Analytics interno propio del SaaS.
2. Integración con plataformas externas de tracking.

El sistema interno debe permitir conocer cómo se utilizan las páginas públicas generadas por el SaaS sin depender de Google Analytics u otros proveedores.

Ejemplos:

* Minisites.
* vCards.
* Propiedades.
* Invitaciones.
* Productos.
* Servicios.
* Landing pages.
* Cualquier URL pública asociada a un listing.

---

# 2. Analytics interno

Registrar automáticamente las visitas realizadas a URLs públicas pertenecientes a un `listing_id`.

Cada pageview deberá poder registrar como mínimo:

* listing_id
* session_id
* visitor_id
* URL
* path
* query string
* page title
* referrer
* timestamp
* IP
* IP hash
* country
* country_code
* region
* city
* timezone
* latitude aproximada
* longitude aproximada
* user agent
* browser
* browser version
* operating system
* device type
* language
* screen width
* screen height
* UTM source
* UTM medium
* UTM campaign
* UTM term
* UTM content

No todos los campos tienen que ser obligatorios.

El tracking nunca debe impedir que cargue la página si falla Analytics.

---

# 3. Visitante y sesión

No considerar cada request como un visitante nuevo.

Crear los conceptos:

### visitor_id

Identificador anónimo persistente del visitante.

Preferentemente generado mediante UUID y almacenado mediante cookie first-party.

No utilizar la IP como identificador principal del visitante.

### session_id

Identificador de una sesión de navegación.

Una sesión puede agrupar varios pageviews.

Definir expiración por inactividad, inicialmente:

30 minutos.

Ejemplo:

Visitor A

Session 123

* `/`
* `/servicios`
* `/servicios/desarrollo-web`
* `/contacto`

Esto representa:

1 visitante
1 sesión
4 pageviews

---

# 4. Privacidad de IP

Implementar soporte para almacenar:

* `ip_address`
* `ip_hash`

El hash debe permitir detectar aproximadamente visitantes repetidos sin necesitar conservar permanentemente la IP original.

La IP completa debe poder deshabilitarse desde configuración.

Agregar una opción:

`Store full IP addresses`

Default:

false

Cuando sea false:

* obtener IP
* utilizarla temporalmente para geolocalización si es necesario
* generar hash
* almacenar solamente el hash

No utilizar servicios externos de geolocalización en cada pageview si eso implica realizar una petición HTTP por visita.

Preparar el sistema para utilizar una base GeoIP local o un servicio abstraído mediante un Service.

---

# 5. Tabla analytics_pageviews

Crear una estructura equivalente a:

```text
analytics_pageviews

id
listing_id
visitor_id
session_id

url
path
query_string
page_title
referrer

ip_address nullable
ip_hash nullable

country nullable
country_code nullable
region nullable
city nullable
timezone nullable
latitude nullable
longitude nullable

user_agent nullable
browser nullable
browser_version nullable
os nullable
device_type nullable
language nullable

screen_width nullable
screen_height nullable

utm_source nullable
utm_medium nullable
utm_campaign nullable
utm_term nullable
utm_content nullable

created_at
```

Agregar índices adecuados para consultas frecuentes.

Especialmente:

* listing_id
* created_at
* visitor_id
* session_id
* path
* country_code
* device_type

Evaluar índices compuestos como:

```text
(listing_id, created_at)
(listing_id, path)
(listing_id, visitor_id)
```

No agregar índices innecesarios.

---

# 6. Eventos

Analytics no debe limitarse únicamente a pageviews.

Crear soporte para eventos.

Tabla:

`analytics_events`

Campos base:

```text
id
listing_id
visitor_id
session_id

event_name
event_category
url
path

metadata JSON nullable

created_at
```

Ejemplos de eventos:

```text
whatsapp_click
phone_click
email_click
contact_form_submit
appointment_click
product_click
service_click
property_click
map_click
social_click
download_vcard
download_file
gallery_open
video_play
cta_click
```

Los eventos deben poder recibir metadata adicional.

Ejemplo:

```json
{
    "event_name": "whatsapp_click",
    "metadata": {
        "location": "hero",
        "button": "Contactar por WhatsApp"
    }
}
```

---

# 7. Tracker frontend

Crear un tracker ligero y reutilizable.

No instalar librerías de analytics adicionales para el tracking interno.

Debe existir una API pública controlada para recibir:

```text
POST /analytics/collect
```

o seguir la convención equivalente existente del proyecto.

El frontend podrá enviar:

```json
{
    "type": "pageview",
    "url": "...",
    "path": "...",
    "referrer": "...",
    "screen_width": 1920,
    "screen_height": 1080
}
```

Para eventos:

```json
{
    "type": "event",
    "event_name": "whatsapp_click",
    "metadata": {
        "location": "hero"
    }
}
```

El `listing_id` NO debe confiarse ciegamente al JavaScript del visitante.

Siempre que sea posible debe resolverse desde la URL, dominio, slug, contexto público o mecanismo seguro existente en el SaaS.

---

# 8. Seguridad del endpoint

El endpoint público de Analytics debe protegerse contra abuso.

Implementar:

* validation
* rate limiting
* tamaño máximo del payload
* lista de campos permitidos
* sanitización
* protección contra metadata excesivamente grande
* validación del listing
* manejo de bots
* bloqueo de eventos inválidos

No aceptar arbitrariamente cualquier estructura JSON.

El endpoint debe responder rápidamente.

El tracking nunca debe afectar la experiencia del usuario.

---

# 9. Bots

Detectar cuando sea posible:

* crawlers
* bots
* herramientas automatizadas

Guardar:

`is_bot`

Permitir excluir bots de las estadísticas normales.

No bloquear Googlebot u otros crawlers legítimos simplemente por utilizar Analytics.

Analytics solamente debe decidir si contabiliza o clasifica la visita.

---

# 10. Integraciones externas

Crear configuración por `listing_id` para herramientas externas.

Inicialmente soportar:

### Google

* Google Analytics 4
* Google Tag Manager

Campos:

```text
GA4 Measurement ID
GTM Container ID
```

### Meta

* Meta Pixel ID

### TikTok

* TikTok Pixel ID

### Microsoft

* Microsoft Clarity Project ID

### Google Ads

* Conversion ID
* Conversion Label

Agregar también:

### Custom scripts

Permitir scripts personalizados únicamente si el rol/permisos del sistema lo permite.

Separar:

* Head scripts
* Body start scripts
* Body end scripts

Esta función debe tratarse como avanzada porque permite insertar JavaScript.

No permitir que un miembro pueda afectar páginas pertenecientes a otro listing.

---

# 11. Configuración

Crear una pestaña:

## General

Campos:

```text
Enable Internal Analytics
Track Pageviews
Track Events
Track Referrers
Track UTM Parameters
Track Device Information
Track Approximate Location
Store Full IP Addresses
Exclude Bots
Session Timeout
Data Retention
```

Defaults sugeridos:

```text
Internal Analytics: true
Pageviews: true
Events: true
Referrers: true
UTM: true
Device Information: true
Approximate Location: true
Store Full IP: false
Exclude Bots: true
Session Timeout: 30 minutes
Data Retention: 12 months
```

---

# 12. Integrations

Crear otra pestaña:

## Integrations

Campos:

```text
Google Analytics Measurement ID
Google Tag Manager ID
Meta Pixel ID
TikTok Pixel ID
Microsoft Clarity ID
Google Ads Conversion ID
Google Ads Conversion Label
```

Cada integración debe tener:

```text
Enabled
ID/configuration
```

No cargar scripts externos cuando la integración esté desactivada.

---

# 13. Dashboard Analytics

Crear una vista principal del módulo.

El usuario debe poder seleccionar:

```text
Today
Yesterday
Last 7 days
Last 30 days
This month
Previous month
Custom range
```

Mostrar KPIs principales:

```text
Pageviews
Unique Visitors
Sessions
Events
Conversions
Average Pages per Session
```

Comparar, cuando sea posible, contra el periodo anterior.

Ejemplo:

```text
Pageviews
2,430
+12.5%
```

---

# 14. Gráfica principal

Mostrar gráfica temporal con:

* Pageviews
* Visitors

Agrupar automáticamente dependiendo del rango:

* hora
* día
* semana
* mes

No consultar todos los registros y procesarlos posteriormente en Vue.

Las agregaciones deben realizarse desde Backend/SQL.

---

# 15. Top Pages

Mostrar:

```text
Page
Views
Visitors
```

Ejemplo:

```text
/                         1,250    870
/servicios                  640    420
/contacto                   320    270
/vcard/juan-perez           210    180
```

Permitir ordenar.

---

# 16. Traffic Sources

Mostrar principales referrers.

Ejemplo:

```text
Direct
Google
Facebook
Instagram
LinkedIn
Bing
Other
```

También mostrar campañas UTM:

```text
Source
Medium
Campaign
Visitors
Sessions
Conversions
```

---

# 17. Ubicación

Mostrar estadísticas por:

```text
Country
Region
City
```

No mostrar la ubicación como si fuera una posición GPS exacta.

La geolocalización basada en IP debe considerarse aproximada.

---

# 18. Tecnología

Mostrar:

### Devices

```text
Desktop
Mobile
Tablet
Other
```

### Browsers

```text
Chrome
Safari
Firefox
Edge
Other
```

### Operating Systems

```text
Windows
macOS
Linux
Android
iOS
Other
```

---

# 19. Events

Crear sección de eventos.

Mostrar:

```text
Event
Total
Unique Visitors
Conversion Rate
```

Ejemplo:

```text
whatsapp_click       340
phone_click           82
contact_form_submit   46
download_vcard       120
```

---

# 20. Conversiones

Preparar Analytics para definir determinados eventos como conversiones.

Ejemplo:

```text
contact_form_submit
appointment_created
whatsapp_click
phone_click
purchase
lead_created
```

No es necesario desarrollar inicialmente un sistema complejo de funnels, pero la arquitectura debe permitir incorporarlo posteriormente.

---

# 21. API / Backend

Separar responsabilidades.

Ejemplo conceptual:

```text
AnalyticsController
AnalyticsCollectController
AnalyticsDashboardController

AnalyticsService
AnalyticsTrackingService
AnalyticsQueryService
AnalyticsGeoService
AnalyticsDeviceService
```

Adaptar los nombres a las convenciones reales del proyecto.

No colocar toda la lógica en Controllers.

---

# 22. Rendimiento

Analytics puede convertirse en una tabla muy grande.

Diseñar desde el principio considerando cientos de miles o millones de registros.

Evitar:

```php
AnalyticsPageview::all();
```

Evitar cargar miles de pageviews para posteriormente contarlos en PHP.

Utilizar:

```text
COUNT
COUNT DISTINCT
GROUP BY
SUM
DATE grouping
indexes
```

directamente desde SQL/Eloquent Query Builder.

Paginar cualquier tabla de resultados.

---

# 23. Agregaciones

Preparar arquitectura para que posteriormente puedan existir tablas agregadas.

Ejemplo:

```text
analytics_daily_stats
```

Con:

```text
listing_id
date
pageviews
visitors
sessions
events
conversions
```

No es obligatorio utilizarlas inmediatamente si el volumen actual no lo requiere.

La arquitectura no debe impedir incorporarlas posteriormente mediante Jobs/Scheduler.

---

# 24. Retención

Implementar configuración de retención.

Ejemplo:

```text
3 months
6 months
12 months
24 months
Unlimited
```

Crear Command/Job preparado para eliminar datos antiguos respetando la configuración del listing.

Nunca eliminar información perteneciente a otros listings accidentalmente.

---

# 25. Integración con módulos existentes

Analytics debe ser reutilizable desde otros módulos.

Ejemplo:

```text
Analytics.track('property_click')
Analytics.track('product_click')
Analytics.track('service_click')
Analytics.track('whatsapp_click')
```

En frontend crear una API equivalente y sencilla.

La implementación concreta debe respetar la arquitectura actual.

No duplicar trackers diferentes para:

* vCards
* properties
* products
* minisites
* invites

Todos deben utilizar el mismo sistema Analytics.

---

# 26. Privacidad

Diseñar el módulo considerando que:

* IP puede considerarse dato personal.
* GeoIP es aproximado.
* No necesitamos fingerprinting invasivo.
* No debemos utilizar canvas fingerprinting.
* No debemos crear identificadores ocultos innecesarios.

Utilizar preferentemente:

* cookies first-party
* UUID anónimo
* session identifiers
* IP hashing

Preparar el tracker para poder respetar consentimiento de cookies cuando el SaaS implemente o tenga disponible un módulo de consentimiento.

Las integraciones externas deben poder cargarse únicamente cuando corresponda según la configuración de consentimiento.

---

# 27. Permisos

Agregar permisos siguiendo el sistema existente.

Ejemplo:

```text
analytics.view
analytics.settings
analytics.integrations
analytics.events
analytics.export
```

Admin/Superadmin podrá consultar información global cuando su rol lo permita.

Member solamente podrá consultar Analytics de sus propios listings.

---

# 28. Admin Analytics

Además del dashboard del miembro, preparar un dashboard administrativo global.

El administrador podrá conocer:

```text
Total pageviews
Total visitors
Total sessions
Active listings
Top listings
Top URLs
Traffic by country
Traffic by device
Events generated
```

Permitir filtrar por listing.

Este dashboard NO debe compartir las mismas restricciones de member cuando el usuario tenga permisos administrativos globales.

---

# 29. No hacer

No instalar herramientas externas sin autorización.

No utilizar Tailwind.

No utilizar React.

No introducir TypeScript.

No crear estilos inline innecesarios.

No crear una arquitectura distinta a los demás módulos.

No almacenar información de Analytics sin `listing_id`.

No confiar en IDs enviados desde el navegador sin validarlos.

No utilizar IP como único mecanismo para identificar usuarios.

No realizar una petición a una API GeoIP externa en cada pageview.

No hacer queries sin índices sobre tablas grandes.

No cargar todos los registros para generar estadísticas.

---

# 30. Stack

Mantener el stack existente del SaaS:

* Laravel
* Vue 3
* Inertia
* Bootstrap 5
* LESS
* Composition API
* MySQL

Utilizar los componentes globales existentes antes de crear componentes nuevos.

Los estilos deben seguir las convenciones LESS/BEM actuales del proyecto.

---

# 31. Implementación por fases

No implementar todo de golpe.

## Fase 1

Implementar:

* instalación del módulo
* migrations
* configuración
* tracker interno
* pageviews
* visitors
* sessions
* IP hash
* GeoIP abstraction
* device detection
* UTM
* referrer
* eventos
* dashboard básico
* top URLs

Verificar funcionamiento antes de continuar.

## Fase 2

Implementar:

* GA4
* GTM
* Meta Pixel
* TikTok Pixel
* Microsoft Clarity
* Google Ads

## Fase 3

Implementar:

* conversiones
* estadísticas avanzadas
* campañas
* admin global analytics
* retención automática
* agregaciones diarias

## Fase 4

Dejar preparada la arquitectura para:

* funnels
* realtime visitors
* goals
* custom events
* exports CSV
* reportes
* comparaciones
* analytics por módulo
* analytics por entidad

---

# Resultado esperado

Al finalizar la primera fase debe ser posible que un miembro entre a:

`Analytics`

y pueda saber como mínimo:

* cuántas veces se ha visto su sitio
* cuántas personas aproximadamente lo visitaron
* cuántas sesiones existieron
* qué URLs fueron las más visitadas
* desde dónde llegaron
* desde qué países/ciudades
* qué dispositivos utilizaron
* qué CTAs fueron utilizados
* cuántos clics recibió WhatsApp
* cuántos formularios fueron enviados

Todo sin necesidad de configurar Google Analytics.

Las herramientas externas serán complementarias al Analytics propio del SaaS, no un reemplazo.
