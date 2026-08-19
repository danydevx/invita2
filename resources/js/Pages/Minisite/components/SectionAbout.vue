<template>
  <section class="section-about">
    <div class="section-about__inner">
      <h2 v-if="title" class="section-about__title">{{ title }}</h2>
      <h3 v-if="subtitle" class="section-about__subtitle">{{ subtitle }}</h3>
      <p v-if="description" class="section-about__description-text">{{ description }}</p>

      <div v-if="content" class="section-about__content">
        <div v-if="showImage && (content.logo || content.image)" class="section-about__image-wrapper">
          <img
            :src="content.logo || content.image"
            :alt="content.name"
            class="section-about__image"
          />
        </div>

        <div v-if="showDescription && content.description" class="section-about__description">
          <p>{{ content.description }}</p>
        </div>
      </div>

      <div v-else class="text-muted text-center py-4">
        No hay información disponible.
      </div>

      <div v-if="buttons && buttons.length" class="section-about__buttons">
        <a
          v-for="(btn, index) in buttons"
          :key="index"
          :href="btn.url"
          class="btn"
          :class="'btn-' + (btn.style || 'primary')"
        >
          {{ btn.text }}
        </a>
      </div>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({
  title: String,
  subtitle: String,
  items: {
    type: Array,
    default: () => [],
  },
  content: {
    type: Object,
    default: null,
  },
  config: {
    type: Object,
    default: () => ({}),
  },
  description: String,
  buttons: {
    type: Array,
    default: () => [],
  },
})

const showImage = computed(() => props.config?.show_image !== false)
const showDescription = computed(() => props.config?.show_description !== false)
</script>

<script>
import { computed, defineComponent } from 'vue'
export default defineComponent({ name: 'SectionAbout' })
</script>

<style lang="less">
.section-about {
  padding: 48px 16px;

  &__inner {
    max-width: 1024px;
    margin: 0 auto;
    text-align: center;
  }

  &__title {
    font-weight: 700;
    margin: 0 0 24px;
    color: #212529;
  }

  &__subtitle {
    font-weight: 600;
    margin: 0 0 16px;
    color: #495057;
  }

  &__description-text {
    font-size: 1rem;
    line-height: 1.6;
    color: #6c757d;
    margin: 0 0 16px;
  }

  &__image-wrapper {
    margin-bottom: 24px;
  }

  &__image {
    width: 120px;
    height: 120px;
    object-fit: contain;
    border-radius: 50%;
  }

  &__description {
    font-size: 1rem;
    line-height: 1.6;
    color: #6c757d;

    p {
      margin: 0;
    }
  }

  &__buttons {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
  }
}
</style>
