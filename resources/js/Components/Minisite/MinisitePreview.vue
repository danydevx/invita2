<template>
  <div class="minisite-preview">
    <div class="minisite-preview__frame">
      <div class="minisite-preview__status-bar">
        <span class="minisite-preview__time">11:32</span>
        <div class="minisite-preview__indicators">
          <i class="bi bi-signal"></i>
          <i class="bi bi-wifi-2"></i>
          <i class="bi bi-battery-full"></i>
        </div>
      </div>

      <div class="minisite-preview__screen">
        <div class="minisite-preview__scroll-area">
          <div class="minisite-preview__sections">
            <template v-for="section in sections" :key="section.id">
              <div v-if="section.is_active" class="minisite-preview__section-wrapper">
                <component
                  :is="sectionComponent(section.section_type)"
                  v-if="sectionComponent(section.section_type) && hasContent(section)"
                  :title="section.title"
                  :subtitle="section.description"
                  :buttons="section.buttons || []"
                  v-bind="sectionProps(section)"
                />
                <div v-else class="minisite-preview__section minisite-preview__section--empty">
                  <div class="minisite-preview__empty-state">
                    <i :class="sectionIcon(section.section_type)"></i>
                    <p>{{ section.title || sectionTypes[section.section_type] || 'Sección' }}</p>
                    <small>Sin contenido</small>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>
    <div class="minisite-preview__actions">
      <a v-if="business?.slug" :href="`/b/${business.slug}`" target="_blank" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-box-arrow-up-right me-1"></i>Abrir en nueva pestaña
      </a>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import SectionServices from '@/Pages/Minisite/components/SectionServices.vue'
import SectionGallery from '@/Pages/Minisite/components/SectionGallery.vue'
import SectionPromotions from '@/Pages/Minisite/components/SectionPromotions.vue'
import SectionContactForm from '@/Pages/Minisite/components/SectionContactForm.vue'
import SectionLocations from '@/Pages/Minisite/components/SectionLocations.vue'
import SectionAbout from '@/Pages/Minisite/components/SectionAbout.vue'
import SectionFeatures from '@/Pages/Minisite/components/SectionFeatures.vue'
import SectionFaqs from '@/Pages/Minisite/components/SectionFaqs.vue'
import SectionProducts from '@/Pages/Minisite/components/SectionProducts.vue'
import SectionHero from '@/Pages/Minisite/components/SectionHero.vue'
import SectionFooter from '@/Pages/Minisite/components/SectionFooter.vue'
import SectionRestaurantMenu from '@/Pages/Minisite/components/SectionRestaurantMenu.vue'

const props = defineProps({
  business: {
    type: Object,
    required: true,
  },
  setting: {
    type: Object,
    default: null,
  },
  sections: {
    type: Array,
    default: () => [],
  },
  socialNetworks: {
    type: Array,
    default: () => [],
  },
  sectionTypes: {
    type: Object,
    default: () => ({}),
  },
})

const sectionComponent = (type) => {
  const components = {
    hero: SectionHero,
    services: SectionServices,
    gallery: SectionGallery,
    promotions: SectionPromotions,
    contact_form: SectionContactForm,
    locations: SectionLocations,
    about: SectionAbout,
    features: SectionFeatures,
    faqs: SectionFaqs,
    products: SectionProducts,
    footer: SectionFooter,
    restaurant_menu: SectionRestaurantMenu,
  }
  return components[type] || null
}

const sectionIcon = (type) => {
  const icons = {
    hero: 'bi bi-house',
    services: 'bi bi-briefcase',
    gallery: 'bi bi-images',
    promotions: 'bi bi-tag',
    contact_form: 'bi bi-envelope',
    locations: 'bi bi-geo-alt',
    about: 'bi bi-info-circle',
    features: 'bi bi-star',
    faqs: 'bi bi-question-circle',
    products: 'bi bi-box-seam',
    footer: 'bi bi-footer',
    restaurant_menu: 'bi bi-cup-hot',
  }
  return icons[type] || 'bi bi-grid'
}

const hasContent = (section) => {
  if (section.section_type === 'hero') {
    return true
  }
  if (section.section_type === 'services') {
    return section.items && section.items.length > 0
  }
  if (section.section_type === 'gallery') {
    return section.items && section.items.length > 0
  }
  if (section.section_type === 'promotions') {
    return section.items && section.items.length > 0
  }
  if (section.section_type === 'contact_form') {
    return section.form && section.form.id
  }
  if (section.section_type === 'locations') {
    return section.items && section.items.length > 0
  }
  if (section.section_type === 'about') {
    return section.content && (section.content.name || section.content.description)
  }
  if (section.section_type === 'features') {
    return section.items && section.items.length > 0
  }
  if (section.section_type === 'faqs') {
    return section.items && section.items.length > 0
  }
  if (section.section_type === 'products') {
    return section.items && section.items.length > 0
  }
  if (section.section_type === 'footer') {
    return true
  }
  if (section.section_type === 'restaurant_menu') {
    return section.items && section.items.length > 0
  }
  return false
}

const sectionProps = (section) => {
  if (section.section_type === 'hero') {
    return {
      business: props.business,
      title: section.title,
      subtitle: section.description,
      backgroundImage: section.config?.background_image,
      config: section.config || {},
    }
  }
  if (section.section_type === 'services') {
    return {
      items: section.items || [],
      config: section.config || {},
      businessSlug: props.business?.slug,
    }
  }
  if (section.section_type === 'gallery') {
    return {
      items: section.items || [],
      config: section.config || {},
      subtitle: section.description,
      buttons: section.buttons || [],
    }
  }
  if (section.section_type === 'promotions') {
    return {
      items: section.items || [],
      config: section.config || {},
      subtitle: section.description,
      buttons: section.buttons || [],
      businessSlug: props.business?.slug,
    }
  }
  if (section.section_type === 'contact_form') {
    return { form: section.form || {}, config: section.config || {} }
  }
  if (section.section_type === 'locations') {
    return { items: section.items || [], config: section.config || {} }
  }
  if (section.section_type === 'about') {
    return { content: section.content || {}, config: section.config || {} }
  }
  if (section.section_type === 'features') {
    return { items: section.items || [], config: section.config || {} }
  }
  if (section.section_type === 'faqs') {
    return { items: section.items || [], config: section.config || {} }
  }
  if (section.section_type === 'products') {
    return { items: section.items || [], config: section.config || {} }
  }
  if (section.section_type === 'footer') {
    return {
      business: props.business,
      text: section.config?.text || '',
      showSocial: section.config?.show_social !== false,
      socialNetworks: props.socialNetworks,
      config: section.config || {},
    }
  }
  if (section.section_type === 'restaurant_menu') {
    return { items: section.items || [], config: section.config || {} }
  }
  return {}
}
</script>

<style lang="less" scoped>
.minisite-preview {
  &__frame {
    width: 320px;
    height: 580px;
    background: #1a1a1a;
    border-radius: 32px;
    padding: 12px;
    box-shadow:
      0 25px 50px -12px rgba(0, 0, 0, 0.25),
      inset 0 0 0 1px rgba(255, 255, 255, 0.1);
    margin: 0 auto;
  }

  &__status-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 16px;
    color: #fff;
    font-size: 12px;
    font-weight: 500;
  }

  &__time {
    font-weight: 600;
  }

  &__indicators {
    display: flex;
    gap: 4px;
    align-items: center;
  }

  &__screen {
    background: #fff;
    border-radius: 24px;
    height: calc(100% - 40px);
    overflow: hidden;
  }

  &__scroll-area {
    height: 100%;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
  }

  &__sections {
    display: flex;
    flex-direction: column;
  }

  &__section-wrapper {
    width: 100%;
  }

  &__section {
    &--empty {
      padding: 24px 16px;
    }
  }

  &__empty-state {
    text-align: center;
    padding: 24px;
    background: #f8f9fa;
    border-radius: 8px;
    color: #6c757d;

    i {
      font-size: 32px;
      margin-bottom: 8px;
      display: block;
      color: #adb5bd;
    }

    p {
      margin: 0 0 4px 0;
      font-weight: 600;
      color: #495057;
    }

    small {
      font-size: 12px;
    }
  }

  &__actions {
    text-align: center;
    margin-top: 16px;
  }
}

.minisite-preview__scroll-area::-webkit-scrollbar {
  width: 4px;
}

.minisite-preview__scroll-area::-webkit-scrollbar-track {
  background: transparent;
}

.minisite-preview__scroll-area::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.2);
  border-radius: 2px;
}
</style>
