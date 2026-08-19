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

    <div class="py-5 text-center" :style="{ backgroundColor: 'var(--brand-background, #fff)', minHeight: '60vh', display: 'flex', alignItems: 'center' }">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6">
            <div class="mb-4">
              <i class="bi bi-check-circle-fill" :style="{ fontSize: '5rem', color: 'var(--brand-accent)' }"></i>
            </div>
            <h1 class="fw-bold mb-3" :style="{ color: 'var(--brand-primary)' }">Reserva confirmada!</h1>
            <p class="lead text-muted mb-4">Tu cita ha sido reservada exitosamente. Te enviaremos un email de confirmacion.</p>
            <Link :href="`/b/${business.slug}`" class="btn btn-primary btn-lg">
              <i class="bi bi-house me-2"></i>Volver al negocio
            </Link>
          </div>
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
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import MinisiteLayout from '@/Layouts/MinisiteLayout.vue'
import FooterSection from '@/Minisites/Themes/Bold/Sections/FooterSection.vue'

const page = usePage()

const business = computed(() => page.props.business)
const theme = computed(() => page.props.theme)
const modules = computed(() => page.props.modules || [])
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
</script>
