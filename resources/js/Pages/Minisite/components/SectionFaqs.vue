<template>
  <section class="section-faqs">
    <div class="section-faqs__inner">
      <h2 v-if="title" class="section-faqs__title">{{ title }}</h2>
      <h3 v-if="subtitle" class="section-faqs__subtitle">{{ subtitle }}</h3>
      <p v-if="description" class="section-faqs__description-text">{{ description }}</p>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay preguntas frecuentes disponibles.
      </div>

      <div v-else class="section-faqs__list">
        <div
          v-for="item in items"
          :key="item.id"
          class="section-faqs__item"
        >
          <div class="section-faqs__question">
            <i class="bi bi-question-circle"></i>
            <span>{{ item.question }}</span>
          </div>
          <div v-if="showQuestions && item.answer" class="section-faqs__answer">
            {{ item.answer }}
          </div>
        </div>
      </div>

      <div v-if="buttons && buttons.length" class="section-faqs__buttons">
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
import { computed } from 'vue'

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

const showQuestions = computed(() => props.config?.show_questions !== false)
</script>

<script>
import { defineComponent } from 'vue'
export default defineComponent({ name: 'SectionFaqs' })
</script>

<style lang="less">
.section-faqs {
  padding: 48px 16px;

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

  &__list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  &__item {
    background: #fff;
    border-radius: 8px;
    padding: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  &__question {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-weight: 600;
    color: #212529;
    margin-bottom: 8px;

    i {
      color: #0d6efd;
      font-size: 1.25rem;
      flex-shrink: 0;
    }
  }

  &__answer {
    font-size: 0.875rem;
    line-height: 1.6;
    color: #6c757d;
    padding-left: 36px;
  }

  &__buttons {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
  }
}
</style>
