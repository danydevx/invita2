<template>
  <section class="vcard-section vcard-about" v-if="about && (about.title || about.description)">
    <h2 v-if="about.title" class="vcard-section__title">{{ about.title }}</h2>
    <p v-if="about.subtitle" class="vcard-about__subtitle">{{ about.subtitle }}</p>
    <div v-if="about.description" class="vcard-about__description" v-html="about.description"></div>
    <div v-if="about.image_path" class="vcard-about__image mt-3">
      <img :src="about.image_path" :alt="about.title || 'About'" class="img-fluid rounded">
    </div>
  </section>
</template>

<script setup>
const props = defineProps({
  about: {
    type: Object,
    default: null,
  },
})
</script>

<style scoped>
.vcard-about__subtitle {
  margin: 0 0 0.75rem;
  font-size: 0.9375rem;
  color: var(--vcard-muted);
  font-weight: 500;
}

.vcard-about__description {
  font-size: 0.875rem;
  color: var(--vcard-text);
  line-height: 1.6;
}

.vcard-about__description :deep(p) {
  margin: 0 0 0.5rem;
}

.vcard-about__description :deep(p:last-child) {
  margin-bottom: 0;
}

.vcard-about__image img {
  max-width: 100%;
  max-height: 300px;
  object-fit: cover;
}
</style>
