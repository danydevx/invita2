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
          <h1 class="fw-bold" :style="{ color: 'var(--brand-primary)' }">Reservar cita</h1>
          <p class="text-muted">Selecciona fecha y horario</p>
        </div>

        <div class="row">
          <div class="col-lg-8">
            <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
              {{ $page.props.flash.success }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <div class="card">
              <div class="card-body">
                <form @submit.prevent="submit">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">Servicio *</label>
                      <select v-model="form.service_id" @change="onServiceChange" class="form-select" required>
                        <option value="">Seleccionar servicio...</option>
                        <option v-for="svc in services" :key="svc.id" :value="svc.id">
                          {{ svc.name }} - {{ formatPrice(svc.price) }} ({{ svc.duration_minutes }} min)
                        </option>
                      </select>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Ubicacion *</label>
                      <select v-model="form.location_id" class="form-select" required>
                        <option value="">Seleccionar ubicacion...</option>
                        <option v-for="loc in allLocations" :key="loc.id" :value="loc.id">
                          {{ loc.name }}
                        </option>
                      </select>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Fecha *</label>
                      <input type="date" v-model="form.appointment_date" @change="onDateChange" class="form-control" required :min="minDate" />
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Horario disponible *</label>
                      <select v-model="form.start_time" class="form-select" required :disabled="loadingSlots || availableSlots.length === 0">
                        <option value="">{{ loadingSlots ? 'Cargando horarios...' : (availableSlots.length === 0 ? 'No hay horarios disponibles' : 'Seleccionar horario...') }}</option>
                        <option v-for="slot in availableSlots" :key="slot.start_time" :value="slot.start_time" :disabled="!slot.available">
                          {{ slot.start_time }} - {{ slot.end_time }}
                          <template v-if="slot.available && slot.remaining_capacity < 999">({{ slot.remaining_capacity }} cupo(s))</template>
                          <template v-if="!slot.available">(ocupado)</template>
                        </option>
                      </select>
                    </div>

                    <div class="col-12"><hr></div>

                    <div class="col-md-4">
                      <label class="form-label">Tu nombre *</label>
                      <input type="text" v-model="form.customer_name" class="form-control" required />
                    </div>

                    <div class="col-md-4">
                      <label class="form-label">Tu email *</label>
                      <input type="email" v-model="form.customer_email" class="form-control" required />
                    </div>

                    <div class="col-md-4">
                      <label class="form-label">Tu telefono</label>
                      <input type="tel" v-model="form.customer_phone" class="form-control" />
                    </div>

                    <div class="col-12">
                      <label class="form-label">Notas adicionales</label>
                      <textarea v-model="form.notes" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="col-12">
                      <button type="submit" class="btn btn-primary btn-lg w-100" :disabled="sending">
                        <span v-if="sending">Confirmando...</span>
                        <span v-else>Confirmar reserva</span>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card bg-light">
              <div class="card-body">
                <h5 class="card-title">Informacion de tu reserva</h5>
                <p class="text-muted small">Complete el formulario para reservar su cita</p>
                <ul class="list-unstyled mb-0">
                  <li v-if="selectedService" class="mb-2">
                    <i class="bi bi-scissors me-2" :style="{ color: 'var(--brand-accent)' }"></i>{{ selectedService.name }}
                  </li>
                  <li v-if="selectedService" class="mb-2">
                    <i class="bi bi-clock me-2" :style="{ color: 'var(--brand-accent)' }"></i>{{ selectedService.duration_minutes }} minutos
                  </li>
                  <li v-if="selectedService" class="mb-2">
                    <i class="bi bi-currency-dollar me-2" :style="{ color: 'var(--brand-accent)' }"></i>{{ formatPrice(selectedService.price) }}
                  </li>
                </ul>
              </div>
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
        address: allLocations.length > 0 ? allLocations[0].address_line_1 + ', ' + allLocations[0].city : '',
        website: business.website
      }"
      :social-links="getFooterSocialLinks()"
    />
  </MinisiteLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import MinisiteLayout from '@/Layouts/MinisiteLayout.vue'
import FooterSection from '@/Minisites/Themes/Bold/Sections/FooterSection.vue'

const page = usePage()

const business = computed(() => page.props.business)
const theme = computed(() => page.props.theme)
const modules = computed(() => page.props.modules || [])
const services = computed(() => page.props.services || [])
const allLocations = computed(() => page.props.allLocations || [])
const availableSlots = computed(() => page.props.availableSlots || [])
const selectedService = computed(() => page.props.selectedService)
const socialNetworks = computed(() => page.props.socialNetworks || [])
const branding = computed(() => page.props.branding || null)

const sending = ref(false)
const loadingSlots = ref(false)

const minDate = computed(() => {
  return new Date().toISOString().split('T')[0]
})

const form = reactive({
  service_id: selectedService.value?.id || '',
  location_id: page.props.selectedLocation?.id || '',
  appointment_date: page.props.selectedDate || '',
  start_time: '',
  customer_name: '',
  customer_email: '',
  customer_phone: '',
  notes: '',
})

const isModuleEnabled = (moduleKey) => {
  if (!moduleKey) return true
  return modules.value.includes(moduleKey)
}

const cardHoverClass = computed(() => 'animate-lift')

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

const formatPrice = (price) => {
  if (!price && price !== 0) return '-'
  return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(price)
}

const onServiceChange = () => {
  router.get(`/b/${business.value.slug}/book`, {
    service: form.service_id,
    location: form.location_id,
    date: form.appointment_date,
  }, { preserveState: true })
}

const onDateChange = () => {
  if (!form.service_id) return
  loadingSlots.value = true
  router.get(`/b/${business.value.slug}/book`, {
    service: form.service_id,
    location: form.location_id,
    date: form.appointment_date,
  }, {
    preserveState: true,
    onFinish: () => {
      loadingSlots.value = false
    },
  })
}

const submit = () => {
  sending.value = true
  router.post(`/b/${business.value.slug}/book`, form, {
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
