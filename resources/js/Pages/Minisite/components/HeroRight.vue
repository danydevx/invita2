<template>
  <div class="hero hero--right" :style="backgroundStyle">
    <div class="hero__inner">
      <div class="hero__content">
        <h1 class="hero__title">{{ title || business.name }}</h1>
        <p v-if="subtitle" class="hero__subtitle">{{ subtitle }}</p>
      </div>
      <div class="hero__media">
        <img v-if="business.logo" :src="business.logo" :alt="business.name" class="hero__logo" />
      </div>
    </div>
    <div v-if="showSocial && socialNetworks && socialNetworks.length" class="hero__social">
      <a
        v-for="(network, idx) in socialNetworks"
        :key="idx"
        :href="network.url"
        target="_blank"
        class="hero__social-link"
        :title="network.platform"
      >
        <i :class="getSocialIcon(network.platform)"></i>
      </a>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  business: Object,
  title: String,
  subtitle: String,
  backgroundImage: String,
  showSocial: {
    type: Boolean,
    default: false,
  },
  socialNetworks: {
    type: Array,
    default: () => [],
  },
})

const backgroundStyle = computed(() => {
  if (props.backgroundImage) {
    return {
      '--hero-bg': `url(${props.backgroundImage})`,
    }
  }
  return {}
})

const getSocialIcon = (platform) => {
  const icons = {
    facebook: 'bi bi-facebook',
    instagram: 'bi bi-instagram',
    twitter: 'bi bi-twitter-x',
    linkedin: 'bi bi-linkedin',
    youtube: 'bi bi-youtube',
    tiktok: 'bi bi-tiktok',
    whatsapp: 'bi bi-whatsapp',
    telegram: 'bi bi-telegram',
    default: 'bi bi-globe',
  }
  return icons[platform?.toLowerCase()] || icons.default
}
</script>

<style lang="less">
.hero {
  background-color: #f8f9fa;
  background-image: var(--hero-bg, none);
  background-size: cover;
  background-position: center;

  &--right {
    .hero__inner {
      display: flex;
      align-items: center;
      gap: 24px;
      padding: 48px 16px;
      max-width: 600px;
      margin: 0 auto;
    }

    .hero__content {
      flex: 1;
      text-align: right;
    }

    .hero__media {
      flex-shrink: 0;
    }

    .hero__logo {
      width: 80px;
      height: 80px;
      object-fit: contain;
      border-radius: 8px;
    }

    .hero__title {
      font-size: 1.75rem;
      font-weight: 700;
      margin: 0 0 8px;
      color: #212529;
    }

    .hero__subtitle {
      font-size: 1rem;
      margin: 0;
      color: #6c757d;
    }
  }

  &__social {
    display: flex;
    justify-content: center;
    gap: 16px;
    padding: 16px;
    background: rgba(255, 255, 255, 0.9);
  }

  &__social-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #3B82F6;
    color: white;
    font-size: 1.25rem;
    transition: all 0.3s ease;
    text-decoration: none;

    &:hover {
      background: #1d4ed8;
      transform: translateY(-2px);
      color: white;
    }
  }
}
</style>
