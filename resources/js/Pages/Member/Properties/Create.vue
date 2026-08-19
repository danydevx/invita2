<template>
  <MemberLayout>
    <Head :title="`Nueva Propiedad - ${business?.name || ''}`" />

    <PageHeader
      title="Nueva Propiedad"
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

    <div v-if="!selectedTypeId" class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <h5 class="card-title mb-4">Selecciona el tipo de propiedad</h5>
        <div class="row g-3">
          <div class="col-md-4" v-for="type in propertyTypes" :key="type.id">
            <div
              class="card h-100 cursor-pointer"
              :class="{ 'border-primary': selectedTypeId === type.id }"
              @click="selectType(type.id)"
            >
              <div class="card-body text-center">
                <i :class="type.icon || 'bi bi-building'" style="font-size: 2rem;"></i>
                <h6 class="mt-2 mb-0">{{ type.name }}</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="mb-4">
            <button type="button" class="btn btn-link p-0" @click="changeType">
              <i class="bi bi-arrow-left me-1"></i>Cambiar tipo de propiedad
            </button>
          </div>

          <div class="row g-3">
            <PropertyLocationSection
              v-model="locationData"
              :errors="mergedErrors"
            />

            <template v-if="formSchema">
              <template v-for="section in filteredSections" :key="section.id">
                <fieldset class="col-12">
                  <legend class="border-bottom pb-2 mb-3">{{ section.name }}</legend>
                  <div class="row g-3">

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
                    :rows="4"
                  />

                  <FieldNumber
                    v-else-if="field.field_type === 'number' || field.field_type === 'decimal'"
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
                  >
                    <option value="">Selecciona una opción</option>
                    <option v-for="opt in field.options" :key="opt.value" :value="opt.value">
                      {{ opt.label }}
                    </option>
                  </FieldSelect>

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

          <div v-if="hasGalleryFields" class="alert alert-info mt-4">
            <i class="bi bi-info-circle me-2"></i>
            La galería de imágenes estará disponible después de crear la propiedad.
          </div>

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              {{ sending ? 'Creando...' : 'Crear Propiedad' }}
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

const page = usePage()
const business = computed(() => page.props.business)
const propertyTypes = computed(() => page.props.propertyTypes || [])
const formSchema = computed(() => page.props.formSchema)
const hasGalleryFields = computed(() => {
  if (!formSchema.value?.sections) return false
  return formSchema.value.sections.some(section =>
    section.fields?.some(f => f.field_type === 'gallery')
  )
})

const SECTIONS_TO_SKIP = ['seo', 'contacto', 'extras', 'multimedia']

const filteredSections = computed(() => {
  if (!formSchema.value?.sections) return []
  return formSchema.value.sections.filter(section => {
    const slug = section.general_field_section_slug
    return !slug || !SECTIONS_TO_SKIP.includes(slug)
  })
})
const selectedTypeId = computed(() => page.props.selectedTypeId)
const limitInfo = computed(() => page.props.limitInfo)
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
    if (biz) {
      return [
        { label: 'Mis Negocios', href: '/member/business-modules' },
        { label: biz.name, href: `/member/businesses/${biz.id}/edit` },
        { label: 'Propiedades', href: `/member/businesses/${biz.id}/properties` },
        { label: 'Nueva Propiedad', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Nueva Propiedad', active: true },
  ]
})

const sending = ref(false)
const mainImageFile = ref(null)
const locationData = ref({})

watch(locationData, (val) => {
  Object.assign(form, val)
}, { deep: true })

const form = reactive({
  property_type_id: selectedTypeId.value,
})

if (formSchema.value?.sections) {
  for (const section of formSchema.value.sections) {
    for (const field of section.fields || []) {
      if (field.field_type === 'gallery') continue
      if (form[field.field_key] === undefined) {
        if (field.field_type === 'boolean') {
          form[field.field_key] = ['1', 'true', true].includes(field.default_value)
        } else if (field.default_value !== null && field.default_value !== '') {
          form[field.field_key] = field.default_value
        } else {
          form[field.field_key] = ''
        }
      }
    }
  }
}

const selectType = (typeId) => {
  window.location.href = `/member/businesses/${business.value.id}/properties/create?type=${typeId}`
}

const changeType = () => {
  window.location.href = `/member/businesses/${business.value.id}/properties/create`
}

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

  const mainFields = [
    'property_type_id', 'title', 'description', 'operation_type', 'price',
    'currency', 'price_period', 'status', 'is_featured', 'is_public',
    'country', 'state', 'state_code', 'city', 'municipality', 'colony',
    'postal_code', 'street', 'exterior_number', 'interior_number',
    'references', 'latitude', 'longitude', 'show_exact_location'
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

  if (mainImageFile.value instanceof File) {
    formData.append('main_image', mainImageFile.value)
  }

  router.post(`/member/businesses/${business.value.id}/properties`, formData, {
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
.cursor-pointer {
  cursor: pointer;
}
</style>
