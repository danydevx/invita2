<template>
  <div class="vcard-preview">
    <div class="vcard-preview__frame">
      <VCard
        :vcard="vcard"
        :contacts="contacts"
        :fields="fields"
        :logoUrl="logoUrl"
        :badgeUrl="badgeUrl"
        :profilePhotoUrl="profilePhotoUrl"
        :heroBackgroundImageUrl="heroBackgroundImageUrl"
        :shape="shape"
        :imageX="vcard.image_x || 0"
        :imageY="vcard.image_y || 0"
        :backgroundType="vcard.background_type || 'solid'"
        :gradientDirection="vcard.gradient_direction || '135deg'"
        :patternKey="vcard.pattern_key || 'dots'"
        :heroImageAlpha="vcard.hero_image_alpha || 100"
        :qrCodeUrl="qrCodeUrl"
        @openImagePosition="openImagePositionModal"
      />
    </div>

    <ImagePositionModal
      :show="showImagePositionModal"
      :imageUrl="imagePositionUrl"
      :currentX="vcard.image_x || 0"
      :currentY="vcard.image_y || 0"
      @close="closeImagePositionModal"
      @save="saveImagePosition"
    />
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import VCard from '../../../Components/VCard/VCard.vue'
import ImagePositionModal from '../../../Components/VCard/ImagePositionModal.vue'

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
})

const showImagePositionModal = ref(false)
const imagePositionUrl = ref('')
const emit = defineEmits(['change-image-position'])

const qrCodeUrl = computed(() => {
  const url = `${window.location.origin}/v/${props.vcard.slug}`
  const encoded = encodeURIComponent(url)
  return `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encoded}`
})

function openImagePositionModal() {
  imagePositionUrl.value = props.profilePhotoUrl || props.logoUrl || ''
  showImagePositionModal.value = true
}

function closeImagePositionModal() {
  showImagePositionModal.value = false
}

function saveImagePosition({ x, y }) {
  emit('change-image-position', { x, y })
  showImagePositionModal.value = false
}
</script>

<style scoped>
.vcard-preview {
  position: sticky;
  top: 0;
  height: 100vh;
  display: flex;
  flex-direction: column;
}

.vcard-preview__frame {
  flex: 1;
  overflow-y: auto;
  background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
  padding: 1rem;
}

.vcard-preview__frame :deep(.vcard) {
  border-radius: 1rem;
  overflow: hidden;
}
</style>
