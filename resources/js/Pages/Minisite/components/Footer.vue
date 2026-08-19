<template>
  <footer class="minisite-footer">
    <div class="minisite-footer__inner">
      <div v-if="showSocial && socialNetworks.length" class="minisite-footer__social">
        <a
          v-for="network in socialNetworks"
          :key="network.platform"
          :href="network.url"
          target="_blank"
          rel="noopener noreferrer"
          class="minisite-footer__social-link"
        >
          <i :class="getIcon(network.platform)"></i>
        </a>
      </div>
      <p v-if="text" class="minisite-footer__text">{{ text }}</p>
      <p class="minisite-footer__copyright">{{ business.name }}</p>
    </div>
  </footer>
</template>

<script setup>
defineProps({
  business: Object,
  text: String,
  showSocial: Boolean,
  socialNetworks: {
    type: Array,
    default: () => [],
  },
})

const getIcon = (platform) => {
  const icons = {
    facebook: 'bi bi-facebook',
    instagram: 'bi bi-instagram',
    twitter: 'bi bi-twitter-x',
    tiktok: 'bi bi-tiktok',
    youtube: 'bi bi-youtube',
    linkedin: 'bi bi-linkedin',
    whatsapp: 'bi bi-whatsapp',
  }
  return icons[platform?.toLowerCase()] || 'bi bi-share'
}
</script>

<style lang="less">
.minisite-footer {
  background: #212529;
  color: #fff;
  padding: 32px 16px;
  margin-top: auto;

  &__inner {
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
  }

  &__social {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-bottom: 16px;
  }

  &__social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    font-size: 1.25rem;
    transition: background 0.2s ease;

    &:hover {
      background: rgba(255, 255, 255, 0.2);
    }
  }

  &__text {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 8px;
  }

  &__copyright {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.5);
    margin: 0;
  }
}
</style>
