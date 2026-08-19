<template>
  <section class="contact-section py-5" :style="sectionStyles">
    <div class="container">
      <div v-if="showTitle" class="text-center mb-5">
        <h2 class="section-title" :style="{ color: titleColor }">{{ title }}</h2>
        <p v-if="subtitle" class="section-subtitle lead" :style="{ color: subtitleColor }">{{ subtitle }}</p>
      </div>

      <div class="row g-4">
        <div class="col-lg-6">
          <div class="contact-form-wrapper" :class="cardClass">
            <h3 v-if="showFormTitle" class="form-title mb-4">{{ formTitle }}</h3>

            <form @submit.prevent="submitForm" class="contact-form">
              <div v-if="showNameField" class="mb-3">
                <label class="form-label">{{ nameLabel }}</label>
                <input
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  :class="inputClass"
                  :placeholder="namePlaceholder"
                  required
                >
              </div>

              <div v-if="showEmailField" class="mb-3">
                <label class="form-label">{{ emailLabel }}</label>
                <input
                  v-model="form.email"
                  type="email"
                  class="form-control"
                  :class="inputClass"
                  :placeholder="emailPlaceholder"
                  required
                >
              </div>

              <div v-if="showPhoneField" class="mb-3">
                <label class="form-label">{{ phoneLabel }}</label>
                <input
                  v-model="form.phone"
                  type="tel"
                  class="form-control"
                  :class="inputClass"
                  :placeholder="phonePlaceholder"
                >
              </div>

              <div class="mb-3">
                <label class="form-label">{{ messageLabel }}</label>
                <textarea
                  v-model="form.message"
                  class="form-control"
                  :class="inputClass"
                  rows="4"
                  :placeholder="messagePlaceholder"
                  required
                ></textarea>
              </div>

              <button type="submit" class="btn btn-lg w-100 btn-scheme" :disabled="sending">
                <span v-if="sending">
                  <i class="bi bi-hourglass-split me-2"></i>Enviando...
                </span>
                <span v-else>
                  <i class="bi bi-send me-2"></i>{{ submitButtonText }}
                </span>
              </button>
            </form>

            <div v-if="successMessage" class="alert alert-success mt-3" role="alert">
              {{ successMessage }}
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="contact-info-wrapper h-100">
            <div v-if="showContactInfo" class="contact-info">
              <h3 v-if="showInfoTitle" class="info-title mb-4">{{ infoTitle }}</h3>

              <div v-if="contactInfo.phone" class="contact-item mb-4">
                <div class="d-flex align-items-start gap-3">
                  <div class="contact-icon" :style="{ backgroundColor: accentColor }">
                    <i class="bi bi-telephone"></i>
                  </div>
                  <div>
                    <h6 class="contact-label">{{ phoneContactLabel }}</h6>
                    <a :href="`tel:${contactInfo.phone}`" class="contact-value">{{ contactInfo.phone }}</a>
                  </div>
                </div>
              </div>

              <div v-if="contactInfo.email" class="contact-item mb-4">
                <div class="d-flex align-items-start gap-3">
                  <div class="contact-icon" :style="{ backgroundColor: accentColor }">
                    <i class="bi bi-envelope"></i>
                  </div>
                  <div>
                    <h6 class="contact-label">{{ emailContactLabel }}</h6>
                    <a :href="`mailto:${contactInfo.email}`" class="contact-value">{{ contactInfo.email }}</a>
                  </div>
                </div>
              </div>

              <div v-if="contactInfo.address" class="contact-item mb-4">
                <div class="d-flex align-items-start gap-3">
                  <div class="contact-icon" :style="{ backgroundColor: accentColor }">
                    <i class="bi bi-geo-alt"></i>
                  </div>
                  <div>
                    <h6 class="contact-label">{{ addressContactLabel }}</h6>
                    <p class="contact-value mb-0">{{ contactInfo.address }}</p>
                  </div>
                </div>
              </div>

              <div v-if="contactInfo.website" class="contact-item mb-4">
                <div class="d-flex align-items-start gap-3">
                  <div class="contact-icon" :style="{ backgroundColor: accentColor }">
                    <i class="bi bi-globe"></i>
                  </div>
                  <div>
                    <h6 class="contact-label">{{ websiteContactLabel }}</h6>
                    <a :href="contactInfo.website" target="_blank" class="contact-value">{{ contactInfo.website }}</a>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="showSocial && socialLinks.length" class="contact-social mt-4">
              <h6 class="social-title mb-3">{{ socialTitle }}</h6>
              <div class="d-flex gap-3 flex-wrap">
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

            <div v-if="showLocations && locations.length" class="contact-locations mt-4">
              <h6 class="locations-title mb-3">{{ locationsTitle }}</h6>
              <div v-for="location in locations.slice(0, 3)" :key="location.id" class="location-item mb-3">
                <strong>{{ location.name }}</strong>
                <p v-if="location.address_line_1" class="mb-0 text-muted small">{{ location.address_line_1 }}, {{ location.city }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'

const props = defineProps({
  title: {
    type: String,
    default: 'Contáctanos'
  },
  subtitle: {
    type: String,
    default: ''
  },
  showTitle: {
    type: Boolean,
    default: true
  },
  titleColor: {
    type: String,
    default: '#1a1a2e'
  },
  subtitleColor: {
    type: String,
    default: '#6B7280'
  },
  sectionBgColor: {
    type: String,
    default: '#ffffff'
  },
  formTitle: {
    type: String,
    default: 'Envíanos un mensaje'
  },
  showFormTitle: {
    type: Boolean,
    default: true
  },
  nameLabel: {
    type: String,
    default: 'Nombre'
  },
  namePlaceholder: {
    type: String,
    default: 'Tu nombre'
  },
  showNameField: {
    type: Boolean,
    default: true
  },
  emailLabel: {
    type: String,
    default: 'Email'
  },
  emailPlaceholder: {
    type: String,
    default: 'tu@email.com'
  },
  showEmailField: {
    type: Boolean,
    default: true
  },
  phoneLabel: {
    type: String,
    default: 'Teléfono'
  },
  phonePlaceholder: {
    type: String,
    default: '+54 9 11 1234-5678'
  },
  showPhoneField: {
    type: Boolean,
    default: true
  },
  messageLabel: {
    type: String,
    default: 'Mensaje'
  },
  messagePlaceholder: {
    type: String,
    default: '¿En qué podemos ayudarte?'
  },
  submitButtonText: {
    type: String,
    default: 'Enviar mensaje'
  },
  successMessage: {
    type: String,
    default: '¡Mensaje enviado! Te responderemos pronto.'
  },
  inputStyle: {
    type: String,
    default: 'light'
  },
  buttonStyle: {
    type: String,
    default: 'primary'
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
  showContactInfo: {
    type: Boolean,
    default: true
  },
  infoTitle: {
    type: String,
    default: 'Información de contacto'
  },
  showInfoTitle: {
    type: Boolean,
    default: true
  },
  phoneContactLabel: {
    type: String,
    default: 'Teléfono'
  },
  emailContactLabel: {
    type: String,
    default: 'Email'
  },
  addressContactLabel: {
    type: String,
    default: 'Dirección'
  },
  websiteContactLabel: {
    type: String,
    default: 'Sitio web'
  },
  socialLinks: {
    type: Array,
    default: () => []
  },
  showSocial: {
    type: Boolean,
    default: true
  },
  socialTitle: {
    type: String,
    default: 'Síguenos'
  },
  locations: {
    type: Array,
    default: () => []
  },
  showLocations: {
    type: Boolean,
    default: true
  },
  locationsTitle: {
    type: String,
    default: 'Nuestras ubicaciones'
  },
  accentColor: {
    type: String,
    default: '#FF4500'
  },
  businessSlug: {
    type: String,
    default: ''
  },
})

const sending = ref(false)
const form = reactive({
  name: '',
  email: '',
  phone: '',
  message: '',
})

const sectionStyles = computed(() => ({
  backgroundColor: props.sectionBgColor,
}))

const cardClass = computed(() => {
  return props.inputStyle === 'dark' ? 'bg-dark text-white p-4 rounded' : 'bg-light p-4 rounded'
})

const inputClass = computed(() => {
  return props.inputStyle === 'dark' ? 'bg-dark text-white border-secondary' : ''
})

const buttonClass = computed(() => {
  return props.buttonStyle === 'secondary' ? 'btn-secondary' : 'btn-primary'
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
    backgroundColor: 'rgba(255,255,255,0.1)',
  }
}

const submitForm = async () => {
  sending.value = true

  try {
    const response = await fetch(`/b/${props.businessSlug}/contact`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      },
      body: JSON.stringify(form),
    })

    if (response.ok) {
      form.name = ''
      form.email = ''
      form.phone = ''
      form.message = ''
    }
  } catch (error) {
    console.error('Error submitting form:', error)
  } finally {
    sending.value = false
  }
}
</script>

<style scoped>
.contact-section {
  font-family: var(--body-font);
}

.section-title {
  font-family: var(--heading-font);
  font-size: var(--font-size-h2, 2.5rem);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin-bottom: 1rem;
}

.section-subtitle {
  max-width: 600px;
  margin: 0 auto;
}

.form-title,
.info-title {
  font-family: var(--heading-font);
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 600;
}

.contact-icon {
  width: 48px;
  height: 48px;
  min-width: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: var(--font-size-h6, 1.2rem);
}

.contact-label {
  font-size: var(--font-size-sm, 0.85rem);
  text-transform: uppercase;
  letter-spacing: 1px;
  opacity: 0.7;
  margin-bottom: 0.25rem;
}

.contact-value {
  color: inherit;
  text-decoration: none;
  font-size: var(--font-size-base, 1rem);
}

a.contact-value:hover {
  color: v-bind('accentColor');
}

.social-title,
.locations-title {
  font-family: 'Oswald', sans-serif;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 600;
}

.social-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 42px;
  height: 42px;
  border-radius: 50%;
  font-size: var(--font-size-h6, 1.25rem);
  transition: all 0.3s ease;
  text-decoration: none;
}

.social-link:hover {
  transform: translateY(-3px);
  opacity: 0.9;
}

.location-item {
  padding-left: 0.5rem;
  border-left: 3px solid v-bind('accentColor');
}

@media (max-width: 768px) {
  .section-title {
    font-size: var(--font-size-h3, 2rem);
  }
}
</style>
