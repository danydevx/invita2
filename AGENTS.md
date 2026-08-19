# AGENTS.md

This is an existing Laravel SaaS. Do not rebuild authentication, roles, layout, Vite, Inertia, Bootstrap, LESS or Spatie setup.

Main task:
Integrate a modular appointment/minisite system for multiple business types.

Rules:
- All database fields must be in English.
- Respect existing roles: superadmin, admin, member, guest.
- Member can only manage owned businesses.
- Guest can only access public minisites.
- Modules must be enabled per business.
- Do not expose disabled modules.
- Do not touch SaaS core unless required.
- Use Laravel conventions: migrations, models, controllers, requests, policies.
- Use Inertia + Vue for admin/member panels.
- Use mobile-first design for public minisites.
- Use Bootstrap 5 and LESS.
- Avoid Tailwind.
- Avoid TypeScript.
- Keep code modular.

Implementation order:
1. Database migrations and models
2. Model relationships
3. Policies and permissions
4. Member CRUD modules
5. Admin/superadmin management
6. Public minisite
7. Appointment calendar
8. Media uploads
9. AI chatbot placeholder

Do not implement everything in one pass.

Before coding, inspect existing:
- routes/web.php
- app/Models/User.php
- existing admin/member controllers
- existing layouts
- existing middleware
- existing permission logic

Ask before deleting or renaming existing files.

Preferred naming:
- Business
- BusinessModule
- BusinessLocation
- BusinessGalleryImage
- BusinessProduct
- BusinessProductImage
- BusinessService
- BusinessLead
- BusinessAppointmentSlot
- BusinessAppointment

Reserved public slugs:
- admin
- member
- login
- register
- dashboard
- api
- storage
- logout
- password
- email
- sanctum

Do not run migrations automatically. Only create the files.

Important architecture update:

Use a modular Laravel structure.

Each feature must live inside its own module folder, including:
- routes
- controllers
- requests
- models
- policies
- services
- migrations
- Inertia pages
- Vue components when needed

Preferred structure:

Modules/
- Businesses
- BusinessModules
- Leads
- ContactForm
- Appointments
- Gallery
- Locations
- Products
- Services
- AiChatbot

The Businesses module is the base module.
Other modules depend on Businesses.

Do not place all controllers, models and views directly in app/ unless the project already has a module convention.

Before coding, inspect whether the project already uses a Modules/ folder or any package like nwidart/laravel-modules.
If no module system exists, propose the structure first before creating files.


enum BusinessType: string
{
    case BARBER_SHOP = 'barber_shop';
    case BEAUTY_SALON = 'beauty_salon';
    case DENTIST = 'dentist';
    case MEDICAL_CLINIC = 'medical_clinic';
    case DOCTOR = 'doctor';
    case SPA = 'spa';
    case VETERINARIAN = 'veterinarian';
    case PHYSIOTHERAPIST = 'physiotherapist';
    case PSYCHOLOGIST = 'psychologist';
    case NUTRITIONIST = 'nutritionist';
    case TATTOO_STUDIO = 'tattoo_studio';
    case GENERIC = 'generic';
}