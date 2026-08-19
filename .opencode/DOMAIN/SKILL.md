
# Skill: SaaS Appointments Domain

You are working inside an existing Laravel SaaS.

Stack:
- Laravel 12
- Inertia + Vue 3
- Bootstrap 5
- LESS
- Spatie Permission
- Existing roles:
  - superadmin
  - admin
  - member
  - guest

Do not rebuild the SaaS core.

Goal:
Add a multi-business appointment/minisite system.

Business types:
- barbershop
- dentist
- doctor
- beauty salon
- spa
- clinic
- generic service business

Public minisite URL format:

/{business_slug}

Example:

/barberia-demo

The member owns or manages one or more businesses.

The public guest can:
- View business minisite
- View services
- View products
- View gallery
- View contact info
- View map locations
- Send contact form
- Request or schedule appointment

All database fields must be in English.