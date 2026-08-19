<template>
  <footer class="footer-section" :style="footerStyles">
    <div class="container">
      <div class="row g-4">
        <div v-if="showBrand" class="col-lg-4 col-md-6">
          <div class="footer-brand">
            <img v-if="logoPath" :src="logoPath" :alt="businessName" class="footer-logo mb-3">
            <h5 class="footer-business-name">{{ businessName }}</h5>
            <p v-if="description" class="footer-description">{{ description }}</p>
          </div>
        </div>

        <div v-if="showContact && (contactInfo.phone || contactInfo.email || contactInfo.address)" class="col-lg-4 col-md-6">
          <h6 class="footer-title">Contacto</h6>
          <ul class="footer-contact list-unstyled">
            <li v-if="contactInfo.phone">
              <i class="bi bi-telephone me-2"></i>
              <a :href="`tel:${contactInfo.phone}`">{{ contactInfo.phone }}</a>
            </li>
            <li v-if="contactInfo.email">
              <i class="bi bi-envelope me-2"></i>
              <a :href="`mailto:${contactInfo.email}`">{{ contactInfo.email }}</a>
            </li>
            <li v-if="contactInfo.address">
              <i class="bi bi-geo-alt me-2"></i>
              <span>{{ contactInfo.address }}</span>
            </li>
            <li v-if="contactInfo.website">
              <i class="bi bi-globe me-2"></i>
              <a :href="contactInfo.website" target="_blank">{{ contactInfo.website }}</a>
            </li>
          </ul>
        </div>

        <div v-if="showSocial && socialLinks.length" class="col-lg-4 col-md-12">
          <h6 class="footer-title">Síguenos</h6>
          <div class="footer-social d-flex gap-3 flex-wrap">
            <a
              v-for="(social, index) in socialLinks"
              :key="index"
              :href="social.url"
              target="_blank"
              class="social-link"
              :style="getSocialLinkStyle(social.platform)"
              :title="social.name || social.platform"
            >
              <i :class="getSocialIcon(social.platform)"></i>
            </a>
          </div>
        </div>
      </div>

      <div v-if="showLinks && footerLinks.length" class="row mt-4">
        <div class="col-12">
          <div class="footer-links d-flex gap-4 flex-wrap justify-content-center">
            <a v-for="(link, index) in footerLinks" :key="index" :href="link.url" class="footer-link">
              {{ link.label }}
            </a>
          </div>
        </div>
      </div>

      <div class="footer-bottom mt-4 pt-4">
        <p class="text-center mb-0">
          &copy; {{ currentYear }} {{ businessName }}. Todos los derechos reservados.
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  businessName: {
    type: String,
    default: ''
  },
  logoPath: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  contactInfo: {
    type: Object,
    default: () => ({
      phone: '',
      email: '',
      address: '',
      website: ''
    })
  },
  socialLinks: {
    type: Array,
    default: () => []
  },
  footerLinks: {
    type: Array,
    default: () => []
  },
  showBrand: {
    type: Boolean,
    default: true
  },
  showContact: {
    type: Boolean,
    default: true
  },
  showSocial: {
    type: Boolean,
    default: true
  },
  showLinks: {
    type: Boolean,
    default: false
  },
  backgroundColor: {
    type: String,
    default: '#1a1a2e'
  },
  textColor: {
    type: String,
    default: '#ffffff'
  },
  accentColor: {
    type: String,
    default: '#FF4500'
  },
  linkColor: {
    type: String,
    default: '#b0b0b0'
  },
})

const currentYear = new Date().getFullYear()

const footerStyles = computed(() => ({
  backgroundColor: props.backgroundColor,
  color: props.textColor,
  paddingTop: '3rem',
  paddingBottom: '2rem',
}))

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
    pinterest: 'bi bi-pinterest',
    snapchat: 'bi bi-snapchat',
    threads: 'bi bi-threads',
    reddit: 'bi bi-reddit',
    discord: 'bi bi-discord',
    spotify: 'bi bi-spotify',
  }
  return icons[platform?.toLowerCase()] || 'bi bi-globe'
}

const getSocialLinkStyle = (platform) => {
  const colors = {
    facebook: '#1877F2',
    instagram: '#E4405F',
    twitter: '#000000',
    linkedin: '#0A66C2',
    youtube: '#FF0000',
    tiktok: '#000000',
    whatsapp: '#25D366',
    telegram: '#26A5E4',
    pinterest: '#E60023',
    snapchat: '#FFFC00',
    threads: '#000000',
    reddit: '#FF4500',
    discord: '#5865F2',
    spotify: '#1DB954',
  }
  return {
    color: colors[platform?.toLowerCase()] || '#ffffff',
    borderColor: colors[platform?.toLowerCase()] || '#ffffff',
  }
}
</script>

<style scoped>
.footer-section {
  font-family: 'Roboto Condensed', sans-serif;
}

.footer-brand {
  max-width: 300px;
}

.footer-logo {
  max-height: 60px;
  filter: brightness(0) invert(1);
}

.footer-business-name {
  font-family: 'Oswald', sans-serif;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.footer-description {
  font-size: var(--font-size-sm, 0.9rem);
  opacity: 0.8;
  line-height: 1.6;
}

.footer-title {
  font-family: var(--heading-font);
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 600;
  margin-bottom: 1rem;
  font-size: var(--font-size-base, 1rem);
}

.footer-contact {
  font-size: var(--font-size-sm, 0.9rem);
}

.footer-contact li {
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
}

.footer-contact a {
  color: v-bind('textColor');
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-contact a:hover {
  color: v-bind('accentColor');
}

.footer-social {
  font-size: var(--font-size-h5, 1.5rem);
}

.social-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 42px;
  height: 42px;
  border-radius: 50%;
  border: 1px solid;
  transition: all 0.3s ease;
  text-decoration: none;
  opacity: 0.8;
}

.social-link:hover {
  opacity: 1;
  transform: translateY(-3px);
  background: rgba(255, 255, 255, 0.1);
}

.footer-links {
  font-size: var(--font-size-sm, 0.85rem);
}

.footer-link {
  color: v-bind('linkColor');
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-link:hover {
  color: v-bind('accentColor');
}

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  font-size: var(--font-size-sm, 0.85rem);
  opacity: 0.7;
}

@media (max-width: 768px) {
  .footer-section {
    text-align: center;
  }

  .footer-brand {
    max-width: 100%;
    margin: 0 auto;
  }

  .footer-social {
    justify-content: center;
  }

  .footer-contact li {
    justify-content: center;
  }
}
</style>
