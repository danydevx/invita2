<template>
  <div v-if="activeFields && activeFields.length > 0" class="vcard__fields">
    <template v-for="field in activeFields" :key="field.id">
      <a
        v-if="getActionUrl(field)"
        :href="getActionUrl(field)"
        class="vcard__field"
        :class="{ 'rounded': shape === 'rounded' }"
        target="_blank"
        rel="noopener nofollow"
      >
        <i :class="getFieldIcon(field.field_type_key)"></i>
        <span>{{ field.label || getFieldDisplayValue(field) }}</span>
      </a>
      <div v-else class="vcard__field vcard__field--static" :class="{ 'rounded': shape === 'rounded' }">
        <i :class="getFieldIcon(field.field_type_key)"></i>
        <span>{{ field.label || getFieldDisplayValue(field) }}</span>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  fields: {
    type: Array,
    default: () => [],
  },
  shape: {
    type: String,
    default: 'rounded',
  },
})

const fieldIcons = {
  website: 'bi-globe',
  link: 'bi-link',
  email: 'bi-envelope',
  phone: 'bi-telephone',
  whatsapp: 'bi-whatsapp',
  instagram: 'bi-instagram',
  facebook: 'bi-facebook',
  linkedin: 'bi-linkedin',
  twitter: 'bi-twitter-x',
  youtube: 'bi-youtube',
  tiktok: 'bi-tiktok',
  spotify: 'bi-spotify',
  github: 'bi-github',
  telegram: 'bi-telegram',
  discord: 'bi-discord',
  paypal: 'bi-paypal',
  venmo: 'bi-credit-card',
  pdf: 'bi-file-pdf',
  address: 'bi-geo-alt',
  note: 'bi-stickies',
}

const activeFields = computed(() => {
  return (props.fields || []).filter(f => f.active !== false)
})

function getFieldIcon(fieldTypeKey) {
  return fieldIcons[fieldTypeKey] || 'bi-link'
}

function getFieldDisplayValue(field) {
  const config = field.config || {}
  switch (field.field_type_key) {
    case 'website':
    case 'link':
      return config.url || ''
    case 'instagram':
    case 'twitter':
    case 'facebook':
    case 'linkedin':
    case 'tiktok':
    case 'github':
    case 'telegram':
      return config.username || ''
    case 'phone':
    case 'whatsapp':
      return config.phone || ''
    case 'email':
      return config.email || ''
    case 'youtube':
      return config.url || ''
    case 'spotify':
      return config.url || ''
    case 'paypal':
      return config.url || ''
    case 'venmo':
      return config.username || ''
    case 'pdf':
      return config.label || 'PDF'
    case 'address':
      return formatAddress(config)
    case 'note':
      return config.text || ''
    default:
      return ''
  }
}

function formatAddress(config) {
  const parts = [
    config.street,
    config.city,
    config.state,
    config.postal_code,
    config.country,
  ].filter(Boolean)
  return parts.join(', ')
}

function getActionUrl(field) {
  const config = field.config || {}
  switch (field.field_type_key) {
    case 'website':
    case 'link':
      return config.url || null
    case 'instagram':
      return config.username ? `https://instagram.com/${config.username}` : null
    case 'twitter':
      return config.username ? `https://x.com/${config.username}` : null
    case 'facebook':
      return config.username ? `https://facebook.com/${config.username}` : null
    case 'linkedin':
      return config.username ? `https://linkedin.com/in/${config.username}` : null
    case 'tiktok':
      return config.username ? `https://tiktok.com/@${config.username}` : null
    case 'youtube':
      return config.url || null
    case 'spotify':
      return config.url || null
    case 'github':
      return config.username ? `https://github.com/${config.username}` : null
    case 'telegram':
      return config.username ? `https://t.me/${config.username}` : null
    case 'discord':
      return config.invite_url || null
    case 'paypal':
      return config.url || null
    case 'phone':
      return config.phone ? `tel:${config.phone}` : null
    case 'whatsapp':
      return config.phone ? `https://wa.me/${config.phone.replace(/\D/g, '')}` : null
    case 'email':
      return config.email ? `mailto:${config.email}` : null
    case 'pdf':
      return config.file ? `/storage/${config.file}` : null
    default:
      return null
  }
}
</script>

<style scoped>
.vcard__fields {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.vcard__field {
  display: flex;
  align-items: center;
  padding: 0.625rem 1rem;
  background: var(--vcard-surface-2);
  border-radius: 0;
  text-decoration: none;
  color: var(--vcard-text);
  font-size: 0.9375rem;
  transition: all 0.2s;
}

.vcard__field.rounded {
  border-radius: 8px;
}

.vcard__field i {
  font-size: 1rem;
  margin-right: 0.75rem;
  color: var(--vcard-primary);
}

.vcard__field:hover {
  background: var(--vcard-primary);
  color: var(--vcard-surface);
}

.vcard__field:hover i {
  color: var(--vcard-surface);
}

.vcard__field--static {
  cursor: default;
}

.vcard__field--static:hover {
  background: var(--vcard-surface-2);
  color: var(--vcard-text);
}
</style>
