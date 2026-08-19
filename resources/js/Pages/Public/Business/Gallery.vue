<template>
  <MinisiteLayout
    :business="business"
    :theme="theme"
    :modules="modules"
    :branding="branding"
  >
    <nav class="navbar navbar-expand-lg sticky-top" :style="navbarStyle">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center" :style="{ color: navbarTextColor }" :href="`/${business.slug}`">
          <img v-if="business.logo_path" :src="business.logo_path" :alt="business.name" class="me-2" style="height: 40px;">
          <span v-else>{{ business.name }}</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPublic" :style="{ borderColor: navbarTextColor }">
          <span class="navbar-toggler-icon" :style="{ filter: navbarTogglerIconFilter }"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarPublic">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <a :href="`/b/${business.slug}`" class="nav-link" :style="{ color: navbarTextColor }">Inicio</a>
            </li>
            <li v-if="isModuleEnabled('services')" class="nav-item">
              <a :href="`/b/${business.slug}/services`" class="nav-link" :style="{ color: navbarTextColor }">Servicios</a>
            </li>
            <li v-if="isModuleEnabled('gallery')" class="nav-item">
              <a :href="`/b/${business.slug}/gallery`" class="nav-link" :style="{ color: navbarTextColor }">Galería</a>
            </li>
            <li v-if="isModuleEnabled('products')" class="nav-item">
              <a :href="`/b/${business.slug}/products`" class="nav-link" :style="{ color: navbarTextColor }">Productos</a>
            </li>
            <li v-if="isModuleEnabled('appointments')" class="nav-item">
              <a :href="`/b/${business.slug}/book`" class="nav-link" :style="{ color: navbarTextColor }">Reservar</a>
            </li>
            <li v-if="isModuleEnabled('contact_form')" class="nav-item">
              <a :href="`/b/${business.slug}/contact`" class="nav-link" :style="{ color: navbarTextColor }">Contacto</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="py-5" :style="{ backgroundColor: 'var(--brand-background, #fff)' }">
      <div class="container">
        <div class="text-center mb-5">
          <h1 class="fw-bold" :style="{ color: 'var(--brand-primary)' }">Galería de fotos</h1>
          <p class="text-muted">Mira nuestro trabajo</p>
        </div>

        <div v-if="images.length === 0" class="alert alert-info text-center">
          No hay imagenes en la galeria.
        </div>

        <div class="row g-3">
          <div v-for="(img, index) in images" :key="img.id" class="col-6 col-md-4 col-lg-3">
            <a href="#" class="gallery-item d-block overflow-hidden" :style="{ borderRadius: 'var(--brand-card-radius, 8px)' }" @click.prevent="openGallery(index)">
              <img :src="img.path" :alt="img.title || img.original_name" class="img-fluid w-100" style="height: 200px; object-fit: cover; display: block;">
            </a>
          </div>
        </div>

        <div class="text-center mt-5">
          <Link :href="`/b/${business.slug}`" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-2"></i>Volver al inicio
          </Link>
        </div>
      </div>
    </div>

    <FooterSection
      v-if="isModuleEnabled('socialmedia') || business.phone || business.email"
      :business-name="business.name"
      :logo-path="business.logo_path"
      :contact-info="{
        phone: business.phone,
        email: business.email,
        address: locations.length > 0 ? locations[0].address_line_1 + ', ' + locations[0].city : '',
        website: business.website
      }"
      :social-links="getFooterSocialLinks()"
    />
  </MinisiteLayout>
</template>

<script setup>
import { computed, onMounted, nextTick } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import MinisiteLayout from '@/Layouts/MinisiteLayout.vue'
import FooterSection from '@/Minisites/Themes/Bold/Sections/FooterSection.vue'
import GLightbox from 'glightbox'
import 'glightbox/dist/css/glightbox.min.css'

const page = usePage()

const business = computed(() => page.props.business)
const theme = computed(() => page.props.theme)
const modules = computed(() => page.props.modules || [])
const images = computed(() => page.props.images || [])
const socialNetworks = computed(() => page.props.socialNetworks || [])
const branding = computed(() => page.props.branding || null)
const locations = computed(() => page.props.locations || [])

const isModuleEnabled = (moduleKey) => {
  if (!moduleKey) return true
  return modules.value.includes(moduleKey)
}

const navbarStyle = computed(() => {
  if (!theme.value?.css_variables?.colors) return {}
  const { primary, background } = theme.value.css_variables.colors
  const isDark = isColorDark(background || '#ffffff')
  return {
    backgroundColor: background || '#ffffff',
    borderBottom: `1px solid ${primary}40`,
    color: isDark ? '#ffffff' : (theme.value.css_variables.colors.text || '#1a1a1a'),
  }
})

const isColorDark = (hexColor) => {
  if (!hexColor || !hexColor.startsWith('#')) return false
  const hex = hexColor.replace('#', '')
  if (hex.length < 6) return false
  const r = parseInt(hex.substr(0, 2), 16)
  const g = parseInt(hex.substr(2, 2), 16)
  const b = parseInt(hex.substr(4, 2), 16)
  const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255
  return luminance < 0.5
}

const navbarTextColor = computed(() => {
  if (!theme.value?.css_variables?.colors) return '#1a1a1a'
  const { background } = theme.value.css_variables.colors
  return isColorDark(background || '#ffffff') ? '#ffffff' : '#1a1a1a'
})

const navbarTogglerIconFilter = computed(() => {
  if (!theme.value?.css_variables?.colors) return 'invert(0%)'
  const { background } = theme.value.css_variables.colors
  return isColorDark(background || '#ffffff') ? 'invert(100%)' : 'invert(0%)'
})

const getFooterSocialLinks = () => {
  if (!isModuleEnabled('socialmedia')) return []
  return socialNetworks.value.filter(sn => sn.show_on_footer)
}

let lightbox = null

const openGallery = (index) => {
  if (lightbox) {
    lightbox.openAt(index)
  }
}

onMounted(() => {
  nextTick(() => {
    const els = images.value.map(img => ({
      href: img.path,
      title: img.title || 'Galería',
      type: 'image'
    }))
    if (els.length > 0) {
      lightbox = GLightbox({
        elements: els,
        touchNavigation: true,
        loop: true,
        autoplayVideos: true,
      })
    }
  })
})
</script>

<style scoped>
.gallery-item {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.gallery-item:hover {
  transform: scale(1.05);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}
</style>
