<template>
  <section class="vcard-identity card border-0 shadow-sm" :class="{ 'rounded': shape === 'rounded' }">
    <div class="card-body text-center">
      <div v-if="hasProfessionalInfo" class="vcard-identity__professional">
        <p v-if="titleLine" class="vcard-identity__title">{{ titleLine }}</p>
        <p v-if="companyLine" class="vcard-identity__company">{{ companyLine }}</p>
        <p v-if="headlineLine" class="vcard-identity__headline">{{ headlineLine }}</p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  vcard: {
    type: Object,
    required: true,
  },
  shape: {
    type: String,
    default: 'rounded',
  },
})

const titleLine = computed(() => [props.vcard.title, props.vcard.department].filter(Boolean).join(' · '))

const companyLine = computed(() => props.vcard.company || '')

const headlineLine = computed(() => props.vcard.headline || '')

const hasProfessionalInfo = computed(() => Boolean(titleLine.value || companyLine.value || headlineLine.value))
</script>

<style scoped>
.vcard-identity__professional {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.vcard-identity__title,
.vcard-identity__company,
.vcard-identity__headline {
  margin: 0;
  color: var(--vcard-muted);
  line-height: 1.5;
}

.vcard-identity__title {
  color: var(--vcard-primary);
  font-weight: 600;
  font-size: 1rem;
}

.vcard-identity__company {
  font-weight: 700;
  color: var(--vcard-text);
  font-size: 1.1rem;
}

.vcard-identity__headline {
  font-size: 0.96rem;
  max-width: 34rem;
  margin-left: auto;
  margin-right: auto;
}
</style>
