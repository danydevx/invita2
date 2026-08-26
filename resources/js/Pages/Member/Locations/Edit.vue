<template>
  <MemberLayout>
    <Head :title="`Editar Ubicacion - ${listing.name}`" />

    <PageHeader
      :title="'Editar Ubicacion'"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing.id}/locations`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit" id="location-form">
          <div class="row g-3">
            <div class="col-12 col-md-8">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="text"
                    id="location-name"
                    v-model="form.name"
                    class="form-control"
                    :class="{ 'is-invalid': errors.name }"
                    placeholder=" "
                  />
                  <label for="location-name">Nombre <strong class="text-danger">*</strong></label>
                  <div v-if="errors.name" class="invalid-feedback">{{ errors.name }}</div>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="form-group">
                <div class="form-check form-switch mt-3 pt-3">
                  <input
                    type="checkbox"
                    id="location-primary"
                    v-model="form.is_primary"
                    class="form-check-input"
                  />
                  <label class="form-check-label" for="location-primary">Ubicacion principal</label>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="text"
                    id="location-address-1"
                    v-model="form.address_line_1"
                    class="form-control"
                    :class="{ 'is-invalid': errors.address_line_1 }"
                    placeholder=" "
                  />
                  <label for="location-address-1">Direccion linea 1 <strong class="text-danger">*</strong></label>
                  <div v-if="errors.address_line_1" class="invalid-feedback">{{ errors.address_line_1 }}</div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="text"
                    id="location-address-2"
                    v-model="form.address_line_2"
                    class="form-control"
                    placeholder=" "
                  />
                  <label for="location-address-2">Direccion linea 2</label>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="text"
                    id="location-city"
                    v-model="form.city"
                    class="form-control"
                    :class="{ 'is-invalid': errors.city }"
                    placeholder=" "
                  />
                  <label for="location-city">Ciudad / Colonia <strong class="text-danger">*</strong></label>
                  <div v-if="errors.city" class="invalid-feedback">{{ errors.city }}</div>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <LocationSelector
                ref="locationSelectorRef"
                v-model="locationData"
                :state-error="errors.state_code"
                :municipality-error="errors.municipality"
                required
                @state-changed="onStateChanged"
                @municipality-changed="onMunicipalityChanged"
                @location-updated="onLocationUpdated"
              />
            </div>

            <div class="col-12 col-md-4">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="text"
                    id="location-postal"
                    v-model="form.postal_code"
                    class="form-control"
                    placeholder=" "
                  />
                  <label for="location-postal">Codigo Postal</label>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="tel"
                    id="location-phone"
                    v-model="form.phone"
                    class="form-control"
                    placeholder=" "
                  />
                  <label for="location-phone">Telefono</label>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="email"
                    id="location-email"
                    v-model="form.email"
                    class="form-control"
                    :class="{ 'is-invalid': errors.email }"
                    placeholder=" "
                  />
                  <label for="location-email">Email</label>
                  <div v-if="errors.email" class="invalid-feedback">{{ errors.email }}</div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <MapPicker
                label="Ubicacion en el mapa"
                :lat="form.latitude"
                :lng="form.longitude"
                @update:lat="form.latitude = $event"
                @update:lng="form.longitude = $event"
                @reverse-geocoded="onReverseGeocoded"
              />
            </div>

            <div class="col-12">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="url"
                    id="location-directions"
                    v-model="form.directions_url"
                    class="form-control"
                    placeholder=" "
                  />
                  <label for="location-directions">Como llegar (URL de Google Maps)</label>
                </div>
              </div>
            </div>

            <div class="col-12">
              <FieldImage
                id="location-image"
                label="Imagen de la ubicacion"
                v-model="locationImage"
                :initialPreview="location.image"
                :maxSizeMb="2"
                accept="image/jpeg,image/png"
              />
              <small class="text-muted">JPG o PNG, max 2MB. Opcional.</small>
            </div>

            <div class="col-12 col-md-4">
              <div class="form-check form-switch mt-3 pt-3">
                <input
                  type="checkbox"
                  id="location-active"
                  v-model="form.is_active"
                  class="form-check-input"
                />
                <label class="form-check-label" for="location-active">Ubicacion activa</label>
              </div>
            </div>
          </div>

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              {{ sending ? 'Actualizando...' : 'Actualizar Ubicacion' }}
            </button>
            <Link :href="`/member/listings/${listing.id}/locations`" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import MapPicker from '@/Components/MapPicker.vue'
import LocationSelector from '@/Components/LocationSelector.vue'
import FieldImage from '@/Components/Fields/FieldImage.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const location = computed(() => page.props.location)

const locationData = ref({
  state_code: location.value.state_code || '',
  municipality: location.value.municipality || '',
})

const errors = reactive({
  name: '',
  address_line_1: '',
  city: '',
  email: '',
  state_code: '',
  municipality: '',
  image: '',
})

const sending = ref(false)
const locationImage = ref(location.value.image || null)
const removeImage = ref(false)
const locationSelectorRef = ref(null)

const form = reactive({
  name: location.value.name || '',
  address_line_1: location.value.address_line_1 || '',
  address_line_2: location.value.address_line_2 || '',
  city: location.value.city || '',
  postal_code: location.value.postal_code || '',
  phone: location.value.phone || '',
  email: location.value.email || '',
  directions_url: location.value.directions_url || '',
  latitude: location.value.latitude || '',
  longitude: location.value.longitude || '',
  is_primary: !!location.value.is_primary,
  is_active: !!location.value.is_active,
})

const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Ubicaciones', href: `/member/listings/${listing.value.id}/locations` },
  { label: location.value?.name || 'Editar' },
])

const onStateChanged = ({ lat, lng }) => {
  if (lat && lng) {
    form.latitude = parseFloat(lat).toFixed(7)
    form.longitude = parseFloat(lng).toFixed(7)
  }
}

const onMunicipalityChanged = ({ lat, lng }) => {
  if (lat && lng) {
    form.latitude = parseFloat(lat).toFixed(7)
    form.longitude = parseFloat(lng).toFixed(7)
  }
}

const onReverseGeocoded = (locationData) => {
  if (locationSelectorRef.value) {
    locationSelectorRef.value.handleReverseGeocoded(locationData)
  }
}

const onLocationUpdated = (locationData) => {
  if (locationData.address) {
    form.address_line_1 = [locationData.address, locationData.number].filter(Boolean).join(' ')
  }
  if (locationData.colony) {
    form.city = locationData.colony
  }
  if (locationData.postal_code) {
    form.postal_code = locationData.postal_code
  }
}

watch(locationImage, (newVal, oldVal) => {
  if (oldVal && oldVal instanceof File && (newVal === null || (Array.isArray(newVal) && newVal.length === 0))) {
    removeImage.value = true
  }
})

const validateForm = () => {
  let isValid = true

  errors.name = ''
  errors.address_line_1 = ''
  errors.city = ''
  errors.email = ''
  errors.state_code = ''
  errors.municipality = ''
  errors.image = ''

  if (!form.name || form.name.trim() === '') {
    errors.name = 'El nombre es obligatorio.'
    isValid = false
  } else if (form.name.length > 150) {
    errors.name = 'El nombre no puede tener más de 150 caracteres.'
    isValid = false
  }

  if (!form.address_line_1 || form.address_line_1.trim() === '') {
    errors.address_line_1 = 'La dirección es obligatoria.'
    isValid = false
  } else if (form.address_line_1.length > 255) {
    errors.address_line_1 = 'La dirección no puede tener más de 255 caracteres.'
    isValid = false
  }

  if (!form.city || form.city.trim() === '') {
    errors.city = 'La ciudad es obligatoria.'
    isValid = false
  } else if (form.city.length > 100) {
    errors.city = 'La ciudad no puede tener más de 100 caracteres.'
    isValid = false
  }

  if (form.email && form.email.trim() !== '') {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(form.email)) {
      errors.email = 'El email no es válido.'
      isValid = false
    } else if (form.email.length > 150) {
      errors.email = 'El email no puede tener más de 150 caracteres.'
      isValid = false
    }
  }

  return isValid
}

const submit = () => {
  if (!validateForm()) {
    toast.warning('Por favor completa los campos requeridos')
    return
  }

  sending.value = true

  const formData = new FormData()
  formData.append('name', form.name)
  formData.append('address_line_1', form.address_line_1)
  formData.append('address_line_2', form.address_line_2 || '')
  formData.append('city', form.city)
  formData.append('state', '')
  formData.append('state_code', locationData.value.state_code || '')
  formData.append('municipality', locationData.value.municipality || '')
  formData.append('postal_code', form.postal_code || '')
  formData.append('country', 'MX')
  formData.append('phone', form.phone || '')
  formData.append('email', form.email || '')
  formData.append('latitude', form.latitude || '')
  formData.append('longitude', form.longitude || '')
  formData.append('directions_url', form.directions_url || '')
  formData.append('is_primary', form.is_primary ? '1' : '0')
  formData.append('is_active', form.is_active ? '1' : '0')
  formData.append('_method', 'PUT')

  if (locationImage.value instanceof File) {
    formData.append('image', locationImage.value)
  }
  if (removeImage.value) {
    formData.append('remove_image', '1')
  }

  router.post(`/member/listings/${listing.value.id}/locations/${location.value.id}`, formData, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Ubicacion actualizada correctamente')
      sending.value = false
    },
    onError: (serverErrors) => {
      sending.value = false
      const errorMessages = []
      if (serverErrors.name) { errors.name = serverErrors.name; errorMessages.push(serverErrors.name) }
      if (serverErrors.address_line_1) { errors.address_line_1 = serverErrors.address_line_1; errorMessages.push(serverErrors.address_line_1) }
      if (serverErrors.city) { errors.city = serverErrors.city; errorMessages.push(serverErrors.city) }
      if (serverErrors.email) { errors.email = serverErrors.email; errorMessages.push(serverErrors.email) }
      if (serverErrors.state_code) { errors.state_code = serverErrors.state_code; errorMessages.push(serverErrors.state_code) }
      if (serverErrors.municipality) { errors.municipality = serverErrors.municipality; errorMessages.push(serverErrors.municipality) }
      if (serverErrors.image) { errors.image = serverErrors.image; errorMessages.push(serverErrors.image) }
      if (errorMessages.length > 0) {
        toast.warning('Por favor completa los campos requeridos: ' + errorMessages.join(', '))
      }
    },
  })
}
</script>
