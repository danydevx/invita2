<template>
  <MemberLayout>
    <Head :title="`Nuevo Producto - ${listing?.name || ''}`" />

    <PageHeader
      title="Nuevo Producto"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/products`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3 mb-3">
            <div class="col-12 col-md-8">
              <FieldText
                id="product-name"
                label="Nombre"
                v-model="form.name"
                :formError="errors.name"
                placeholder="Nombre del producto"
                required
                @blur="generateSlug"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="product-slug"
                label="Slug"
                v-model="form.slug"
                :formError="errors.slug"
                placeholder="nombre-producto"
              />
              <small class="text-muted">Se genera automaticamente si se deja vacio.</small>
            </div>

            <div class="col-12">
              <FieldTextarea
                id="product-description"
                label="Descripcion"
                v-model="form.description"
                :formError="errors.description"
                :rows="3"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldSelect
                id="product-category"
                label="Categoria"
                v-model="form.category_id"
                :formError="errors.category_id"
              >
                <option value="">Sin categoria</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </FieldSelect>
            </div>

            <div class="col-12 col-md-6">
              <FieldSelect
                id="product-location"
                label="Ubicacion"
                v-model="form.business_location_id"
                :formError="errors.business_location_id"
              >
                <option value="">Todas las ubicaciones</option>
                <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
              </FieldSelect>
            </div>

            <div class="col-12 col-md-4">
              <FieldNumber
                id="product-price"
                label="Precio"
                v-model="form.price"
                :formError="errors.price"
                placeholder="0.00"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldSwitch
                id="product-show-price"
                label="Mostrar precio"
                v-model="form.show_price"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldNumber
                id="product-compare-price"
                label="Precio anterior"
                v-model="form.compare_at_price"
                :formError="errors.compare_at_price"
                placeholder="0.00"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldNumber
                id="product-quantity"
                label="Cantidad en stock"
                v-model="form.quantity"
                :formError="errors.quantity"
                placeholder="0"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="product-sku"
                label="SKU"
                v-model="form.sku"
                :formError="errors.sku"
                placeholder="SKU-001"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="product-barcode"
                label="Codigo de barras"
                v-model="form.barcode"
                :formError="errors.barcode"
                placeholder="123456789"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldSwitch
                id="product-featured"
                label="Producto destacado"
                v-model="form.is_featured"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldPhone
                id="product-whatsapp"
                label="WhatsApp"
                v-model="form.whatsapp_contact"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldSwitch
                id="product-active"
                label="Producto activo"
                v-model="form.is_active"
              />
            </div>

            <div class="col-12">
              <FieldImage
                id="product-image"
                label="Imagen principal"
                v-model="productImages"
                :maxFiles="1"
                :maxSizeMb="2"
                accept="image/jpeg"
              />
              <small class="text-muted">JPG, max 2MB</small>
            </div>

            <div class="col-12">
              <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-1"></i>
                La galería de imágenes estará disponible después de crear el producto.
              </div>
            </div>
          </div>

          <FormActions :submitText="'Guardar'" :submittingText="'Guardando...'" :cancelHref="`/member/listings/${listing?.id}/products`" :sending="sending" />
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
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'
import FieldImage from '@/Components/Fields/FieldImage.vue'
import ProductImageUpload from '@/Components/Fields/ProductImageUpload.vue'
import FormActions from '@/Components/FormActions.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const locations = computed(() => page.props.locations || [])
const categories = computed(() => page.props.categories || [])

const errors = reactive({
  name: '',
  slug: '',
  description: '',
  price: '',
  compare_at_price: '',
  quantity: '',
  sku: '',
  barcode: '',
  business_location_id: '',
  category_id: '',
})

const validateForm = () => {
  let isValid = true

  errors.name = ''
  errors.slug = ''
  errors.description = ''
  errors.price = ''
  errors.compare_at_price = ''
  errors.quantity = ''
  errors.sku = ''
  errors.barcode = ''
  errors.business_location_id = ''
  errors.category_id = ''

  if (!form.name || form.name.trim() === '') {
    errors.name = 'El nombre es obligatorio.'
    isValid = false
  } else if (form.name.length > 150) {
    errors.name = 'El nombre no puede tener mas de 150 caracteres.'
    isValid = false
  }

  if (form.price && isNaN(parseFloat(form.price))) {
    errors.price = 'El precio debe ser un numero valido.'
    isValid = false
  }

  if (form.compare_at_price && isNaN(parseFloat(form.compare_at_price))) {
    errors.compare_at_price = 'El precio anterior debe ser un numero valido.'
    isValid = false
  }

  if (form.quantity && isNaN(parseInt(form.quantity))) {
    errors.quantity = 'La cantidad debe ser un numero entero.'
    isValid = false
  }

  return isValid
}

const sending = ref(false)
const businessMenu = computed(() => page.props.businessMenu || [])
const productImages = ref([])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Productos', href: `/member/listings/${listing.value?.id}/products` },
  { label: 'Nuevo' },
])

const form = reactive({
  name: '',
  slug: '',
  description: '',
  price: '',
  show_price: true,
  compare_at_price: '',
  sku: '',
  barcode: '',
  quantity: '',
  is_active: true,
  is_featured: false,
  whatsapp_contact: '',
  sort_order: 0,
  business_location_id: '',
  category_id: '',
})

const generateSlug = () => {
  if (form.name && !form.slug) {
    form.slug = form.name.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '')
  }
}

const submit = () => {
  if (!validateForm()) {
    toast.warning('Por favor completa los campos requeridos')
    return
  }

  sending.value = true
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
  if (productImages.value instanceof File) {
    formData.append('image', productImages.value)
  }
  router.post(`/member/listings/${listing.value.id}/products`, formData, {
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
