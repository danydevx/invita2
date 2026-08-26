<template>
  <MemberLayout>
    <Head :title="`Editar Servicio - ${listing?.name || ''}`" />

    <PageHeader
      :title="'Editar Servicio'"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/services`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12 col-md-8">
              <FieldText
                id="service-name"
                label="Nombre"
                v-model="form.name"
                :formError="errors.name"
                required
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="service-slug"
                label="Slug"
                v-model="form.slug"
                :formError="errors.slug"
              />
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
              <div class="d-flex align-items-end gap-2">
                <div class="flex-grow-1">
                  <FieldSelect
                    id="service-category"
                    label="Categoria"
                    v-model="form.category_id"
                    :options="categoryOptions"
                    :formError="errors.category_id"
                  />
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" @click="openCategoryModal">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
            </div>

            <div class="col-12 col-md-3">
              <FieldNumber
                id="service-duration"
                label="Duracion (minutos)"
                v-model="form.duration_minutes"
                :formError="errors.duration_minutes"
                required
              />
            </div>

            <div class="col-12 col-md-3">
              <FieldNumber
                id="service-price"
                label="Precio"
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
                :initialPreview="initialPreview"
                :maxFiles="1"
                :maxSizeMb="2"
                accept="image/jpeg"
                @update:keep="onImageKeepChange"
              />
              <small class="text-muted">JPG, max 2MB</small>
            </div>

            <div class="col-12">
              <ServiceImageUpload
                :businessId="listing?.id"
                :serviceId="service?.id"
                :images="props.serviceImages || []"
                :maxFiles="10"
                :maxSizeMb="2"
                label="Galería de imágenes"
                @updated="reloadPage"
              />
            </div>
          </div>

          <FormActions
            submitText="Actualizar Servicio"
            submittingText="Actualizando..."
            :cancelHref="`/member/listings/${listing?.id}/services`"
            :sending="sending"
          />
        </form>
      </div>
    </div>

    <div ref="categoryModalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nueva Categoria</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="createCategory">
            <div class="modal-body">
              <div class="mb-3">
                <FieldText
                  id="category-name"
                  label="Nombre"
                  v-model="categoryForm.name"
                  required
                />
              </div>
              <div class="mb-3">
                <FieldTextarea
                  id="category-description"
                  label="Descripcion"
                  v-model="categoryForm.description"
                  :rows="2"
                />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="categorySending">
                {{ categorySending ? 'Creando...' : 'Crear Categoria' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, ref, reactive, onMounted, nextTick } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'
import FieldImage from '@/Components/Fields/FieldImage.vue'
import ServiceImageUpload from '@/Components/Fields/ServiceImageUpload.vue'
import FormActions from '@/Components/FormActions.vue'

const props = defineProps({
  listing: { type: Object, required: true },
  service: { type: Object, required: true },
  locations: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] },
  serviceImages: { type: Array, default: () => [] },
})

const page = usePage()
const listing = computed(() => props.listing)
const service = computed(() => props.service)

const sending = ref(false)
const mainImage = ref(null)
const keepImage = ref(true)
const initialPreview = computed(() => service.value?.image ? `/storage/${service.value.image}` : '')

const onImageKeepChange = (value) => {
  keepImage.value = value
}

const locationOptions = computed(() => [
  { value: '', label: 'Todas las ubicaciones' },
  ...(props.locations || []).map(l => ({ value: l.id, label: l.name }))
])

const categoryOptions = computed(() => [
  { value: '', label: 'Sin categoria' },
  ...(props.categories || []).map(c => ({ value: c.id, label: c.name }))
])

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
  category_id: '',
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
  errors.category_id = ''
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

onMounted(() => {
  form.name = service.value?.name || ''
  form.slug = service.value?.slug || ''
  form.description = service.value?.description || ''
  form.duration_minutes = service.value?.duration_minutes || 30
  form.price = service.value?.price || ''
  form.deposit_required = !!service.value?.deposit_required
  form.deposit_amount = service.value?.deposit_amount || ''
  form.allows_online_booking = !!service.value?.allows_online_booking
  form.whatsapp_contact = service.value?.whatsapp_contact || ''
  form.is_active = !!service.value?.is_active
  form.sort_order = service.value?.sort_order ?? 0
  form.business_location_id = service.value?.business_location_id || ''
  form.category_id = service.value?.category_id || ''
  categoryModal = new Modal(categoryModalElement.value)
})

const categoryModalElement = ref(null)
let categoryModal = null
const categorySending = ref(false)
const categoryForm = reactive({
  name: '',
  description: '',
})

const openCategoryModal = () => {
  categoryForm.name = ''
  categoryForm.description = ''
  nextTick(() => categoryModal.show())
}

const createCategory = () => {
  if (!categoryForm.name.trim()) {
    toast.warning('El nombre de la categoria es requerido')
    return
  }
  categorySending.value = true

  router.post(`/member/listings/${listing.value.id}/service-categories`, categoryForm, {
    preserveScroll: true,
    onSuccess: () => {
      categoryModal.hide()
      toast.success('Categoria creada exitosamente')
      categoryForm.name = ''
      categoryForm.description = ''
    },
    onError: (errors) => {
      const firstError = Object.values(errors)[0]
      if (firstError) {
        toast.error(firstError)
      } else {
        toast.error('Error al crear la categoria')
      }
    },
    onFinish: () => {
      categorySending.value = false
    },
  })
}

const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Servicios', href: `/member/listings/${listing.value.id}/services` },
  { label: service.value?.name || 'Editar' },
])

const submit = () => {
  if (!validateForm()) {
    toast.warning('Por favor completa los campos requeridos')
    return
  }

  sending.value = true
  const formData = new FormData()
  formData.append('_method', 'PUT')

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
  } else if (!keepImage.value && service.value?.image) {
    formData.append('_remove_image', '1')
  }

  router.post(`/member/listings/${listing.value.id}/services/${service.value.id}`, formData, {
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

const reloadPage = () => {
  router.reload({ preserveScroll: true })
}
</script>
