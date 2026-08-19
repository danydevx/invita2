<template>
  <section class="section-features">
    <div class="section-features__inner">
      <h2 v-if="title" class="section-features__title">{{ title }}</h2>
      <h3 v-if="subtitle" class="section-features__subtitle">{{ subtitle }}</h3>
      <p v-if="description" class="section-features__description-text">{{ description }}</p>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay características disponibles.
      </div>

      <div v-else class="section-features__grid">
        <div
          v-for="item in items"
          :key="item.id"
          class="section-features__item"
        >
          <div v-if="showIcon && item.icon" class="section-features__icon">
            <i :class="item.icon"></i>
          </div>
          <div class="section-features__content">
            <h3 v-if="showTitle && item.title" class="section-features__item-title">
              {{ item.title }}
            </h3>
            <p v-if="showDescription && item.description" class="section-features__item-desc">
              {{ item.description }}
            </p>
          </div>
        </div>
      </div>

      <div v-if="buttons && buttons.length" class="section-features__buttons">
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

const showIcon = computed(() => props.config?.show_icon !== false)
const showTitle = computed(() => props.config?.show_title !== false)
const showDescription = computed(() => props.config?.show_description !== false)
</script>

<script>
import { computed, defineComponent } from 'vue'
export default defineComponent({ name: 'SectionFeatures' })
</script>

<style lang="less">
.section-features {
  padding: 48px 16px;
  background: #f8f9fa;

  &__inner {
    max-width: 1024px;
    margin: 0 auto;
  }

  &__title {
    font-weight: 700;
    margin: 0 0 8px;
    text-align: center;
    color: #212529;
  }

  &__subtitle {
    font-weight: 600;
    color: #495057;
    text-align: center;
    margin: 0 0 16px;
  }

  &__description-text {
    font-size: 1rem;
    color: #6c757d;
    text-align: center;
    margin: 0 0 16px;
  }

  &__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 16px;
  }

  &__item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 16px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  &__icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e7f1ff;
    border-radius: 50%;
    color: #0d6efd;
    font-size: 1.25rem;
    margin-bottom: 12px;
  }

  &__content {
    flex: 1;
  }

  &__item-title {
    font-size: 0.875rem;
    font-weight: 600;
    margin: 0 0 8px;
    color: #212529;
  }

  &__item-desc {
    font-size: 0.75rem;
    color: #6c757d;
    margin: 0;
    line-height: 1.4;
  }

  &__buttons {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
  }
}
</style>
