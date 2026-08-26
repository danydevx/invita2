<template>
  <MemberLayout>
    <Head :title="`Nuevo Producto - ${listing?.name || ''}`" />

    <PageHeader
      title="Nuevo Producto"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/menu-products`"
    />

    <div class="container-fluid py-4">
      <form @submit.prevent="submitForm">
        <div class="row">
          <div class="col-lg-8">
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="mb-0">Datos del producto</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <FieldText
                    id="product-title"
                    label="Nombre del producto"
                    v-model="form.title"
                    :formError="errors.title"
                    required
                  />
                </div>
                <div class="mb-3">
                  <FieldSelect
                    id="product-category"
                    label="Categoria"
                    v-model="form.category_id"
                    :formError="errors.category_id"
                    required
                  >
                    <option value="" disabled>Seleccionar...</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.title }}</option>
                  </FieldSelect>
                </div>
                <div class="mb-3">
                  <FieldTextarea
                    id="product-description"
                    label="Descripcion"
                    v-model="form.description"
                    :formError="errors.description"
                    :rows="3"
                  />
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <FieldNumber
                        id="product-base-price"
                        label="Precio base"
                        v-model="form.base_price"
                        :formError="errors.base_price"
                      />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <FieldNumber
                        id="product-sort"
                        label="Orden"
                        v-model="form.sort_order"
                      />
                    </div>
                  </div>
                </div>
                <div class="mb-3">
                  <FieldSwitch
                    id="product-show-price"
                    label="Mostrar precio"
                    v-model="form.show_price"
                  />
                </div>
                <div class="mb-3">
                  <FieldSwitch
                    id="product-featured"
                    label="Producto destacado"
                    v-model="form.featured"
                  />
                </div>
                <div class="mb-3">
                  <FieldSwitch
                    id="product-active"
                    label="Activo"
                    v-model="form.active"
                  />
                </div>
              </div>
            </div>

            <div class="card mb-4">
              <div class="card-header">
                <h5 class="mb-0">Imagen del producto</h5>
              </div>
              <div class="card-body">
                <FieldImage
                  id="product-image"
                  label=""
                  v-model="productImage"
                  :maxSizeMb="10"
                />
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="card mb-4">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Variantes</h5>
                <button type="button" class="btn btn-sm btn-outline-secondary" @click="addVariant">
                  <i class="bi bi-plus me-1"></i>Agregar
                </button>
              </div>
              <div class="card-body p-0">
                <div v-if="form.variants.length === 0" class="text-center text-muted py-4">
                  <p class="mb-0">No hay variantes. Agrega una para definir opciones del producto.</p>
                </div>
                <div v-else ref="variantsList" class="variants-list">
                  <div
                    v-for="(variant, index) in form.variants"
                    :key="index"
                    class="variant-item border-bottom"
                    :data-index="index"
                  >
                    <div class="variant-header d-flex align-items-center justify-content-between p-3" @click="toggleVariant(index)">
                      <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-grip-vertical variant-drag-handle text-muted" style="cursor: grab;"></i>
                        <strong>{{ variant.title || 'Variante ' + (index + 1) }}</strong>
                        <span v-if="variant.price" class="text-muted">${{ variant.price }}</span>
                      </div>
                      <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-danger" @click.stop="removeVariant(index)">
                          <i class="bi bi-trash"></i>
                        </button>
                        <i :class="isVariantExpanded(index) ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"></i>
                      </div>
                    </div>
                    <div v-if="isVariantExpanded(index)" class="variant-body p-3 pt-0">
                      <div class="row">
                        <div class="col-6 mb-2">
                          <label class="form-label small">Nombre</label>
                          <input v-model="variant.title" type="text" class="form-control" placeholder="Ej: Chica">
                        </div>
                        <div class="col-6 mb-2">
                          <label class="form-label small">Precio</label>
                          <input v-model.number="variant.price" type="number" step="0.01" class="form-control" placeholder="0.00">
                        </div>
                      </div>
                      <div class="mb-2">
                        <FieldImage
                          :id="`variant-image-${index}`"
                          label=""
                          v-model="variantImages[index]"
                          :maxSizeMb="5"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-4">
          <FormActions :submitText="'Crear Producto'" :submittingText="'Creando...'" :cancelHref="`/member/listings/${listing?.id}/menu-products`" :sending="sending" />
        </div>
      </form>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed, reactive, nextTick } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import Sortable from 'sortablejs'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldImage from '@/Components/Fields/FieldImage.vue'
import FormActions from '@/Components/FormActions.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const categories = computed(() => page.props.categories || [])

const errors = reactive({
  title: '',
  description: '',
  base_price: '',
  category_id: '',
})

const validateForm = () => {
  let isValid = true

  errors.title = ''
  errors.description = ''
  errors.base_price = ''
  errors.category_id = ''

  if (!form.value.title || form.value.title.trim() === '') {
    errors.title = 'El nombre es obligatorio.'
    isValid = false
  }

  if (!form.value.category_id) {
    errors.category_id = 'Selecciona una categoria.'
    isValid = false
  }

  if (form.value.base_price && isNaN(parseFloat(form.value.base_price))) {
    errors.base_price = 'El precio debe ser un numero valido.'
    isValid = false
  }

  return isValid
}

const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Menú', href: `/member/listings/${listing.value?.id}/menu-products` },
  { label: 'Nuevo' },
])

const productImage = ref(null)
const variantsList = ref(null)
const sending = ref(false)

const variantImages = ref([])
const expandedVariants = ref([])

let sortableInstance = null

const form = ref({
  title: '',
  category_id: '',
  description: '',
  image: null,
  base_price: null,
  show_price: true,
  featured: false,
  active: true,
  sort_order: 0,
  variants: [],
})

const isVariantExpanded = (index) => {
  return expandedVariants.value.includes(index)
}

const toggleVariant = (index) => {
  const pos = expandedVariants.value.indexOf(index)
  if (pos > -1) {
    expandedVariants.value.splice(pos, 1)
  } else {
    expandedVariants.value.push(index)
  }
}

const initSortable = () => {
  if (!variantsList.value) return

  if (sortableInstance) {
    sortableInstance.destroy()
  }

  sortableInstance = Sortable.create(variantsList.value, {
    handle: '.variant-drag-handle',
    animation: 150,
    ghostClass: 'sortable-ghost',
    dragClass: 'sortable-drag',
    onEnd: (evt) => {
      const items = form.value.variants.splice(evt.oldIndex, 1)[0]
      form.value.variants.splice(evt.newIndex, 0, items)
    },
  })
}

const addVariant = () => {
  form.value.variants.push({
    title: '',
    price: null,
    sort_order: form.value.variants.length,
    image: null,
  })
  const newIndex = form.value.variants.length - 1
  variantImages.value.push(null)
  expandedVariants.value.push(newIndex)
  nextTick(() => initSortable())
}

const removeVariant = (index) => {
  form.value.variants.splice(index, 1)
  variantImages.value.splice(index, 1)
  expandedVariants.value = expandedVariants.value.filter(i => i !== index)
}

const submitForm = () => {
  if (!validateForm()) {
    toast.warning('Por favor completa los campos requeridos')
    return
  }

  sending.value = true

  const variantsWithImages = form.value.variants.map((v, i) => ({
    ...v,
    image: variantImages.value[i],
  }))

  router.post(`/member/listings/${listing.value.id}/menu-products`, {
    ...form.value,
    image: productImage.value,
    variants: variantsWithImages,
  }, {
    forceFormData: true,
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

<style scoped>
.variants-list {
  max-height: 500px;
  overflow-y: auto;
}

.variant-header {
  cursor: pointer;
  user-select: none;
}

.variant-header:hover {
  background-color: #f8f9fa;
}

.variant-drag-handle:active {
  cursor: grabbing;
}

.sortable-ghost {
  opacity: 0.4;
  background: #e9ecef;
}

.sortable-drag {
  opacity: 0.8;
  background: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>
