<template>
  <div v-if="nonWhatsappContacts.length > 0" class="vcard__contacts">
    <a
      v-for="contact in nonWhatsappContacts"
      :key="contact.id"
      :href="getContactLink(contact)"
      class="vcard__contact"
      :class="{ 'rounded': shape === 'rounded' }"
      target="_blank"
      rel="noopener"
    >
      <span class="vcard__contact-icon" :class="{ 'rounded': shape === 'rounded' }">
        <i :class="getContactIcon(contact.type)"></i>
      </span>
      <span class="vcard__contact-value">{{ getContactDisplay(contact) }}</span>
    </a>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  contacts: {
    type: Array,
    default: () => [],
  },
  shape: {
    type: String,
    default: 'rounded',
  },
})

const nonWhatsappContacts = computed(() => {
  return props.contacts.filter(c => c.type !== 'whatsapp')
})

const contactIcons = {
  phone: 'bi-telephone',
  email: 'bi-envelope',
  whatsapp: 'bi-whatsapp',
}

function getContactIcon(type) {
  return contactIcons[type] || 'bi-telephone'
}

function getContactDisplay(contact) {
  if (contact.type === 'whatsapp' || contact.type === 'phone') {
    const prefix = contact.country_code ? `+${contact.country_code} ` : ''
    const ext = contact.extension ? ` ext. ${contact.extension}` : ''
    return prefix + contact.value + ext
  }
  return contact.value
}

function getContactLink(contact) {
  if (contact.type === 'email') {
    return `mailto:${contact.value}`
  }
  if (contact.type === 'whatsapp') {
    const phone = contact.value.replace(/\D/g, '')
    return `https://wa.me/${phone}`
  }
  if (contact.type === 'phone') {
    const phone = contact.value.replace(/\D/g, '')
    return `tel:+${contact.country_code || ''}${phone}`
  }
  return '#'
}
</script>

<style scoped>
.vcard__contacts {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.75rem;
  margin: 1.25rem 0 1.5rem;
}

.vcard__contact {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.95rem 1rem;
  background: var(--vcard-surface);
  border-radius: 0;
  text-decoration: none;
  color: var(--vcard-text);
  transition: all 0.2s;
  box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
  border: 1px solid rgba(15, 23, 42, 0.04);
  min-height: 74px;
}

.vcard__contact.rounded {
  border-radius: 14px;
}

.vcard__contact-icon {
  width: 42px;
  height: 42px;
  border-radius: 0;
  background: color-mix(in srgb, var(--vcard-primary) 14%, var(--vcard-surface));
  color: var(--vcard-primary);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex: 0 0 auto;
}

.vcard__contact-icon.rounded {
  border-radius: 12px;
}

.vcard__contact:hover {
  background: var(--vcard-primary);
  color: var(--vcard-surface);
  transform: translateY(-1px);
}

.vcard__contact:hover .vcard__contact-icon {
  background: rgba(255, 255, 255, 0.18);
  color: var(--vcard-surface);
}

.vcard__contact-value {
  font-size: 0.95rem;
  font-weight: 500;
  line-height: 1.35;
  word-break: break-word;
}

@media (max-width: 575px) {
  .vcard__contacts {
    grid-template-columns: 1fr;
  }
}
</style>
