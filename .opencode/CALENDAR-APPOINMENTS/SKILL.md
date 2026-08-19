
# Skill: Calendar and Appointment Slots

Implement appointment availability using slots.

Slot rules:
- A slot belongs to a business.
- A slot may belong to a location.
- A slot has starts_at and ends_at.
- Status can be open, closed or booked.

Member can:
- create open slots
- close slots
- delete future open slots
- see booked slots
- block unavailable hours

Guest can:
- see available open slots
- request or book appointment
- enter:
  - customer_name required
  - customer_email nullable
  - customer_phone nullable
  - customer_whatsapp nullable
  - notes nullable
  - service_id nullable

When appointment is created:
- create business_appointment
- mark slot as booked if appointment_slot_id exists

Do not implement payment yet.

Use timezone-aware datetime handling.
Default timezone can follow Laravel app timezone.