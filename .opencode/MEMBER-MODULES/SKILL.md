# Skill: Member Business Modules

Create member area modules using Inertia + Vue.

Member can manage only businesses where businesses.owner_id equals auth user id.

Member routes should be grouped under:

/member/businesses

Required screens:

1. Business profile
- name
- slug
- business_type
- description
- contact fields
- social links

2. Leads
- list leads
- view lead
- update status

3. Contact form settings
- enabled only if contact_form module is active

4. Calendar / appointment slots
- open slots
- close slots
- edit slots
- mark unavailable blocks
- Google Calendar-like UI later, simple CRUD first

5. Gallery
- max 20 images per business
- jpg only
- max 2MB each

6. Locations
- multiple locations
- map picker fields:
  - latitude
  - longitude
- address fields:
  - country
  - state
  - city
  - municipality
  - postal_code
  - address

7. Products
- title required
- description
- price
- available yes/no
- main image
- max 5 extra images

8. Services
- title required
- description
- price
- available yes/no
- image

9. AI chatbot
- create placeholder module
- do not implement AI logic yet
- prepare table/model later for training content

Every module must check business_modules.is_enabled before showing UI.
04-minisite-public.md
# Skill: Public Minisite

Create public minisite routes:

GET /{business:slug}

The minisite must be available to guest users.

Public page sections depend on enabled modules:

- contact_info
- services
- products
- gallery
- locations
- contact_form
- appointments
- ai_chatbot

If a module is disabled, do not render that section.

The public minisite should use a mobile-first layout.

Use Blade or Inertia depending on the existing SaaS architecture, but do not break current SaaS routes.

Important:
Reserved routes must not be captured by business slug.

Reserved slugs:
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

Validate business slug so it does not collide with reserved routes.

Contact form:
POST /{business:slug}/contact

Fields:
- name required
- email required
- subject nullable
- comments nullable

Store submissions in business_leads.

Appointments:
Public users can request or book appointment slots only if appointments module is enabled.