<template>
  <div class="vcard__hero" :class="heroClasses" @click="$emit('openImagePosition')">
    <div class="vcard__hero-bg"></div>
    <div class="vcard__profile">
      <div class="vcard__profile-wrapper">
        <img
          v-if="profilePhotoUrl"
          :src="profilePhotoUrl"
          alt="Profile photo"
          class="vcard__profile-photo"
          :class="{ 'rounded': shape === 'rounded' }"
          :style="profilePhotoStyle"
        >
        <div
          v-else-if="logoUrl"
          class="vcard__profile-logo-only"
          :class="{ 'rounded': shape === 'rounded' }"
        >
          <img :src="logoUrl" alt="Logo" class="vcard__profile-logo-img" :style="profilePhotoStyle">
        </div>
        <div
          v-else
          class="vcard__profile-placeholder"
          :class="{ 'rounded': shape === 'rounded' }"
          :style="{ backgroundColor: vcard.primary_color || 'var(--vcard-primary)' }"
        >
          <i class="bi bi-person"></i>
        </div>
        <img
          v-if="logoUrl && profilePhotoUrl"
          :src="logoUrl"
          alt="Logo"
          class="vcard__profile-logo-overlay"
        >
      </div>
      <div v-if="badgeUrl" class="vcard__profile-badge">
        <img :src="badgeUrl" alt="Badge" class="vcard__badge">
      </div>
    </div>
    <div v-if="profilePhotoUrl || logoUrl" class="vcard__edit-hint">
      <i class="bi bi-pencil-fill"></i>
    </div>
  </div>
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
  shape: {
    type: String,
    default: 'rounded',
  },
  design: {
    type: String,
    default: 'classic',
  },
  imageX: {
    type: Number,
    default: 0,
  },
  imageY: {
    type: Number,
    default: 0,
  },
})

defineEmits(['openImagePosition'])

const heroBgColor = computed(() => {
  const color = props.vcard.primary_color || 'var(--vcard-primary)'
  return color + '20'
})

const profilePhotoStyle = computed(() => ({
  transform: `translate(${props.imageX / 16}rem, ${props.imageY / 16}rem)`,
}))

const heroClasses = computed(() => {
  return ['vcard__hero', `vcard__hero--${props.design || 'classic'}`]
})
</script>

<style>
.vcard__hero {
  position: relative;
  padding: 1.5rem 1rem 1rem;
  text-align: center;
  cursor: pointer;
}

.vcard__hero-bg {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 100px;
  background: var(--vcard-primary);
  opacity: 0.2;
}

.vcard__profile {
  position: relative;
  z-index: 1;
  display: inline-block;
}

.vcard__profile-wrapper {
  position: relative;
  display: inline-block;
}

.vcard__profile-photo,
.vcard__profile-placeholder,
.vcard__profile-logo-only {
  width: 160px;
  height: 160px;
  object-fit: cover;
  border: 5px solid var(--vcard-surface);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-radius: 0;
}

.vcard__profile-photo.rounded,
.vcard__profile-placeholder.rounded,
.vcard__profile-logo-only.rounded {
  border-radius: 1rem;
}

.vcard__profile-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
}

.vcard__profile-placeholder i {
  font-size: 4rem;
  color: var(--vcard-surface);
}

.vcard__profile-logo-only {
  background-color: var(--vcard-surface);
  overflow: hidden;
}

.vcard__profile-logo-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.vcard__profile-logo-overlay {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 44px;
  height: 44px;
  border-radius: 0;
  object-fit: contain;
  border: 3px solid var(--vcard-surface);
  background: var(--vcard-surface);
  padding: 2px;
}

.vcard__profile-logo-overlay.rounded {
  border-radius: 50%;
}

.vcard__profile-badge {
  position: absolute;
  top: 0;
  right: -10px;
  z-index: 2;
}

.vcard__badge {
  max-width: 40px;
  max-height: 20px;
  object-fit: contain;
}

.vcard__edit-hint {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: rgba(0, 0, 0, 0.5);
  color: var(--vcard-surface);
  width: 160px;
  height: 160px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.2s;
  pointer-events: none;
}

.vcard__hero:hover .vcard__edit-hint {
  opacity: 1;
}

.vcard__edit-hint i {
  font-size: 1.5rem;
}
</style>
