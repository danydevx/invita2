<template>
  <section class="vcard-hero vcard-hero--classic">
    <div class="vcard-hero__banner" :style="heroBannerStyle">
      <div v-if="profilePhotoUrl" class="vcard-hero__profile-wrap">
        <div class="vcard-hero__profile-frame" :class="{ 'rounded': shape === 'rounded' }">
          <img
            :src="profilePhotoUrl"
            alt="Profile photo"
            class="vcard-hero__profile-photo"
            :style="profilePhotoStyle"
          >
        </div>
      </div>

      <img
        v-if="heroBackgroundImageUrl"
        :src="heroBackgroundImageUrl"
        alt="Hero image"
        class="vcard-hero__banner-image"
        :style="heroImageStyle"
      >

      <svg class="vcard-hero__wave" viewBox="0 0 1000 120" preserveAspectRatio="none" aria-hidden="true">
        <path
          d="M0,56 C120,14 210,104 336,68 C468,30 566,8 690,54 C808,98 890,26 1000,56 L1000,120 L0,120 Z"
          fill="var(--vcard-primary)"
        />
      </svg>

      <div class="vcard-hero__badge" v-if="badgeUrl" :class="{ 'rounded': shape === 'rounded' }">
        <img :src="badgeUrl" alt="Badge">
      </div>
    </div>

    <div class="vcard-hero__body">
      <div v-if="logoUrl" class="vcard-hero__logo" :class="{ 'rounded': shape === 'rounded' }">
        <img :src="logoUrl" alt="Logo">
      </div>

      <div class="vcard-hero__identity">
        <p v-if="accreditationsLine" class="vcard-hero__accreditations">{{ accreditationsLine }}</p>
        <h1 class="vcard-hero__name">{{ displayName }}</h1>
        <p v-if="personalMetaLine" class="vcard-hero__meta">{{ personalMetaLine }}</p>
      </div>

      <div v-if="heroFields.length" class="vcard-hero__field-icons" aria-label="Campos destacados en el hero">
        <span
          v-for="field in heroFields"
          :key="field.id"
          class="vcard-hero__field-icon"
          :class="{ 'rounded': shape === 'rounded' }"
          :title="field.label || field.field_type_definition?.name || 'Campo'"
        >
          <i :class="field.field_type_definition?.icon || 'bi-link'"></i>
        </span>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  vcard: {
    type: Object,
    required: true,
  },
  profilePhotoUrl: {
    type: String,
    default: null,
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
  heroFields: {
    type: Array,
    default: () => [],
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
})

const displayName = computed(() => {
  const parts = [
    props.vcard.prefix,
    props.vcard.first_name,
    props.vcard.middle_name,
    props.vcard.last_name,
  ].filter(Boolean)

  return parts.join(' ') || props.vcard.full_name || props.vcard.name || ''
})

const personalMetaLine = computed(() => {
  const parts = []
  if (props.vcard.preferred_name) parts.push(props.vcard.preferred_name)
  if (props.vcard.pronouns) parts.push(props.vcard.pronouns)
  return parts.join(' · ')
})

const accreditationsLine = computed(() => props.vcard.accreditations || '')

const heroImageStyle = computed(() => ({
  objectPosition: 'center',
  opacity: (props.heroImageAlpha / 100).toFixed(2),
}))

const profilePhotoStyle = computed(() => ({
  transform: `translate(${props.imageX / 16}rem, ${props.imageY / 16}rem)`,
}))

const patterns = {
  dots: `url("data:image/svg+xml,%3Csvg width='20' height='20' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='2' cy='2' r='1.5' fill='%23ffffff' fill-opacity='0.3'/%3E%3C/svg%3E")`,
  'lines-diagonal': `url("data:image/svg+xml,%3Csvg width='10' height='10' xmlns='http://www.w3.org/2000/svg'%3E%3Cline x1='0' y1='10' x2='10' y2='0' stroke='%23ffffff' stroke-opacity='0.25' stroke-width='1'/%3E%3C/svg%3E")`,
  'lines-horizontal': `url("data:image/svg+xml,%3Csvg width='10' height='4' xmlns='http://www.w3.org/2000/svg'%3E%3Cline x1='0' y1='2' x2='10' y2='2' stroke='%23ffffff' stroke-opacity='0.25' stroke-width='1'/%3E%3C/svg%3E")`,
  squares: `url("data:image/svg+xml,%3Csvg width='20' height='20' xmlns='http://www.w3.org/2000/svg'%3E%3Crect x='0' y='0' width='10' height='10' fill='none' stroke='%23ffffff' stroke-opacity='0.2' stroke-width='1'/%3E%3Crect x='10' y='10' width='10' height='10' fill='none' stroke='%23ffffff' stroke-opacity='0.2' stroke-width='1'/%3E%3C/svg%3E")`,
  chevron: `url("data:image/svg+xml,%3Csvg width='40' height='40' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 20 L20 0 L40 20 L20 40 Z' fill='none' stroke='%23ffffff' stroke-opacity='0.15' stroke-width='1'/%3E%3C/svg%3E")`,
  crosshatch: `url("data:image/svg+xml,%3Csvg width='12' height='12' xmlns='http://www.w3.org/2000/svg'%3E%3Cline x1='0' y1='6' x2='12' y2='6' stroke='%23ffffff' stroke-opacity='0.2' stroke-width='1'/%3E%3Cline x1='6' y1='0' x2='6' y2='12' stroke='%23ffffff' stroke-opacity='0.2' stroke-width='1'/%3E%3C/svg%3E")`,
}

const heroBannerStyle = computed(() => {
  const base = {
    backgroundColor: 'var(--vcard-primary)',
    backgroundImage: 'none',
    backgroundSize: 'auto',
  }

  switch (props.backgroundType) {
    case 'solid':
      return base

    case 'gradient':
      return {
        ...base,
        background: `linear-gradient(${props.gradientDirection}, var(--vcard-primary), color-mix(in srgb, var(--vcard-primary) 65%, black))`,
      }

    case 'pattern':
      const patternSvg = patterns[props.patternKey] || patterns.dots
      return {
        ...base,
        backgroundImage: patternSvg,
        backgroundSize: 'auto',
      }

    default:
      return base
  }
})
</script>

<style scoped>
.vcard-hero--classic {
  background: var(--vcard-surface);
}

.vcard-hero__banner {
  position: relative;
  height: clamp(12.5rem, 40vw, 13.75rem);
}

.vcard-hero__banner-image,
.vcard-hero__banner-fallback {
  width: 100%;
  height: 100%;
  display: block;
}

.vcard-hero__banner-image {
  object-fit: cover;
}

.vcard-hero__wave {
  position: absolute;
  left: 0;
  right: 0;
  bottom: -0.0625rem;
  width: 100%;
  height: 5.625rem;
  pointer-events: none;
}

.vcard-hero__profile-wrap {
  position: absolute;
  left: 50%;
  bottom: -2.5rem;
  transform: translateX(-50%);
  z-index: 10;
}

.vcard-hero__profile-frame {
  width: 11.25rem;
  height: 11.25rem;
  padding: 0.25rem;
  background: rgba(255, 255, 255, 0.96);
  box-shadow: 0 0.5rem 1.25rem rgba(15, 23, 42, 0.18);
  overflow: hidden;
  border-radius: 0;
}

.vcard-hero__profile-frame.rounded {
  border-radius: 1rem;
}

.vcard-hero__profile-photo {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: cover;
  border-radius: inherit;
}

.vcard-hero__badge {
  position: absolute;
  top: 1rem;
  left: 1rem;
  z-index: 2;
  background: rgba(255, 255, 255, 0.92);
  border-radius: 0;
  padding: 0.35rem 0.55rem;
  box-shadow: 0 0.5rem 1.25rem rgba(15, 23, 42, 0.12);
}

.vcard-hero__badge.rounded {
  border-radius: 62.4375rem;
}

.vcard-hero__badge img {
  display: block;
  max-width: 4.5rem;
  max-height: 1.75rem;
  object-fit: contain;
}

.vcard-hero__body {
  position: relative;
  background: var(--vcard-surface);
  padding: 3.75rem 1.25rem 2rem;
}

.vcard-hero__identity {
  position: relative;
  z-index: 1;
  padding-top: 0.5rem;
  max-width: calc(100% - 5.25rem);
}

.vcard-hero__field-icons {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin-top: 1rem;
}

.vcard-hero__field-icon {
  width: 2.5rem;
  height: 2.5rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 0;
  background: color-mix(in srgb, var(--vcard-primary) 12%, var(--vcard-surface));
  color: var(--vcard-primary);
  box-shadow: 0 0.25rem 0.75rem rgba(15, 23, 42, 0.1);
  flex: 0 0 auto;
}

.vcard-hero__field-icon.rounded {
  border-radius: 50%;
}

.vcard-hero__field-icon i {
  font-size: 1rem;
  line-height: 1;
}

.vcard-hero__name {
  margin: 0;
  font-size: clamp(1.8rem, 5vw, 2.35rem);
  line-height: 1.05;
  font-weight: 700;
  color: var(--vcard-text);
}

.vcard-hero__accreditations {
  margin: 0 0 0.4rem;
  font-size: 0.82rem;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: var(--vcard-muted);
  font-weight: 700;
}

.vcard-hero__meta {
  margin: 0.35rem 0 0;
  font-size: 0.95rem;
  color: var(--vcard-muted);
}

.vcard-hero__logo {
  position: absolute;
  top: -2.375rem;
  right: 1.25rem;
  width: 4.5rem;
  height: 4.5rem;
  border-radius: 0;
  background: var(--vcard-surface);
  box-shadow: 0 0.625rem 1.5rem rgba(15, 23, 42, 0.14);
  padding: 0.4rem;
}

.vcard-hero__logo.rounded {
  border-radius: 1rem;
}

.vcard-hero__logo img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
}
</style>
