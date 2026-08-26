<template>
  <section v-if="whatsappContact" class="vcard-whatsapp">
    <a
      :href="whatsappLink"
      class="vcard-whatsapp__btn"
      :class="{ 'rounded': shape === 'rounded' }"
      target="_blank"
      rel="noopener"
    >
      <span class="vcard-whatsapp__icon" :class="{ 'rounded': shape === 'rounded' }">
        <i class="bi bi-whatsapp"></i>
      </span>
      <span class="vcard-whatsapp__content">
        <span class="vcard-whatsapp__label">WhatsApp</span>
        <span class="vcard-whatsapp__value">{{ whatsappDisplay }}</span>
      </span>
      <span class="vcard-whatsapp__arrow">
        <i class="bi bi-chevron-right"></i>
      </span>
    </a>
  </section>
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

const whatsappContact = computed(() => {
  return props.contacts.find(c => c.type === 'whatsapp')
})

const whatsappLink = computed(() => {
  if (!whatsappContact.value) return '#'
  const phone = whatsappContact.value.value.replace(/\D/g, '')
  return `https://wa.me/${phone}`
})

const whatsappDisplay = computed(() => {
  if (!whatsappContact.value) return ''
  const c = whatsappContact.value
  const prefix = c.country_code ? `+${c.country_code} ` : ''
  const ext = c.extension ? ` ext. ${c.extension}` : ''
  return prefix + c.value + ext
})
</script>

<style scoped>
.vcard-whatsapp {
  margin: 1.25rem 0;
}

.vcard-whatsapp__btn {
  display: flex;
  align-items: center;
  gap: 1rem;
  width: 100%;
  padding: 1rem 1.25rem;
  background: #25D366;
  border-radius: 0;
  text-decoration: none;
  color: #fff;
  font-size: 1rem;
  transition: all 0.2s;
  box-shadow: 0 6px 18px rgba(37, 211, 102, 0.35);
}

.vcard-whatsapp__btn.rounded {
  border-radius: 14px;
}

.vcard-whatsapp__btn:hover {
  background: #20bd5a;
  transform: translateY(-1px);
  box-shadow: 0 8px 24px rgba(37, 211, 102, 0.4);
}

.vcard-whatsapp__icon {
  width: 48px;
  height: 48px;
  border-radius: 0;
  background: rgba(255, 255, 255, 0.2);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.vcard-whatsapp__icon.rounded {
  border-radius: 12px;
}

.vcard-whatsapp__icon i {
  font-size: 1.5rem;
}

.vcard-whatsapp__content {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.vcard-whatsapp__label {
  font-size: 0.75rem;
  font-weight: 500;
  opacity: 0.9;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.vcard-whatsapp__value {
  font-size: 1.0625rem;
  font-weight: 600;
  word-break: break-word;
}

.vcard-whatsapp__arrow {
  flex-shrink: 0;
  font-size: 1.25rem;
  opacity: 0.8;
}
</style>
