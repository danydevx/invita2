<template>
  <div class="public-vcard-page py-4 px-3">
    <Head :title="vcard.name" />

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
    />
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import VCard from '../../../../../Modules/VCards/resources/assets/js/Components/VCard/VCard.vue'

defineProps({
  vcard: {
    type: Object,
    required: true,
  },
})
</script>

<style scoped>
.public-vcard-page {
  min-height: 100vh;
  background: #e5e5e5;
}
</style>
