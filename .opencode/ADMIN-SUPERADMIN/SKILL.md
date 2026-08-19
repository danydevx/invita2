# Skill: Admin and Superadmin Management

Superadmin can manage all businesses, users, modules and settings.

Admin can manage businesses depending on existing SaaS permission rules.

Required admin screens:

1. Business list
- filter by owner
- filter by business_type
- filter by active status

2. Business detail
- edit business info
- manage contact info
- manage locations
- manage services
- manage products
- manage gallery
- view leads
- view appointments

3. Module manager
Each business has toggle switches for:
- leads
- contact_form
- appointments
- gallery
- locations
- contact_info
- products
- services
- ai_chatbot

When disabled:
- member cannot access module
- public minisite does not show module
- routes should reject access

4. Ownership
Admin/superadmin can assign business owner to a member user.

Only users with member role should own customer businesses.