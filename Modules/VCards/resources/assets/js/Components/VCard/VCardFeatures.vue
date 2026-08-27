<template>
  <section class="vcard-section vcard-features" v-if="features.length > 0">
    <h2 class="vcard-section__title">Características</h2>

    <div class="vcard-features__grid">
      <article
        v-for="feature in features"
        :key="feature.id"
        class="vcard-feature"
      >
        <div class="vcard-feature__icon" v-if="feature.icon">
          <i :class="feature.icon"></i>
        </div>
        <div class="vcard-feature__content">
          <h3 class="vcard-feature__title">{{ feature.title }}</h3>
          <p v-if="feature.description" class="vcard-feature__text">{{ feature.description }}</p>
        </div>
      </article>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({
  features: {
    type: Array,
    default: () => [],
  },
})
</script>

<style scoped>
.vcard-features__grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
  gap: 1rem;
}

.vcard-feature {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 1rem;
  background: var(--vcard-surface);
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s, box-shadow 0.2s;
}

.vcard-feature:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

.vcard-feature__icon {
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  background: var(--vcard-primary);
  color: var(--vcard-surface);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.75rem;
  flex-shrink: 0;
}

.vcard-feature__icon i {
  font-size: 1.25rem;
}

.vcard-feature__content {
  flex: 1;
  min-width: 0;
}

.vcard-feature__title {
  margin: 0 0 0.25rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--vcard-text);
  line-height: 1.3;
}

.vcard-feature__text {
  margin: 0;
  font-size: 0.75rem;
  color: var(--vcard-muted);
  line-height: 1.4;
}
</style>
