<template>
  <section class="section-reviews">
    <div class="section-reviews__inner">
      <h2 v-if="title" class="section-reviews__title">{{ title }}</h2>
      <h3 v-if="subtitle" class="section-reviews__subtitle">{{ subtitle }}</h3>
      <p v-if="description" class="section-reviews__description-text">{{ description }}</p>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay reseñas disponibles.
      </div>

      <div v-else-if="viewMode === 'carousel'" class="section-reviews__carousel">
        <div
          v-for="review in displayedItems"
          :key="review.id"
          class="section-reviews__card"
        >
          <div class="section-reviews__stars">
            <i
              v-for="star in 5"
              :key="star"
              class="bi"
              :class="star <= review.rating ? 'bi-star-fill' : 'bi-star'"
            ></i>
          </div>

          <p v-if="showComment && review.comment" class="section-reviews__comment">
            "{{ review.comment }}"
          </p>

          <div class="section-reviews__author">
            <strong v-if="showClientName">{{ review.client_name }}</strong>
            <span v-if="review.company"> - {{ review.company }}</span>
          </div>

          <a
            v-if="review.google_link"
            :href="review.google_link"
            target="_blank"
            class="section-reviews__google-link"
          >
            <i class="bi bi-google"></i> Ver en Google
          </a>
        </div>
      </div>

      <div v-else-if="viewMode === 'list'" class="section-reviews__list">
        <div
          v-for="review in displayedItems"
          :key="review.id"
          class="section-reviews__list-item"
        >
          <div class="section-reviews__stars">
            <i
              v-for="star in 5"
              :key="star"
              class="bi"
              :class="star <= review.rating ? 'bi-star-fill' : 'bi-star'"
            ></i>
          </div>

          <p v-if="showComment && review.comment" class="section-reviews__comment">
            "{{ review.comment }}"
          </p>

          <div class="section-reviews__author">
            <strong v-if="showClientName">{{ review.client_name }}</strong>
            <span v-if="review.company"> - {{ review.company }}</span>
          </div>

          <a
            v-if="review.google_link"
            :href="review.google_link"
            target="_blank"
            class="section-reviews__google-link"
          >
            <i class="bi bi-google"></i> Ver en Google
          </a>
        </div>
      </div>

      <div v-else class="section-reviews__grid">
        <div
          v-for="review in displayedItems"
          :key="review.id"
          class="section-reviews__card"
        >
          <div class="section-reviews__stars">
            <i
              v-for="star in 5"
              :key="star"
              class="bi"
              :class="star <= review.rating ? 'bi-star-fill' : 'bi-star'"
            ></i>
          </div>

          <p v-if="showComment && review.comment" class="section-reviews__comment">
            "{{ review.comment }}"
          </p>

          <div class="section-reviews__author">
            <strong v-if="showClientName">{{ review.client_name }}</strong>
            <span v-if="review.company"> - {{ review.company }}</span>
          </div>

          <a
            v-if="review.google_link"
            :href="review.google_link"
            target="_blank"
            class="section-reviews__google-link"
          >
            <i class="bi bi-google"></i> Ver en Google
          </a>
        </div>
      </div>

      <div v-if="hasMoreItems" class="section-reviews__show-all">
        <a :href="allReviewsUrl" class="btn btn-outline-primary">
          Ver todas las reseñas ({{ items.length }})
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
  config: Object,
  description: String,
  buttons: {
    type: Array,
    default: () => [],
  },
})

const viewMode = computed(() => props.config?.view_mode || 'grid')

const maxItems = computed(() => {
  if (props.config?.show_all) return props.items.length
  return props.config?.max_items || 12
})

const displayedItems = computed(() => {
  return props.items.slice(0, maxItems.value)
})

const hasMoreItems = computed(() => {
  return !props.config?.show_all && props.items.length > maxItems.value
})

const allReviewsUrl = computed(() => {
  return '#reviews'
})

const showComment = computed(() => {
  return props.config?.show_comment !== false
})

const showClientName = computed(() => {
  return props.config?.show_client_name !== false
})
</script>

<script>
import { computed } from 'vue'
export default {
  name: 'SectionReviews'
}
</script>

<style lang="less">
.section-reviews {
  padding: 48px 16px;
  background: #fff;

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
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 24px;
  }

  &__carousel {
    display: flex;
    gap: 16px;
    overflow-x: auto;
    padding-bottom: 16px;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;

    &::-webkit-scrollbar {
      height: 4px;
    }

    &::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 2px;
    }

    &::-webkit-scrollbar-thumb {
      background: #dee2e6;
      border-radius: 2px;
    }
  }

  &__carousel &__card {
    flex: 0 0 280px;
    scroll-snap-align: start;
  }

  &__list {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  &__list-item {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  &__card {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  &__stars {
    color: #ffc107;
    font-size: 1.25rem;
  }

  &__comment {
    font-size: 0.95rem;
    color: #495057;
    line-height: 1.6;
    margin: 0;
    font-style: italic;
  }

  &__author {
    font-size: 0.9rem;
    color: #212529;

    strong {
      color: #212529;
    }
  }

  &__google-link {
    font-size: 0.85rem;
    color: #4285f4;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: auto;

    &:hover {
      text-decoration: underline;
    }
  }

  &__show-all {
    display: flex;
    justify-content: center;
    margin-top: 24px;
  }
}
</style>
