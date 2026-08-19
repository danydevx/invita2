<template>
  <section class="hero-section" :class="heroClass" :style="heroStyles">
    <div v-if="backgroundImage" class="hero-background" :style="backgroundStyles"></div>
    <div class="hero-overlay" :style="{ opacity: overlayOpacity }"></div>

    <div class="container hero-container">
      <div class="row justify-content-center align-items-center min-vh-75">
        <div class="col-12 text-center">
          <img v-if="showLogo && logoPath" :src="logoPath" :alt="businessName" class="hero-logo mb-4">

          <h1 class="hero-title mb-3">{{ title || businessName }}</h1>

          <p v-if="subtitle" class="hero-subtitle lead mb-4">{{ subtitle }}</p>

          <p v-if="textAux" class="hero-text-aux small mb-4">{{ textAux }}</p>

          <div v-if="buttons && buttons.length" class="hero-buttons d-flex gap-3 flex-wrap justify-content-center">
            <a
              v-for="(btn, idx) in buttons"
              :key="idx"
              :href="getButtonHref(btn)"
              class="btn btn-lg hero-btn btn-scheme"
            >
              {{ btn.text }}
            </a>
          </div>

          <div v-if="showContactInfo && (phone || email)" class="hero-contact mt-4 d-flex gap-4 justify-content-center">
            <span v-if="phone" class="contact-item">
              <i class="bi bi-telephone me-2"></i>{{ phone }}
            </span>
            <span v-if="email" class="contact-item">
              <i class="bi bi-envelope me-2"></i>{{ email }}
            </span>
          </div>

          <div v-if="showSocialLinks && socialLinks && socialLinks.length" class="hero-social mt-4 d-flex gap-3 justify-content-center">
            <a
              v-for="(social, idx) in socialLinks"
              :key="idx"
              :href="social.url"
              target="_blank"
              class="social-link"
              :title="social.name"
            >
              <i :class="getSocialIcon(social.platform)"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: String,
  subtitle: String,
  textAux: String,
  buttons: {
    type: Array,
    default: () => []
  },
  backgroundType: {
    type: String,
    default: 'gradient'
  },
  backgroundColor: String,
  gradientStart: String,
  gradientEnd: String,
  backgroundImage: String,
  overlayOpacity: {
    type: Number,
    default: 0.7
  },
  layout: {
    type: String,
    default: 'fullbleed'
  },
  alignment: {
    type: String,
    default: 'left'
  },
  showLogo: {
    type: Boolean,
    default: true
  },
  showContactInfo: {
    type: Boolean,
    default: true
  },
  showSocialLinks: {
    type: Boolean,
    default: false
  },
  socialLinks: {
    type: Array,
    default: () => []
  },
  logoPath: String,
  businessName: String,
  phone: String,
  email: String
})

const heroClass = computed(() => `hero-layout-${props.layout}`)

const heroStyles = computed(() => {
  if (props.backgroundImage) {
    return {
      backgroundImage: `url(${props.backgroundImage})`,
      backgroundSize: 'cover',
      backgroundPosition: 'center',
      minHeight: '650px'
    }
  }

  if (props.backgroundType === 'color') {
    return {
      backgroundColor: props.backgroundColor || '#1a1a1a',
      minHeight: '650px'
    }
  }

  return {
    background: `linear-gradient(135deg, ${props.gradientStart || '#FF4500'} 0%, ${props.gradientEnd || '#FF6B35'} 100%)`,
    minHeight: '650px'
  }
})

const backgroundStyles = computed(() => ({
  backgroundImage: `url(${props.backgroundImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center'
}))

const columnClasses = computed(() => {
  if (props.alignment === 'center') {
    return 'col-12 text-center'
  }
  if (props.alignment === 'right') {
    return 'col-12 text-end'
  }
  return 'col-lg-7'
})

const getButtonClass = (style) => {
  if (style === 'primary') return 'btn-primary'
  if (style === 'secondary') return 'btn-secondary'
  if (style === 'outline') return 'btn-outline-light'
  return 'btn-primary'
}

const getButtonHref = (btn) => {
  if (btn.link_type === 'internal' && btn.anchor) {
    return `#${btn.anchor}`
  }
  return btn.url || '#'
}

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
    tiktok: 'bi bi-tiktok',
    default: 'bi bi-globe'
  }
  return icons[platform?.toLowerCase()] || icons.default
}
</script>

<style scoped>
.hero-section {
  position: relative;
  padding: 80px 0;
  color: white;
  overflow: hidden;
  display: flex;
  align-items: center;
}

.hero-layout-fullbleed {
  min-height: 650px;
}

.hero-layout-fullwidth {
  min-height: 600px;
}

.hero-layout-centered {
  min-height: 500px;
  text-align: center;
}

.hero-layout-split .col-lg-7:last-child {
  display: flex;
  justify-content: flex-end;
}

.hero-background {
  position: absolute;
  inset: 0;
  z-index: 0;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: #000000;
  z-index: 1;
}

.hero-container {
  position: relative;
  z-index: 2;
}

.hero-title {
  font-family: var(--heading-font);
  font-size: var(--font-size-h1, 3.5rem);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  line-height: 1.1;
  margin-bottom: 1rem;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-subtitle {
  font-family: var(--body-font);
  font-size: var(--font-size-h6, 1.25rem);
  opacity: 0.95;
  font-weight: 300;
  max-width: 600px;
  margin: 0 auto;
}

.hero-text-aux {
  font-family: var(--body-font);
  opacity: 0.8;
  font-size: var(--font-size-sm, 0.95rem);
  max-width: 600px;
  margin: 0 auto;
}

.hero-buttons {
  margin-top: 1.5rem;
}

.hero-btn {
  font-family: var(--heading-font);
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 500;
  padding: 0.75rem 2rem;
  border-radius: 0;
  transition: all 0.3s ease;
}

.hero-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(255, 69, 0, 0.4);
}

.hero-contact {
  font-family: var(--body-font);
  font-size: var(--font-size-sm, 0.9rem);
  opacity: 0.9;
}

.contact-item i {
  opacity: 0.8;
}

.hero-social {
  font-size: var(--font-size-h5, 1.5rem);
}

.social-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.15);
  color: white;
  transition: all 0.3s ease;
  text-decoration: none;
}

.social-link:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateY(-3px);
  color: white;
}

.hero-logo {
  max-height: 80px;
  filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.3));
}

@media (max-width: 768px) {
  .hero-title {
    font-size: var(--font-size-h2, 2.5rem);
  }

  .hero-subtitle {
    font-size: var(--font-size-base, 1rem);
  }

  .hero-layout-split .col-lg-7:last-child {
    justify-content: flex-start;
  }
}
</style>
