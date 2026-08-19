<template>
  <component
    v-if="currentSection"
    :is="currentSection.component"
    :id="sectionConfig?.anchorId"
    v-bind="mergedProps"
  />
</template>

<script setup>
import { computed } from 'vue'
import BoldTheme from '../Themes/Bold/theme.js'

const props = defineProps({
  sectionName: {
    type: String,
    required: true
  },
  sectionProps: {
    type: Object,
    default: () => ({})
  },
  themeSlug: {
    type: String,
    default: 'bold'
  },
  globalProps: {
    type: Object,
    default: () => ({})
  },
  heroData: Object,
  businessData: Object
})

const themeConfig = computed(() => {
  const themes = {
    'bold': BoldTheme
  }
  return themes[props.themeSlug] || BoldTheme
})

const sectionConfig = computed(() => {
  return themeConfig.value.sections.find(s => s.name === props.sectionName)
})

const currentSection = computed(() => {
  return sectionConfig.value?.component || null
})

const mergedProps = computed(() => {
  const sectionDefaults = sectionConfig.value?.props || {}
  const themeOverrides = props.sectionProps || {}

  return {
    ...props.globalProps,
    ...sectionDefaults,
    ...themeOverrides,
    ...(props.sectionName === 'hero' ? {
      title: props.heroData?.title || props.businessData?.name,
      subtitle: props.heroData?.subtitle,
      textAux: props.heroData?.text_aux,
      buttons: props.heroData?.buttons || [],
      backgroundType: props.heroData?.background_type,
      backgroundColor: props.heroData?.background_color,
      gradientStart: props.heroData?.background_gradient_start,
      gradientEnd: props.heroData?.background_gradient_end,
      backgroundImage: props.heroData?.background_image_path,
      alignment: props.heroData?.alignment || sectionDefaults.alignment,
      showLogo: sectionDefaults.showLogo ?? true,
      showContactInfo: props.heroData?.show_contact_info ?? sectionDefaults.showContactInfo ?? true,
      showSocialLinks: props.heroData?.show_social_links ?? sectionDefaults.showSocialLinks ?? false,
      socialLinks: props.heroData?.social_links || [],
      overlayOpacity: sectionDefaults.overlayOpacity ?? 0.7,
      logoPath: props.businessData?.logo_path,
      businessName: props.businessData?.name,
      phone: props.businessData?.phone,
      email: props.businessData?.email
    } : {})
  }
})
</script>
