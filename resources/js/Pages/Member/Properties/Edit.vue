<template>
  <MemberLayout>
    <Head :title="`Editar Propiedad - ${business?.name || ''}`" />

    <PageHeader
      title="Editar Propiedad"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business?.id}/properties`"
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
      <div class="card-body p-0">
        <div v-if="Object.keys(mergedErrors).length" class="alert alert-danger m-3">
          <ul class="mb-0">
            <li v-for="(error, key) in mergedErrors" :key="key">{{ error }}</li>
          </ul>
        </div>
        <form @submit.prevent="submit">
          <div class="property-sections">

            <div class="property-section">
              <div class="property-section__header">
                <i class="bi bi-hash"></i>
                <span>Información de la Propiedad</span>
              </div>
              <div class="property-section__body">
                <div v-if="formSchema && lockedSection">
                  <FormSection
                    :section="lockedSection"
                    :form="form"
                    :errors="mergedErrors"
                    :mainImageFile="mainImageFile"
                    :initialMainImageUrl="property?.main_image_url"
                    @update:keep="keepMainImage = $event"
                    @image-removed="removeMainImage"
                  />
                </div>
              </div>
            </div>

            <PropertyLocationSection
              v-model="locationData"
              :errors="mergedErrors"
            />

            <div v-if="formSchema && nonLockedSections.length > 0">
              <div v-for="section in nonLockedSections" :key="section.id" class="property-section">
                <div class="property-section__header">
                  <i class="bi bi-list-ul"></i>
                  <span>{{ section.name }}</span>
                </div>
                <div class="property-section__body">
                  <FormSection
                    :section="section"
                    :form="form"
                    :errors="mergedErrors"
                    :mainImageFile="mainImageFile"
                    :initialMainImageUrl="property?.main_image_url"
                    @update:keep="keepMainImage = $event"
                    @image-removed="removeMainImage"
                  />
                </div>
              </div>
            </div>

            <div v-if="hasGalleryFields" class="property-section">
              <div class="property-section__header">
                <i class="bi bi-images"></i>
                <span>Galería de imágenes</span>
              </div>
              <div class="property-section__body">
                <PropertyImageUpload
                  :businessId="business?.id"
                  :propertyId="property?.id"
                  :images="propertyImages || []"
                  :maxFiles="10"
                  :maxSizeMb="5"
                  label="Galería de imágenes"
                  @updated="reloadImages"
                />
              </div>
            </div>

            <div v-if="amenities.length > 0" class="property-section">
              <div class="property-section__header">
                <i class="bi bi-star"></i>
                <span>Amenidades</span>
              </div>
              <div class="property-section__body">
                <PropertyAmenityPicker
                  v-model="form.amenity_ids"
                  :amenities="amenities"
                />
              </div>
            </div>

          </div>

          <div class="property-form-actions">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              {{ sending ? 'Guardando...' : 'Guardar Cambios' }}
            </button>
            <Link :href="`/member/businesses/${business?.id}/properties`" class="btn btn-outline-secondary">
              Cancelar
            </Link>
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
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldRadio from '@/Components/Fields/FieldRadio.vue'
import FieldCheckbox from '@/Components/Fields/FieldCheckbox.vue'
import FieldDate from '@/Components/Fields/FieldDate.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldImage from '@/Components/Fields/FieldImage.vue'
import FieldPrice from '@/Components/Fields/FieldPrice.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'
import FieldUrl from '@/Components/Fields/FieldUrl.vue'
import FieldFile from '@/Components/Fields/FieldFile.vue'
import PropertyLocationSection from '@/Components/Properties/PropertyLocationSection.vue'
import FormSection from '@/Components/Properties/FormSection.vue'
import PropertyImageUpload from '@/Components/Fields/PropertyImageUpload.vue'
import PropertyAmenityPicker from '@/Components/Fields/PropertyAmenityPicker.vue'

const page = usePage()
const business = computed(() => page.props.business)
const property = computed(() => page.props.property)
const propertyType = computed(() => page.props.propertyType)
const formSchema = computed(() => page.props.formSchema)
const dynamicValues = computed(() => page.props.dynamicValues || {})
const propertyImages = computed(() => page.props.propertyImages || [])
const amenities = computed(() => page.props.amenities || [])
const selectedAmenityIds = computed(() => page.props.selectedAmenityIds || [])

const hasGalleryFields = computed(() => {
  if (!formSchema.value?.sections) return false
  return formSchema.value.sections.some(section =>
    section.fields?.some(f => f.field_type === 'gallery')
  )
})

const lockedSection = computed(() => {
  if (!formSchema.value?.sections) return null
  return formSchema.value.sections.find(section => section.is_locked) || null
})

const nonLockedSections = computed(() => {
  if (!formSchema.value?.sections) return []
  return formSchema.value.sections.filter(section => !section.is_locked)
})

const reloadImages = () => {
  router.reload({ only: ['propertyImages'], preserveScroll: true })
}
const serverErrors = computed(() => {
  const allErrors = { ...(page.props.errors || {}) }
  Object.keys(allErrors).forEach(key => {
    if (key.startsWith('dynamic_values.')) {
      const fieldKey = key.replace('dynamic_values.', '')
      allErrors[fieldKey] = allErrors[key]
    }
  })
  return allErrors
})

const localErrors = reactive({})

const mergedErrors = computed(() => {
  return { ...serverErrors.value, ...localErrors }
})
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const businessMatch = path.match(/^\/member\/businesses\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    const pt = propertyType.value
    const typeLabel = pt ? `${pt.name} (${pt.slug})` : 'Tipo'
    const typeHref = pt ? `/member/businesses/${businessId}/properties?property_type_id=${pt.id}` : null
    if (biz) {
      const items = [
        { label: 'Mis Negocios', href: '/member/business-modules' },
        { label: biz.name, href: `/member/businesses/${biz.id}/edit` },
        { label: 'Propiedades', href: `/member/businesses/${biz.id}/properties` },
      ]
      if (typeHref) items.push({ label: typeLabel, href: typeHref })
      items.push({ label: 'Editar Propiedad', active: true })
      return items
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Propiedades', href: '/member/business-modules/properties' },
    { label: 'Editar Propiedad', active: true },
  ]
})

const sending = ref(false)
const mainImageFile = ref(null)
const keepMainImage = ref(true)

function removeMainImage() {
  keepMainImage.value = false
  form.remove_main_image = true
}

const nonLocationValues = {}
Object.keys(dynamicValues.value).forEach(key => {
  let val = dynamicValues.value[key]
  if (val && typeof val === 'object' && val.date) {
    val = val.date.substring(0, 10)
  }
  nonLocationValues[key] = val
})

const initialForm = {
  property_type_id: property.value?.property_type_id,
  title: property.value?.title || '',
  description: property.value?.description || '',
  operation_type: property.value?.operation_type || '',
  price: property.value?.price || '',
  currency: property.value?.currency || 'MXN',
  price_period: property.value?.price_period || 'single',
  status: property.value?.status || 'draft',
  is_featured: property.value?.is_featured || false,
  is_public: property.value?.is_public || false,
  remove_main_image: false,
  amenity_ids: [...selectedAmenityIds.value],
  ...nonLocationValues,
}

if (formSchema.value?.sections) {
  for (const section of formSchema.value.sections) {
    for (const field of section.fields || []) {
      if (field.field_type === 'gallery') continue
      if (initialForm[field.field_key] === undefined) {
        if (field.field_type === 'boolean') {
          initialForm[field.field_key] = ['1', 'true', true].includes(field.default_value)
        } else if (field.default_value !== null && field.default_value !== '') {
          initialForm[field.field_key] = field.default_value
        } else {
          initialForm[field.field_key] = ''
        }
      }
    }
  }
}

const form = reactive(initialForm)

const locationKeys = ['country', 'state', 'city', 'municipality', 'colony', 'postal_code', 'street', 'exterior_number', 'interior_number', 'references', 'latitude', 'longitude', 'show_exact_location']

const locationData = ref({
  country: property.value?.country || 'MX',
  state: property.value?.state || '',
  city: property.value?.city || '',
  municipality: property.value?.municipality || '',
  colony: property.value?.colony || '',
  postal_code: property.value?.postal_code || '',
  street: property.value?.street || '',
  exterior_number: property.value?.exterior_number || '',
  interior_number: property.value?.interior_number || '',
  references: property.value?.references || '',
  latitude: property.value?.latitude || '',
  longitude: property.value?.longitude || '',
  show_exact_location: property.value?.show_exact_location || false,
})

watch(locationData, (val) => {
  Object.assign(form, val)
}, { deep: true })

const getFieldColClass = (fieldType) => {
  if (['textarea', 'image'].includes(fieldType)) {
    return 'col-12'
  }
  if (['select', 'radio', 'checkbox'].includes(fieldType)) {
    return 'col-12 col-md-6'
  }
  return 'col-12 col-md-6'
}

const validateForm = () => {
  Object.keys(localErrors).forEach(key => delete localErrors[key])

  const requiredFields = ['title', 'operation_type', 'price', 'state', 'city']

  for (const fieldKey of requiredFields) {
    const val = form[fieldKey]
    if (!val || (typeof val === 'string' && val.trim() === '')) {
      localErrors[fieldKey] = `El campo ${fieldKey} es obligatorio.`
    }
  }

  if (formSchema.value?.sections) {
    for (const section of formSchema.value.sections) {
      for (const field of section.fields || []) {
        if (field.is_required && field.field_type !== 'gallery') {
          const val = form[field.field_key]
          if (!val || (typeof val === 'string' && val.trim() === '')) {
            localErrors[field.field_key] = `El campo ${field.label} es obligatorio.`
          }
        }
      }
    }
  }

  if (Object.keys(localErrors).length > 0) {
    toast.warning('Por favor completa los campos requeridos')
    return false
  }

  return true
}

const submit = () => {
  if (!validateForm()) {
    return
  }

  sending.value = true
  const formData = new FormData()
  formData.append('_method', 'PUT')

  const mainFields = [
    'property_type_id', 'title', 'description', 'operation_type', 'price',
    'currency', 'price_period', 'status', 'is_featured', 'is_public',
    'remove_main_image', 'country', 'state', 'state_code', 'city',
    'municipality', 'colony', 'postal_code', 'street', 'exterior_number',
    'interior_number', 'references', 'latitude', 'longitude', 'show_exact_location'
  ]

  const dynamicValues = {}

  Object.keys(form).forEach(key => {
    if (mainFields.includes(key)) {
      const val = form[key]
      if (val !== null && val !== '') {
        if (typeof val === 'boolean') {
          formData.append(key, val ? '1' : '0')
        } else {
          formData.append(key, val)
        }
      }
    } else if (key !== 'location' && key !== 'amenity_ids') {
      dynamicValues[key] = form[key]
    }
  })

  if (Object.keys(dynamicValues).length > 0) {
    formData.append('dynamic_values', JSON.stringify(dynamicValues))
  }

  if (form.amenity_ids && form.amenity_ids.length > 0) {
    form.amenity_ids.forEach(id => {
      formData.append('amenity_ids[]', id)
    })
  }

  if (mainImageFile.value instanceof File) {
    formData.append('main_image', mainImageFile.value)
  }

  router.post(`/member/businesses/${business.value.id}/properties/${property.value.id}`, formData, {
    preserveScroll: true,
    onSuccess: () => {
      sending.value = false
    },
    onError: (errs) => {
      sending.value = false
      Object.keys(errs).forEach(key => {
        const fieldKey = key.startsWith('dynamic_values.')
          ? key.replace('dynamic_values.', '')
          : key
        localErrors[fieldKey] = errs[key]
      })
    },
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>

<style scoped>
.property-sections {
  display: flex;
  flex-direction: column;
}

.property-section {
  border-bottom: 1px solid #e9ecef;
}

.property-section:last-child {
  border-bottom: none;
}

.property-section__header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 1rem;
  background: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  font-weight: 600;
  color: #495057;
}

.property-section__header i {
  color: #0d6efd;
}

.property-section__body {
  padding: 1rem;
}

.property-form-actions {
  display: flex;
  gap: 0.5rem;
  padding: 1rem;
  background: #f8f9fa;
  border-top: 1px solid #e9ecef;
}
</style>
