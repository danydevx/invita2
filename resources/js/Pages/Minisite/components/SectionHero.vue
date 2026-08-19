<template>
  <component
    :is="heroComponent"
    v-if="heroComponent"
    :business="business"
    :title="title"
    :subtitle="subtitle"
    :backgroundImage="backgroundImage"
  />
</template>

<script setup>
import { computed } from 'vue'
import HeroLeft from './HeroLeft.vue'
import HeroCenter from './HeroCenter.vue'
import HeroRight from './HeroRight.vue'

const props = defineProps({
  business: Object,
  title: String,
  subtitle: String,
  backgroundImage: String,
  config: {
    type: Object,
    default: () => ({}),
  },
})

const heroComponent = computed(() => {
  const layout = props.config?.layout || 'left'
  return { left: HeroLeft, center: HeroCenter, right: HeroRight }[layout] || HeroLeft
})
</script>
