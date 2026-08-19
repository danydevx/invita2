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
          <h1 class="fw-bold" :style="{ color: 'var(--brand-primary)' }">Contacto</h1>
          <p class="text-muted">{{ business.name }}</p>
        </div>

        <div class="row">
          <div class="col-md-8">
            <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
              {{ $page.props.flash.success }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <div class="card">
              <div class="card-body">
                <h5 class="card-title mb-4">Envianos un mensaje</h5>
                <form @submit.prevent="submit">
                  <div class="mb-3">
                    <label class="form-label">Tu nombre *</label>
                    <input type="text" v-model="form.name" class="form-control" required />
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Tu email *</label>
                    <input type="email" v-model="form.email" class="form-control" required />
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Tu telefono</label>
                    <input type="tel" v-model="form.phone" class="form-control" />
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Mensaje *</label>
                    <textarea v-model="form.message" class="form-control" rows="4" required></textarea>
                  </div>

                  <button type="submit" class="btn btn-primary" :disabled="sending">
                    <span v-if="sending">Enviando...</span>
                    <span v-else>Enviar mensaje</span>
                  </button>
                </form>
              </div>
            </div>
          </div>

          <div class="col-md-4 mt-4 mt-md-0">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Informacion de contacto</h5>
                <ul class="list-unstyled mb-0">
                  <li v-if="business.phone" class="mb-2">
                    <i class="bi bi-telephone me-2" :style="{ color: 'var(--brand-accent)' }"></i>{{ business.phone }}
                  </li>
                  <li v-if="business.email" class="mb-2">
                    <i class="bi bi-envelope me-2" :style="{ color: 'var(--brand-accent)' }"></i>{{ business.email }}
                  </li>
                </ul>
              </div>
            </div>

            <div v-if="business.phone || business.email" class="mt-3">
              <a v-if="business.phone" :href="`tel:${business.phone}`" class="btn btn-outline-primary w-100 mb-2">
                <i class="bi bi-telephone me-2"></i>Llamar
              </a>
              <a v-if="business.email" :href="`mailto:${business.email}`" class="btn btn-outline-secondary w-100">
                <i class="bi bi-envelope me-2"></i>Enviar email
              </a>
            </div>
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
import { computed, reactive } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import MinisiteLayout from '@/Layouts/MinisiteLayout.vue'
import FooterSection from '@/Minisites/Themes/Bold/Sections/FooterSection.vue'

const page = usePage()

const business = computed(() => page.props.business)
const theme = computed(() => page.props.theme)
const modules = computed(() => page.props.modules || [])
const socialNetworks = computed(() => page.props.socialNetworks || [])
const branding = computed(() => page.props.branding || null)
const locations = computed(() => page.props.locations || [])

const form = reactive({
  name: '',
  email: '',
  phone: '',
  message: '',
})

const sending = computed(() => false)

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

const submit = () => {
  router.post(`/b/${business.value.slug}/contact`, form, {
    onSuccess: () => {
      form.name = ''
      form.email = ''
      form.phone = ''
      form.message = ''
    }
  })
}
</script>
