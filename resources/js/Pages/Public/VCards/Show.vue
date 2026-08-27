<template>
  <div class="public-vcard-page py-4 px-3" :style="pageBackgroundStyle">
    <Head>
      <title>{{ seoTitle }}</title>
      <meta name="description" :content="seoDescription" v-if="seoDescription" />
      <meta name="robots" :content="robotsContent" v-if="seoSetting" />
      <link rel="canonical" :href="canonicalUrl" v-if="canonicalUrl" />
      <meta name="theme-color" :content="themeColor" />
      <meta name="msapplication-TileColor" :content="themeColor" />
      <meta name="apple-mobile-web-app-title" :content="seoTitle" />
      <link rel="icon" type="image/png" sizes="32x32" :href="logoUrl || '/icons/default-vcard.png'" />
      <link rel="icon" type="image/png" sizes="192x192" :href="logoUrl || '/icons/default-vcard-192.png'" />
      <link rel="apple-touch-icon" :href="logoUrl || '/icons/default-vcard-apple.png'" />
      <meta property="og:title" :content="ogTitle" />
      <meta property="og:description" :content="ogDescription" v-if="ogDescription" />
      <meta property="og:type" content="profile" />
      <meta property="og:url" :content="vcard.public_url" />
      <meta property="og:image" :content="ogImage" v-if="ogImage" />
      <meta property="og:image:alt" :content="ogImageAlt" v-if="ogImageAlt" />
      <meta property="profile:first_name" :content="vcard.first_name" v-if="vcard.first_name" />
      <meta property="profile:last_name" :content="vcard.last_name" v-if="vcard.last_name" />
    </Head>

    <VCard
      :vcard="vcard"
      :contacts="vcard.contacts || []"
      :fields="vcard.active_fields || []"
      :logoUrl="vcard.logo ? `/storage/${vcard.logo}` : null"
      :badgeUrl="vcard.badge ? `/storage/${vcard.badge}` : null"
      :profilePhotoUrl="vcard.profile_photo ? `/storage/${vcard.profile_photo}` : null"
      :heroBackgroundImageUrl="vcard.hero_background_image ? `/storage/${vcard.hero_background_image}` : null"
      :shape="vcard.shape || 'circle'"
      :imageX="vcard.image_x || 0"
      :imageY="vcard.image_y || 0"
      :backgroundType="vcard.background_type || 'solid'"
      :gradientDirection="vcard.gradient_direction || '135deg'"
      :patternKey="vcard.pattern_key || 'dots'"
      :heroImageAlpha="vcard.hero_image_alpha || 100"
      :qrCodeUrl="vcard.qr_code_url"
      :sections="vcard.sections || {}"
      :selectedServices="vcard.services || []"
      :packages="vcard.packages || []"
      :gallery="vcard.gallery || null"
      :products="vcard.products || []"
      :testimonials="vcard.testimonials || []"
      :businessHours="vcard.business_hours || []"
      :menu="vcard.menu || []"
      :location="vcard.location || null"
      :features="vcard.features || []"
    />

    <AiChatWidget
      v-if="aiChatbot && aiChatbot.is_enabled"
      :businessSlug="vcard.listing_slug"
      :businessName="vcard.listing_name || vcard.name"
      :chatbotName="aiChatbot.chatbot_name || 'Asistente Virtual'"
      :chatbotAvatar="aiChatbot.chatbot_avatar || ''"
      :widgetColor="aiChatbot.widget_color || '#3B82F6'"
      :widgetTheme="aiChatbot.widget_theme || 'light'"
      :allowReset="aiChatbot.allow_reset_chat"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import VCard from '../../../../../Modules/VCards/resources/assets/js/Components/VCard/VCard.vue'
import AiChatWidget from '@/Components/Minisite/AiChatWidget.vue'

const props = defineProps({
  vcard: {
    type: Object,
    required: true,
  },
  aiChatbot: {
    type: Object,
    default: null,
  },
})

const seoSetting = computed(() => props.vcard?.seo_setting || null)

const logoUrl = computed(() => {
  return props.vcard.logo ? `/storage/${props.vcard.logo}` : null
})

const themeColor = computed(() => {
  return props.vcard.primary_color || '#2563EB'
})

const seoTitle = computed(() => {
  return seoSetting.value?.seo_title || props.vcard.name || 'vCard'
})

const seoDescription = computed(() => {
  return seoSetting.value?.seo_description || props.vcard.headline || ''
})

const ogTitle = computed(() => {
  return seoSetting.value?.og_title || seoTitle.value
})

const ogDescription = computed(() => {
  return seoSetting.value?.og_description || seoDescription.value
})

const ogImage = computed(() => {
  if (seoSetting.value?.og_image) {
    return seoSetting.value.og_image
  }
  if (props.vcard.profile_photo) {
    return `/storage/${props.vcard.profile_photo}`
  }
  return null
})

const ogImageAlt = computed(() => {
  return seoSetting.value?.og_image_alt || props.vcard.name || 'vCard'
})

const canonicalUrl = computed(() => {
  return seoSetting.value?.canonical_url || props.vcard.public_url
})

const robotsContent = computed(() => {
  if (!seoSetting.value) return 'index, follow'
  const parts = []
  parts.push(seoSetting.value.allow_indexing !== false ? 'index' : 'noindex')
  parts.push(seoSetting.value.follow_links !== false ? 'follow' : 'nofollow')
  return parts.join(', ')
})

const patterns = {
  dots: `url("data:image/svg+xml,%3Csvg width='20' height='20' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='2' cy='2' r='1.5' fill='%23ffffff' fill-opacity='0.3'/%3E%3C/svg%3E")`,
  'lines-diagonal': `url("data:image/svg+xml,%3Csvg width='10' height='10' xmlns='http://www.w3.org/2000/svg'%3E%3Cline x1='0' y1='10' x2='10' y2='0' stroke='%23ffffff' stroke-opacity='0.25' stroke-width='1'/%3E%3C/svg%3E")`,
  'lines-horizontal': `url("data:image/svg+xml,%3Csvg width='10' height='4' xmlns='http://www.w3.org/2000/svg'%3E%3Cline x1='0' y1='2' x2='10' y2='2' stroke='%23ffffff' stroke-opacity='0.25' stroke-width='1'/%3E%3C/svg%3E")`,
  squares: `url("data:image/svg+xml,%3Csvg width='20' height='20' xmlns='http://www.w3.org/2000/svg'%3E%3Crect x='0' y='0' width='10' height='10' fill='none' stroke='%23ffffff' stroke-opacity='0.2' stroke-width='1'/%3E%3Crect x='10' y='10' width='10' height='10' fill='none' stroke='%23ffffff' stroke-opacity='0.2' stroke-width='1'/%3E%3C/svg%3E")`,
  chevron: `url("data:image/svg+xml,%3Csvg width='40' height='40' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 20 L20 0 L40 20 L20 40 Z' fill='none' stroke='%23ffffff' stroke-opacity='0.15' stroke-width='1'/%3E%3C/svg%3E")`,
  crosshatch: `url("data:image/svg+xml,%3Csvg width='12' height='12' xmlns='http://www.w3.org/2000/svg'%3E%3Cline x1='0' y1='6' x2='12' y2='6' stroke='%23ffffff' stroke-opacity='0.2' stroke-width='1'/%3E%3Cline x1='6' y1='0' x2='6' y2='12' stroke='%23ffffff' stroke-opacity='0.2' stroke-width='1'/%3E%3C/svg%3E")`,
}

const pageBackgroundStyle = computed(() => {
  const type = props.vcard.body_background_type || 'solid'
  const color = props.vcard.body_primary_color || '#ffffff'
  const direction = props.vcard.body_gradient_direction || '135deg'

  if (type === 'gradient') {
    return {
      background: `linear-gradient(${direction}, ${color}, ${adjustColor(color, 30)})`,
    }
  } else if (type === 'pattern') {
    const patternKey = props.vcard.body_pattern_key || 'dots'
    const patternSvg = patterns[patternKey] || patterns.dots
    return {
      backgroundColor: color,
      backgroundImage: patternSvg,
    }
  }

  return { backgroundColor: color }
})

function adjustColor(hex, percent) {
  const num = parseInt(hex.replace('#', ''), 16)
  const amt = Math.round(2.55 * percent)
  const R = Math.max(0, (num >> 16) - amt)
  const G = Math.max(0, ((num >> 8) & 0x00FF) - amt)
  const B = Math.max(0, (num & 0x0000FF) - amt)
  return `#${(1 << 24 | R << 16 | G << 8 | B).toString(16).slice(1)}`
}
</script>

<style scoped>
.public-vcard-page {
  min-height: 100vh;
}
</style>
