<template>
  <div class="vcard__header text-center">
    <h1 class="vcard__name">{{ displayName }}</h1>
    <p v-if="vcard.accreditations" class="vcard__accreditations">{{ vcard.accreditations }}</p>
    <p v-if="displayTitle" class="vcard__title">{{ displayTitle }}</p>
    <p v-if="vcard.headline" class="vcard__headline">{{ vcard.headline }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  vcard: {
    type: Object,
    required: true,
  },
})

const displayName = computed(() => {
  const parts = []
  if (props.vcard.prefix) parts.push(props.vcard.prefix)
  if (props.vcard.first_name) parts.push(props.vcard.first_name)
  if (props.vcard.middle_name) parts.push(props.vcard.middle_name)
  if (props.vcard.last_name) parts.push(props.vcard.last_name)
  return parts.length > 0 ? parts.join(' ') : props.vcard.name || ''
})

const displayTitle = computed(() => {
  const parts = []
  if (props.vcard.title) parts.push(props.vcard.title)
  if (props.vcard.department) parts.push(props.vcard.department)
  if (props.vcard.company) parts.push('@ ' + props.vcard.company)
  return parts.join(' - ')
})
</script>

<style scoped>
.vcard__header {
  margin-bottom: 1.5rem;
}

.vcard__name {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--vcard-text);
  margin: 0;
}

.vcard__accreditations {
  font-size: 0.875rem;
  color: var(--vcard-muted);
  margin: 0.25rem 0 0;
}

.vcard__title {
  font-size: 1rem;
  color: var(--vcard-primary);
  font-weight: 500;
  margin: 0.5rem 0 0;
}

.vcard__headline {
  font-size: 0.875rem;
  color: var(--vcard-muted);
  margin: 0.5rem 0 0;
  line-height: 1.5;
}
</style>
