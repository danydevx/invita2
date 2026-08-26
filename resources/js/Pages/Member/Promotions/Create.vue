<template>
  <MemberLayout>
    <Head :title="`Nueva Promocion - ${listing.name}`" />

    <PageHeader
      title="Nueva Promocion"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing.id}/promotions`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <FieldText
                id="promotion-name"
                label="Nombre"
                v-model="form.name"
                :formError="errors.name"
                required
              />
            </div>

            <div class="col-md-6">
              <FieldSelect
                id="promotion-location"
                label="Ubicacion"
                v-model="form.business_location_id"
              >
                <option :value="null">Todas las ubicaciones</option>
                <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
              </FieldSelect>
            </div>

            <div class="col-12">
              <FieldTextarea
                id="promotion-description"
                label="Descripcion"
                v-model="form.description"
                :rows="3"
              />
            </div>

            <div class="col-12">
              <FieldImage
                id="promotion-image"
                label="Imagen de la promocion"
                v-model="mainImage"
                :maxFiles="1"
                :maxSizeMb="2"
                accept="image/jpeg"
              />
              <small class="text-muted">JPG, max 2MB</small>
            </div>

            <div class="col-md-4">
              <FieldNumber
                id="promotion-regular-price"
                label="Precio Regular"
                v-model="form.regular_price"
              />
            </div>

            <div class="col-md-4">
              <FieldNumber
                id="promotion-price"
                label="Precio Promo"
                v-model="form.promotion_price"
              />
            </div>

            <div class="col-md-4">
              <FieldText
                id="promotion-coupon"
                label="Codigo de Cupon"
                v-model="form.coupon_code"
              />
            </div>

            <div class="col-md-6">
              <FieldDate
                id="promotion-starts"
                label="Fecha de Inicio"
                v-model="form.starts_at"
              />
            </div>

            <div class="col-md-6">
              <FieldDate
                id="promotion-expires"
                label="Fecha de Vencimiento"
                v-model="form.expires_at"
              />
            </div>

            <div class="col-md-4">
              <FieldNumber
                id="promotion-sort"
                label="Orden"
                v-model="form.sort_order"
              />
              <small class="text-muted">Menor numero aparece primero.</small>
            </div>

            <div class="col-md-8 d-flex align-items-end">
              <FieldSwitch
                id="promotion-active"
                label="Activo"
                v-model="form.is_active"
              />
            </div>
          </div>

          <FormActions :submitText="'Crear Promocion'" :submittingText="'Creando...'" :cancelHref="`/member/listings/${listing.id}/promotions`" :sending="sending" />
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldDate from '@/Components/Fields/FieldDate.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldImage from '@/Components/Fields/FieldImage.vue'
import FormActions from '@/Components/FormActions.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Promociones', href: `/member/listings/${listing.value.id}/promotions` },
  { label: 'Nueva' },
])
const locations = computed(() => page.props.locations || [])

const errors = reactive({
  name: '',
  description: '',
  regular_price: '',
  promotion_price: '',
  starts_at: '',
  expires_at: '',
  business_location_id: '',
})

const validateForm = () => {
  let isValid = true

  errors.name = ''
  errors.description = ''
  errors.regular_price = ''
  errors.promotion_price = ''
  errors.starts_at = ''
  errors.expires_at = ''
  errors.business_location_id = ''

  if (!form.name || form.name.trim() === '') {
    errors.name = 'El nombre es obligatorio.'
    isValid = false
  }

  if (form.regular_price && isNaN(parseFloat(form.regular_price))) {
    errors.regular_price = 'El precio regular debe ser un numero valido.'
    isValid = false
  }

  if (form.promotion_price && isNaN(parseFloat(form.promotion_price))) {
    errors.promotion_price = 'El precio promo debe ser un numero valido.'
    isValid = false
  }

  if (form.starts_at && form.expires_at) {
    const start = new Date(form.starts_at)
    const expires = new Date(form.expires_at)
    if (expires <= start) {
      errors.expires_at = 'La fecha de vencimiento debe ser posterior a la de inicio.'
      isValid = false
    }
  }

  return isValid
}

const sending = ref(false)
const mainImage = ref(null)

const form = reactive({
  name: '',
  description: '',
  business_location_id: null,
  regular_price: '',
  promotion_price: '',
  coupon_code: '',
  starts_at: '',
  expires_at: '',
  sort_order: 0,
  is_active: true,
})

const submit = () => {
  if (!validateForm()) {
    toast.warning('Por favor completa los campos requeridos')
    return
  }

  sending.value = true
  const formData = new FormData()
  formData.append('name', form.name)
  formData.append('description', form.description || '')
  formData.append('business_location_id', form.business_location_id || '')
  formData.append('regular_price', form.regular_price || '')
  formData.append('promotion_price', form.promotion_price || '')
  formData.append('coupon_code', form.coupon_code || '')
  formData.append('starts_at', form.starts_at || '')
  formData.append('expires_at', form.expires_at || '')
  formData.append('sort_order', form.sort_order || 0)
  formData.append('is_active', form.is_active ? '1' : '0')

  if (mainImage.value) {
    formData.append('image', mainImage.value)
  }

  router.post(`/member/listings/${listing.value.id}/promotions`, formData, {
    preserveScroll: true,
    onSuccess: () => {
      sending.value = false
    },
    onError: (errs) => {
      sending.value = false
      Object.keys(errs).forEach(key => {
        if (key in errors) {
          errors[key] = errs[key]
        }
      })
      toast.warning('Por favor completa los campos requeridos')
    },
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
