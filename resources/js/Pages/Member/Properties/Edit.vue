<template>
  <MemberLayout>
    <Head :title="`Editar Propiedad - ${listing?.name || ''}`" />

    <PageHeader
      title="Editar Propiedad"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/properties`"
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
          <div class="row g-3">
            <template v-if="formSchema">
              <template v-for="section in filteredSections" :key="section.id">
                <fieldset class="col-12">
                  <legend class="border-bottom pb-2 mb-3">{{ section.name }}</legend>

                  <LocationFields
                    v-if="section.general_field_section_slug === 'ubicacion'"
                    v-model="locationData"
                    :errors="mergedErrors"
                  />

                  <div v-else class="row g-3">
                    <template v-for="field in section.fields.filter(f => f.field_type !== 'gallery')" :key="field.id">
                      <div class="col-12" :class="getFieldColClass(field.field_type)">
                      <FieldText
                        v-if="field.field_type === 'text'"
                        :id="`field-${field.field_key}`"
                        :label="field.label"
                        v-model="form[field.field_key]"
                        :formError="mergedErrors[field.field_key]"
                        :placeholder="field.placeholder"
                        :helpText="field.help_text"
                        :required="field.is_required"
                      />

                      <FieldTextarea
                        v-else-if="field.field_type === 'textarea'"
                        :id="`field-${field.field_key}`"
                        :label="field.label"
                        v-model="form[field.field_key]"
                        :formError="mergedErrors[field.field_key]"
                        :placeholder="field.placeholder"
                        :helpText="field.help_text"
                        :required="field.is_required"
                        :rows="5"
                      />

                      <FieldNumber
                        v-else-if="field.field_type === 'number' || field.field_type === 'decimal' || field.field_type === 'integer'"
                        :id="`field-${field.field_key}`"
                        :label="field.label"
                        v-model="form[field.field_key]"
                        :formError="mergedErrors[field.field_key]"
                        :placeholder="field.placeholder"
                        :helpText="field.help_text"
                        :required="field.is_required"
                      />

                      <FieldPrice
                        v-else-if="field.field_type === 'price'"
                        :id="`field-${field.field_key}`"
                        :label="field.label"
                        v-model="form[field.field_key]"
                        :formError="mergedErrors[field.field_key]"
                        :placeholder="field.placeholder"
                        :helpText="field.help_text"
                        :required="field.is_required"
                        currencyLabel="Monto"
                      />

                      <FieldSelect
                        v-else-if="field.field_type === 'select'"
                        :id="`field-${field.field_key}`"
                        :label="field.label"
                        v-model="form[field.field_key]"
                        :formError="mergedErrors[field.field_key]"
                        :helpText="field.help_text"
                        :required="field.is_required"
                        :options="field.options"
                        placeholder="Selecciona una opción"
                      />

                      <FieldRadio
                        v-else-if="field.field_type === 'radio'"
                        :id="`field-${field.field_key}`"
                        :label="field.label"
                        v-model="form[field.field_key]"
                        :formError="mergedErrors[field.field_key]"
                        :helpText="field.help_text"
                        :required="field.is_required"
                        :options="field.options"
                      />

                      <FieldCheckbox
                        v-else-if="field.field_type === 'checkbox'"
                        :id="`field-${field.field_key}`"
                        :label="field.label"
                        v-model="form[field.field_key]"
                        :formError="mergedErrors[field.field_key]"
                        :helpText="field.help_text"
                      />

                      <FieldDate
                        v-else-if="field.field_type === 'date'"
                        :id="`field-${field.field_key}`"
                        :label="field.label"
                        v-model="form[field.field_key]"
                        :formError="mergedErrors[field.field_key]"
                        :placeholder="field.placeholder"
                        :helpText="field.help_text"
                        :required="field.is_required"
                      />

                      <FieldImage
                        v-else-if="field.field_type === 'image'"
                        :id="`field-${field.field_key}`"
                        :label="field.label"
                        v-model="mainImageFile"
                        :initialPreview="mainImagePreview"
                        :helpText="field.help_text"
                        :required="field.is_required"
                        :maxFiles="1"
                        :maxSizeMb="5"
                        accept="image/jpeg,image/png,image/webp"
                      />

                      <FieldSwitch
                        v-else-if="field.field_type === 'boolean'"
                        :id="`field-${field.field_key}`"
                        :label="field.label"
                        v-model="form[field.field_key]"
                      />

                      <FieldEmail
                        v-else-if="field.field_type === 'email'"
                        :id="`field-${field.field_key}`"
                        :label="field.label"
                        v-model="form[field.field_key]"
                        :formError="mergedErrors[field.field_key]"
                        :placeholder="field.placeholder"
                        :helpText="field.help_text"
                        :required="field.is_required"
                      />

                      <FieldPhone
                        v-else-if="field.field_type === 'phone'"
                        :id="`field-${field.field_key}`"
                        :label="field.label"
                        v-model="form[field.field_key]"
                        :formError="mergedErrors[field.field_key]"
                        :placeholder="field.placeholder"
                        :helpText="field.help_text"
                        :required="field.is_required"
                      />

                      <FieldUrl
                        v-else-if="field.field_type === 'url'"
                        :id="`field-${field.field_key}`"
                        :label="field.label"
                        v-model="form[field.field_key]"
                        :formError="mergedErrors[field.field_key]"
                        :placeholder="field.placeholder"
                        :helpText="field.help_text"
                        :required="field.is_required"
                      />

                      <FieldFile
                        v-else-if="field.field_type === 'file'"
                        :id="`field-${field.field_key}`"
                        :label="field.label"
                        v-model="form[field.field_key]"
                        :helpText="field.help_text"
                        :required="field.is_required"
                        accept=".pdf,.doc,.docx,.xls,.xlsx"
                      />
                      </div>
                    </template>
                  </div>
                </fieldset>
              </template>
            </template>
          </div>

          <fieldset v-if="amenities.length > 0" class="col-12 mt-4">
            <legend class="border-bottom pb-2 mb-3">Amenidades</legend>
            <div class="row g-3">
              <div v-for="amenity in amenities" :key="amenity.id" class="col-6 col-md-4 col-lg-3">
                <div class="form-check">
                  <input
                    :id="`amenity-${amenity.id}`"
                    v-model="form.amenity_ids"
                    type="checkbox"
                    :value="amenity.id"
                    class="form-check-input"
                  >
                  <label :for="`amenity-${amenity.id}`" class="form-check-label d-flex align-items-center gap-2">
                    <i :class="amenity.icon || 'bi bi-star'" style="font-size: 1rem;"></i>
                    {{ amenity.name }}
                  </label>
                </div>
              </div>
            </div>
          </fieldset>

          <PropertyGalleryManager
            v-if="hasGalleryFields"
            :listingId="listing?.id"
            :propertyId="property?.id"
            :images="propertyImages"
            :maxImages="10"
            class="mt-4"
          />

          <FormActions :submitText="'Guardar'" :submittingText="'Guardando...'" :cancelHref="`/member/listings/${listing?.id}/properties`" :sending="sending" />
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
import LocationFields from '@/Components/Properties/LocationFields.vue'
import PropertyGalleryManager from '@/Components/Properties/PropertyGalleryManager.vue'
import FormActions from '@/Components/FormActions.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
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

const filteredSections = computed(() => {
  if (!formSchema.value?.sections) return []
  return formSchema.value.sections.filter(section => {
    return true
  })
})

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
  const businessMatch = path.match(/^\/member\/listings\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    if (biz) {
      return [
        { label: 'Inicio', href: `/member/listings/${biz.id}/modules` },
        { label: 'Propiedades', href: `/member/listings/${biz.id}/properties` },
        { label: property.value?.title || 'Editar', active: true },
      ]
    }
  }
  return [
    { label: 'Inicio', href: '/member/dashboard' },
    { label: 'Propiedades', href: `/member/listings/${listing.value?.id}/properties` },
    { label: property.value?.title || 'Editar', active: true },
  ]
})

const sending = ref(false)
const mainImageFile = ref(null)
const keepMainImage = ref(true)
const mainImagePreview = computed(() => property.value?.main_image_url || null)

const LOCATION_FIELDS = ['country', 'state', 'state_code', 'city', 'municipality', 'colony', 'postal_code', 'street', 'exterior_number', 'interior_number', 'references', 'latitude', 'longitude', 'show_exact_location']

const nonLocationValues = {}
Object.keys(dynamicValues.value).forEach(key => {
  let val = dynamicValues.value[key]
  if (val && typeof val === 'object' && val.date) {
    val = val.date.substring(0, 10)
  }
  nonLocationValues[key] = val
})

const form = reactive({
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
  country: property.value?.country || 'MX',
  state: property.value?.state || '',
  state_code: property.value?.state_code || '',
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
  ...nonLocationValues,
})

const locationData = ref({
  country: form.country,
  state: form.state,
  state_code: form.state_code,
  city: form.city,
  municipality: form.municipality,
  colony: form.colony,
  postal_code: form.postal_code,
  street: form.street,
  exterior_number: form.exterior_number,
  interior_number: form.interior_number,
  references: form.references,
  latitude: form.latitude,
  longitude: form.longitude,
  show_exact_location: form.show_exact_location,
})

watch(locationData, (val) => {
  Object.assign(form, val)
}, { deep: true })

watch(form, (val) => {
  const locationUpdates = {}
  for (const key of LOCATION_FIELDS) {
    if (val[key] !== undefined) {
      locationUpdates[key] = val[key]
    }
  }
  if (Object.keys(locationUpdates).length > 0) {
    Object.assign(locationData.value, locationUpdates)
  }
}, { deep: true })

watch(formSchema, (schema) => {
  if (schema?.sections) {
    for (const section of schema.sections) {
      for (const field of section.fields || []) {
        if (field.field_type === 'gallery') continue
        if (field.default_value !== null && field.default_value !== '') {
          form[field.field_key] = field.field_type === 'boolean'
            ? ['1', 'true', true].includes(field.default_value)
            : field.default_value
        } else if (form[field.field_key] === undefined) {
          form[field.field_key] = ''
        }
      }
    }
  }
}, { immediate: true })

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

  if (form.operation_type === 'rent' && !form.price_period) {
    localErrors.price_period = 'La periodicidad es obligatoria para rentas.'
  }

  let hasErrors = false

  if (formSchema.value?.sections) {
    for (const section of formSchema.value.sections) {
      for (const field of section.fields || []) {
        if (field.is_required && field.field_type !== 'gallery' && field.field_type !== 'image') {
          const val = form[field.field_key]
          if (val === undefined || val === '' || (typeof val === 'string' && val.trim() === '')) {
            localErrors[field.field_key] = `El campo ${field.label} es obligatorio.`
            hasErrors = true
          }
        }
      }
    }
  }

  if (hasErrors || Object.keys(localErrors).length > 0) {
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

  const dynamicValuesOutput = {}

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
      dynamicValuesOutput[key] = form[key]
    }
  })

  if (Object.keys(dynamicValuesOutput).length > 0) {
    formData.append('dynamic_values', JSON.stringify(dynamicValuesOutput))
  }

  if (form.amenity_ids && form.amenity_ids.length > 0) {
    form.amenity_ids.forEach(id => {
      formData.append('amenity_ids[]', id)
    })
  }

  if (mainImageFile.value instanceof File) {
    formData.append('main_image', mainImageFile.value)
  }

  router.post(`/member/listings/${listing.value.id}/properties/${property.value.id}`, formData, {
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
