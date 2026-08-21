<template>
  <AdminLayout>
    <Head :title="`Editar Servicio - ${listing?.name || ''}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link :href="`/admin/listings/${listing?.id}/services`" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
        <h1 class="h4 mb-1 mt-1">Editar Servicio</h1>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3 mb-3">
            <div class="col-12 col-md-8">
              <FieldText
                id="service-name"
                label="Nombre"
                v-model="form.name"
                :formError="errors.name"
                placeholder="Nombre del servicio"
                required
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="service-slug"
                label="Slug"
                v-model="form.slug"
                :formError="errors.slug"
                placeholder="nombre-servicio"
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
                :formError="errors.business_location_id"
              >
                <option value="">Todas las ubicaciones</option>
                <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
              </FieldSelect>
            </div>

            <div class="col-12 col-md-6">
              <FieldSelect
                id="service-category"
                label="Categoria"
                v-model="form.category_id"
                :formError="errors.category_id"
              >
                <option value="">Sin categoria</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </FieldSelect>
            </div>

            <div class="col-12 col-md-3">
              <FieldNumber
                id="service-duration"
                label="Duracion (minutos)"
                v-model="form.duration_minutes"
                :formError="errors.duration_minutes"
                placeholder="30"
                required
              />
            </div>

            <div class="col-12 col-md-3">
              <FieldNumber
                id="service-price"
                label="Precio"
                v-model="form.price"
                :formError="errors.price"
                placeholder="0.00"
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
                placeholder="0.00"
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
                v-model="form.whatsapp_contact"
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
                :images="serviceImages"
                :maxFiles="10"
                :maxSizeMb="2"
                label="Galería de imágenes"
                context="admin"
                @updated="reloadPage"
              />
            </div>
          </div>

          <FormActions
            :submitText="'Guardar'"
            :submittingText="'Guardando...'"
            :cancelHref="`/admin/listings/${listing?.id}/services`"
            :sending="sending"
          />
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'
import FieldImage from '@/Components/Fields/FieldImage.vue'
import ServiceImageUpload from '@/Components/Fields/ServiceImageUpload.vue'
import FormActions from '@/Components/FormActions.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const service = computed(() => page.props.service)
const locations = computed(() => page.props.locations || [])
const categories = computed(() => page.props.categories || [])

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

const sending = ref(false)
const mainImage = ref(null)
const keepImage = ref(true)
const initialPreview = computed(() => service.value?.image ? `/storage/${service.value.image}` : '')
const serviceImages = computed(() => page.props.serviceImages || [])

const onImageKeepChange = (value) => {
  keepImage.value = value
}

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

const submit = () => {
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

  router.post(`/admin/listings/${listing.value.id}/services/${service.value.id}`, formData, {
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
