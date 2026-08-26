<template>
  <MemberLayout>
    <Head :title="`Editar Promocion - ${listing.name}`" />

    <PageHeader
      title="Editar Promocion"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing.id}/promotions`"
    />

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ $page.props.flash.error }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

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
                :initialPreview="initialPreview"
                :maxFiles="1"
                :maxSizeMb="2"
                accept="image/jpeg"
                @update:keep="onImageKeepChange"
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

            <div class="col-12">
              <div v-if="promotion.qr_code_path" class="card bg-light p-3">
                <div class="d-flex align-items-center gap-3">
                  <img :src="promotion.qr_code_path" alt="QR Code" class="rounded" style="max-height: 120px;">
                  <div>
                    <strong>Codigo QR</strong>
                    <p class="text-muted small mb-2">Escanealo para verificar la promocion.</p>
                    <button
                      type="button"
                      class="btn btn-sm btn-outline-primary"
                      :disabled="regenerating"
                      @click="regenerateQr"
                    >
                      <i class="bi bi-arrow-clockwise me-1"></i>
                      {{ regenerating ? 'Regenerando...' : 'Regenerar QR' }}
                    </button>
                  </div>
                </div>
              </div>
              <div v-else-if="form.coupon_code" class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                El codigo QR se generara automaticamente al guardar.
              </div>
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

          <div class="d-flex gap-2">
            <FormActions
              :submitText="'Guardar'"
              :submittingText="'Guardando...'"
              :cancelHref="`/member/listings/${listing.id}/promotions`"
              :sending="sending"
            />
            <button
              type="button"
              class="btn btn-outline-danger"
              @click="deletePromotion"
            >
              <i class="bi bi-trash me-1"></i>
              Eliminar
            </button>
          </div>
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
const promotion = computed(() => page.props.promotion)
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Promociones', href: `/member/listings/${listing.value.id}/promotions` },
  { label: promotion.value?.name || 'Editar' },
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
const keepImage = ref(true)
const regenerating = ref(false)
const initialPreview = computed(() => promotion.value?.image ? promotion.value.image : '')

const onImageKeepChange = (value) => {
  keepImage.value = value
}

const form = reactive({
  name: promotion.value.name,
  description: promotion.value.description || '',
  business_location_id: promotion.value.business_location_id,
  regular_price: promotion.value.regular_price || '',
  promotion_price: promotion.value.promotion_price || '',
  coupon_code: promotion.value.coupon_code || '',
  starts_at: promotion.value.starts_at || '',
  expires_at: promotion.value.expires_at || '',
  sort_order: promotion.value.sort_order || 0,
  is_active: promotion.value.is_active || false,
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
  formData.append('_method', 'PUT')

  if (mainImage.value instanceof File) {
    formData.append('image', mainImage.value)
  } else if (!keepImage.value && promotion.value?.image) {
    formData.append('remove_image', '1')
  }

  router.post(`/member/listings/${listing.value.id}/promotions/${promotion.value.id}`, formData, {
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

const regenerateQr = () => {
  if (!confirm('Regenerar el codigo QR?')) return
  regenerating.value = true
  router.post(`/member/listings/${listing.value.id}/promotions/${promotion.value.id}/regenerate-qr`, {
    onSuccess: () => {
      regenerating.value = false
      console.log('QR regenerated, reloading...')
      window.location.reload()
    },
    onError: (errors) => {
      regenerating.value = false
      console.log('Error regenerating:', errors)
      alert('Error al regenerar el codigo QR')
    },
  })
}

const deletePromotion = () => {
  if (!confirm(`Eliminar la promocion "${promotion.value.name}"?`)) return
  router.delete(`/member/listings/${listing.value.id}/promotions/${promotion.value.id}`, {
    onSuccess: () => {
      window.location.href = `/member/listings/${listing.value.id}/promotions`
    },
  })
}
</script>
