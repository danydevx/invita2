<template>
  <MemberLayout>
    <Head :title="`Nuevo Servicio - ${listing.name}`" />

    <PageHeader
      :title="'Nuevo Servicio'"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing.id}/services`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12 col-md-8">
              <FieldText
                id="service-name"
                label="Nombre"
                placeholder="Corte de cabello"
                v-model="form.name"
                :formError="errors.name"
                required
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="service-slug"
                label="Slug"
                placeholder="corte-de-cabello"
                v-model="form.slug"
                :formError="errors.slug"
              />
              <small class="text-muted">Se genera automaticamente si se deja vacio.</small>
            </div>

            <div class="col-12">
              <FieldTextarea
                id="service-description"
                label="Descripcion"
                v-model="form.description"
                :formError="errors.description"
                :rows="3"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldSelect
                id="service-location"
                label="Ubicacion"
                v-model="form.business_location_id"
                :options="locationOptions"
                :formError="errors.business_location_id"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldSelect
                id="service-category"
                label="Categoria"
                v-model="form.category_id"
                :options="categoryOptions"
                :formError="errors.category_id"
              />
            </div>

            <div class="col-12 col-md-3">
              <FieldNumber
                id="service-duration"
                label="Duracion (minutos)"
                placeholder="30"
                v-model="form.duration_minutes"
                :formError="errors.duration_minutes"
                required
              />
            </div>

            <div class="col-12 col-md-3">
              <FieldNumber
                id="service-price"
                label="Precio"
                placeholder="0.00"
                v-model="form.price"
                :formError="errors.price"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldSwitch
                id="service-deposit-required"
                label="Requiere deposito"
                v-model="form.deposit_required"
              />
            </div>

            <div v-if="form.deposit_required" class="col-12 col-md-4">
              <FieldNumber
                id="service-deposit-amount"
                label="Monto deposito"
                placeholder="0.00"
                v-model="form.deposit_amount"
                :formError="errors.deposit_amount"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldSwitch
                id="service-online-booking"
                label="Permite reserva online"
                v-model="form.allows_online_booking"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldPhone
                id="service-whatsapp"
                label="WhatsApp"
                placeholder="55 1234 5678"
                v-model="form.whatsapp_contact"
                :formError="errors.whatsapp_contact"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldSwitch
                id="service-active"
                label="Servicio activo"
                v-model="form.is_active"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldNumber
                id="service-sort-order"
                label="Orden"
                placeholder="0"
                v-model="form.sort_order"
                :formError="errors.sort_order"
              />
              <small class="text-muted">Menor numero aparece primero.</small>
            </div>

            <div class="col-12">
              <FieldImage
                id="service-image"
                label="Imagen principal"
                v-model="mainImage"
                :maxFiles="1"
                :maxSizeMb="2"
                accept="image/jpeg"
              />
              <small class="text-muted">JPG, max 2MB</small>
            </div>

            <div class="col-12">
              <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-1"></i>
                La galería de imágenes estará disponible después de crear el servicio.
              </div>
            </div>
          </div>

          <FormActions
            submitText="Crear Servicio"
            submittingText="Creando..."
            :cancelHref="`/member/listings/${listing.id}/services`"
            :sending="sending"
          />
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
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'
import FieldImage from '@/Components/Fields/FieldImage.vue'
import FormActions from '@/Components/FormActions.vue'

const props = defineProps({
  listing: { type: Object, required: true },
  locations: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] },
})

const page = usePage()
const listing = computed(() => page.props.listing)

const locationOptions = computed(() => [
  { value: '', label: 'Todas las ubicaciones' },
  ...props.locations.map(l => ({ value: l.id, label: l.name }))
])

const categoryOptions = computed(() => [
  { value: '', label: 'Sin categoria' },
  ...props.categories.map(c => ({ value: c.id, label: c.name }))
])

const mainImage = ref(null)
const sending = ref(false)

const form = reactive({
  name: '',
  slug: '',
  description: '',
  duration_minutes: 30,
  price: '',
  deposit_required: false,
  deposit_amount: '',
  allows_online_booking: true,
  whatsapp_contact: '',
  is_active: true,
  sort_order: 0,
  business_location_id: '',
  category_id: '',
})

const errors = reactive({
  name: '',
  slug: '',
  description: '',
  duration_minutes: '',
  price: '',
  deposit_amount: '',
  whatsapp_contact: '',
  business_location_id: '',
  sort_order: '',
})

const validateForm = () => {
  let isValid = true

  errors.name = ''
  errors.slug = ''
  errors.description = ''
  errors.duration_minutes = ''
  errors.price = ''
  errors.deposit_amount = ''
  errors.whatsapp_contact = ''
  errors.business_location_id = ''
  errors.sort_order = ''

  if (!form.name || form.name.trim() === '') {
    errors.name = 'El nombre es obligatorio.'
    isValid = false
  } else if (form.name.length > 150) {
    errors.name = 'El nombre no puede tener mas de 150 caracteres.'
    isValid = false
  }

  if (!form.duration_minutes || form.duration_minutes < 1) {
    errors.duration_minutes = 'La duracion minima es 1 minuto.'
    isValid = false
  }

  if (form.price && isNaN(parseFloat(form.price))) {
    errors.price = 'El precio debe ser un numero valido.'
    isValid = false
  }

  if (form.deposit_required && form.deposit_amount && isNaN(parseFloat(form.deposit_amount))) {
    errors.deposit_amount = 'El monto del deposito debe ser un numero valido.'
    isValid = false
  }

  return isValid
}

watch(() => form.name, (val) => {
  if (val && !form.slug) {
    form.slug = val.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '')
  }
})

const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Servicios', href: `/member/listings/${listing.value.id}/services` },
  { label: 'Nuevo' },
])

const submit = () => {
  if (!validateForm()) {
    toast.warning('Por favor completa los campos requeridos')
    return
  }

  const formData = new FormData()

  Object.keys(form).forEach(key => {
    const val = form[key]
    if (val !== null && val !== '') {
      if (typeof val === 'boolean') {
        formData.append(key, val ? '1' : '0')
      } else {
        formData.append(key, val)
      }
    }
  })

  if (mainImage.value instanceof File) {
    formData.append('image', mainImage.value)
  }

  sending.value = true
  router.post(`/member/listings/${listing.value.id}/services`, formData, {
    preserveScroll: true,
    onSuccess: () => {
      sending.value = false
    },
    onError: (errs) => {
      sending.value = false
      const errorMessages = Object.values(errs).flat()
      if (errorMessages.length > 0) {
        toast.warning('Por favor completa los campos requeridos')
      }
    },
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
