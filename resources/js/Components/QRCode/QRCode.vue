<template>
  <img v-if="qrUrl" :src="qrUrl" :alt="alt" :width="size" :height="size" />
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  value: {
    type: String,
    required: true,
  },
  size: {
    type: [Number, String],
    default: 200,
  },
  alt: {
    type: String,
    default: 'QR Code',
  },
})

const qrUrl = computed(() => {
  if (!props.value) return ''
  const encoded = encodeURIComponent(props.value)
  return `https://api.qrserver.com/v1/create-qr-code/?size=${props.size}x${props.size}&data=${encoded}`
})
</script>
