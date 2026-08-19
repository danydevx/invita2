<template>
  <section class="hero-simple" :style="heroStyle">
    <div class="hero-simple__content">
      <h1 class="hero-simple__title">{{ title }}</h1>
      <nav class="hero-simple__breadcrumbs">
        <a :href="`/m/${businessSlug}`" class="hero-simple__breadcrumb-link">
          ← Inicio
        </a>
        <span class="hero-simple__breadcrumb-sep">/</span>
        <span class="hero-simple__breadcrumb-current">{{ title }}</span>
      </nav>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  backgroundImage: {
    type: String,
    default: '',
  },
  businessSlug: {
    type: String,
    required: true,
  },
})

const heroStyle = computed(() => {
  const bg = props.backgroundImage || 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1200&q=80'
  return {
    backgroundImage: `linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.5)), url(${bg})`,
    backgroundSize: 'cover',
    backgroundPosition: 'center',
  }
})
</script>

<style lang="less">
.hero-simple {
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 80px 16px 32px;
  margin-top: 64px;
  position: relative;
  z-index: 10;

  &__content {
    text-align: center;
    color: #fff;
    max-width: 1024px;
    width: 100%;
  }

  &__title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 16px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
  }

  &__breadcrumbs {
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  &__breadcrumb-link {
    color: #fff;
    text-decoration: none;
    opacity: 0.9;

    &:hover {
      opacity: 1;
      text-decoration: underline;
    }
  }

  &__breadcrumb-sep {
    opacity: 0.6;
  }

  &__breadcrumb-current {
    opacity: 0.8;
  }
}
</style>
