02-database.md
# Skill: Database Design for Appointment SaaS

Create migrations and models for these entities.

Main tables:

businesses
- id
- owner_id foreignId users
- name string required
- slug string unique required
- business_type string nullable
- description text nullable
- email string nullable
- phone string nullable
- whatsapp string nullable
- website_url string nullable
- facebook_url string nullable
- instagram_url string nullable
- youtube_url string nullable
- x_url string nullable
- tiktok_url string nullable
- mercadolibre_url string nullable
- is_active boolean default true
- created_at
- updated_at

business_modules
- id
- business_id
- module_key string
- is_enabled boolean default false
- created_at
- updated_at

Allowed module keys:
- leads
- contact_form
- appointments
- gallery
- locations
- contact_info
- products
- services
- ai_chatbot

business_locations
- id
- business_id
- name string nullable
- country string nullable
- state string nullable
- city string nullable
- municipality string nullable
- postal_code string nullable
- address text nullable
- latitude decimal nullable
- longitude decimal nullable
- is_main boolean default false
- is_active boolean default true
- created_at
- updated_at

business_gallery_images
- id
- business_id
- image_path string required
- sort_order unsignedInteger default 0
- is_active boolean default true
- created_at
- updated_at

business_products
- id
- business_id
- title string required
- description text nullable
- price decimal nullable
- main_image_path string nullable
- is_available boolean default true
- created_at
- updated_at

business_product_images
- id
- product_id
- image_path string required
- sort_order unsignedInteger default 0
- created_at
- updated_at

business_services
- id
- business_id
- title string required
- description text nullable
- price decimal nullable
- image_path string nullable
- is_available boolean default true
- created_at
- updated_at

business_leads
- id
- business_id
- name string required
- email string required
- subject string nullable
- comments text nullable
- source string default contact_form
- status string default new
- created_at
- updated_at

business_appointment_slots
- id
- business_id
- location_id nullable
- starts_at datetime required
- ends_at datetime required
- status string default open
- notes text nullable
- created_at
- updated_at

Allowed slot status:
- open
- closed
- booked

business_appointments
- id
- business_id
- location_id nullable
- service_id nullable
- appointment_slot_id nullable
- customer_name string required
- customer_email string nullable
- customer_phone string nullable
- customer_whatsapp string nullable
- starts_at datetime required
- ends_at datetime required
- status string default pending
- notes text nullable
- created_at
- updated_at

Allowed appointment status:
- pending
- confirmed
- cancelled
- completed

Use policies or gates so members can only manage their own businesses.