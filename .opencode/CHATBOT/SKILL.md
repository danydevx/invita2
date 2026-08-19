
# Skill: AI Chatbot Future Module

Prepare the SaaS for a future AI chatbot module.

Do not implement AI completions yet.

Create module key:
- ai_chatbot

Later the member will train the chatbot with:
- business description
- services
- products
- FAQs
- custom instructions
- opening hours
- locations
- appointment rules

Optional future tables:

business_chatbot_settings
- id
- business_id
- assistant_name string nullable
- welcome_message text nullable
- instructions longText nullable
- is_enabled boolean default false
- created_at
- updated_at

business_chatbot_training_items
- id
- business_id
- title string required
- content longText required
- type string default custom
- is_active boolean default true
- created_at
- updated_at

The public minisite should only render chatbot placeholder if:
- ai_chatbot module is enabled
- chatbot settings are enabled