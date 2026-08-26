<template>
  <article class="vcard" :class="`vcard--${activeTheme}`" :style="wrapperStyle">
    <section class="vcard__shell">
      <VCardHeroClassic
        :vcard="vcard"
        :profilePhotoUrl="profilePhotoUrl"
        :logoUrl="logoUrl"
        :badgeUrl="badgeUrl"
        :heroBackgroundImageUrl="heroBackgroundImageUrl"
        :heroFields="heroFields"
        :shape="shape"
        :imageX="imageX"
        :imageY="imageY"
        :backgroundType="backgroundType"
        :gradientDirection="gradientDirection"
        :patternKey="patternKey"
        :heroImageAlpha="heroImageAlpha"
        @openImagePosition="emit('openImagePosition')"
      />

      <div class="vcard__content">
        <VCardIdentity :vcard="vcard" :shape="shape" />
        <VCardContactList :contacts="contacts" :shape="shape" />
        <VCardFieldList :fields="fields" :shape="shape" />
        <VCardSocials />
        <VCardAppointments :shape="shape" />
        <VCardServices />
        <VCardGallery />
        <VCardProducts />
        <VCardTestimonials />
        <VCardBusinessHours />
        <VCardMenu />
        <VCardContactForm />
        <VCardMap
          :lat="vcard.latitude"
          :lng="vcard.longitude"
          :address="vcard.address"
          :city="vcard.city"
          :state="vcard.state"
          :country="vcard.country"
          :zip="vcard.zip"
        />

        <div class="vcard__qr text-center" v-if="qrCodeUrl">
          <img
            :src="qrCodeUrl"
            alt="QR Code"
            class="vcard__qr-image"
            width="150"
            height="150"
          >
          <p class="vcard__qr-text small text-muted mt-2">Escanea para guardar mi contacto</p>
        </div>

        <VCardActions :vcard="vcard" :shape="shape" />
        <VCardShare :vcard="vcard" :vcard-url="shareUrl" />
      </div>
    </section>
  </article>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, watch } from 'vue'
import VCardHeroClassic from './VCardHeroClassic.vue'
import VCardIdentity from './VCardIdentity.vue'
import VCardActions from './VCardActions.vue'
import VCardContactList from './VCardContactList.vue'
import VCardFieldList from './VCardFieldList.vue'
import VCardSocials from './VCardSocials.vue'
import VCardAppointments from './VCardAppointments.vue'
import VCardServices from './VCardServices.vue'
import VCardGallery from './VCardGallery.vue'
import VCardProducts from './VCardProducts.vue'
import VCardTestimonials from './VCardTestimonials.vue'
import VCardBusinessHours from './VCardBusinessHours.vue'
import VCardContactForm from './VCardContactForm.vue'
import VCardMap from './VCardMap.vue'
import VCardShare from './VCardShare.vue'
import VCardMenu from './VCardMenu.vue'

const props = defineProps({
  vcard: {
    type: Object,
    required: true,
  },
  contacts: {
    type: Array,
    default: () => [],
  },
  fields: {
    type: Array,
    default: () => [],
  },
  logoUrl: {
    type: String,
    default: null,
  },
  badgeUrl: {
    type: String,
    default: null,
  },
  heroBackgroundImageUrl: {
    type: String,
    default: null,
  },
  profilePhotoUrl: {
    type: String,
    default: null,
  },
  shape: {
    type: String,
    default: 'rounded',
  },
  imageX: {
    type: Number,
    default: 0,
  },
  imageY: {
    type: Number,
    default: 0,
  },
  backgroundType: {
    type: String,
    default: 'solid',
  },
  gradientDirection: {
    type: String,
    default: '135deg',
  },
  patternKey: {
    type: String,
    default: null,
  },
  heroImageAlpha: {
    type: Number,
    default: 100,
  },
  theme: {
    type: String,
    default: 'classic',
  },
  qrCodeUrl: {
    type: String,
    default: null,
  },
  vcardUrl: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['openImagePosition'])

const fontLinkId = 'vcard-font-link'
const activeTheme = computed(() => props.theme || props.vcard.design || 'classic')

const heroFields = computed(() => {
  return (props.fields || []).filter((field) => {
    const isActive = field.active !== false
    const showInHero = [true, 1, '1', 'true'].includes(field.config?.show_in_hero)
    return isActive && showInHero
  })
})

const wrapperStyle = computed(() => ({
  '--vcard-primary': props.vcard.primary_color || '#313bac',
  '--vcard-font': `'${props.vcard.font || 'Poppins'}', sans-serif`,
  '--vcard-text': '#111827',
  '--vcard-muted': '#6b7280',
  '--vcard-surface': '#ffffff',
  '--vcard-surface-2': '#f8fafc',
  'font-family': `'${props.vcard.font || 'Poppins'}', sans-serif`,
}))

const shareUrl = computed(() => {
  if (props.vcardUrl) return props.vcardUrl
  if (props.vcard?.public_url) return props.vcard.public_url
  if (typeof window !== 'undefined' && window.location) {
    return window.location.href
  }
  const slug = props.vcard?.slug || props.vcard?.name?.toLowerCase().replace(/\s+/g, '-') || 'unknown'
  return `/v/${slug}`
})

function buildGoogleFontHref(fontName) {
  const family = (fontName || 'Poppins').replace(/ /g, '+')
  return `https://fonts.googleapis.com/css2?family=${family}:wght@400;500;600;700&display=swap`
}

function syncFontLink() {
  if (typeof document === 'undefined') return

  let link = document.getElementById(fontLinkId)
  if (!link) {
    link = document.createElement('link')
    link.id = fontLinkId
    link.rel = 'stylesheet'
    document.head.appendChild(link)
  }

  link.href = buildGoogleFontHref(props.vcard.font)
}

onMounted(syncFontLink)

watch(() => props.vcard.font, syncFontLink)

onBeforeUnmount(() => {
  if (typeof document === 'undefined') return
  const link = document.getElementById(fontLinkId)
  if (link) link.remove()
})
</script>
