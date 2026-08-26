<template>
  <MemberLayout>
    <Head :title="`Nueva Sección - ${listing?.name || ''}`" />

    <PageHeader
      title="Nueva Sección"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/minisite/sections`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="createSection">
          <div class="mb-4">
            <h5>Tipo de Sección</h5>
            <div class="row g-2">
              <div v-for="(label, type) in sectionTypes" :key="type" class="col-6 col-md-3">
                <div
                  class="section-type-card"
                  :class="{ 'section-type-card--active': form.section_type === type }"
                  @click="selectType(type)"
                >
                  <i class="bi bi-folder"></i>
                  <span>{{ label }}</span>
                </div>
              </div>
            </div>
          </div>

          <div v-if="form.section_type">
            <div class="mb-3">
              <FieldText
                id="section-title"
                label="Título de la Sección"
                v-model="form.title"
                placeholder="Ej: Nuestros Servicios"
              />
            </div>

            <div class="mb-3">
              <FieldText
                id="section-subtitle"
                label="Subtítulo"
                v-model="form.subtitle"
                placeholder="Subtítulo breve para mostrar bajo el título..."
              />
            </div>

            <div class="mb-3">
              <FieldTextarea
                id="section-description"
                label="Descripción"
                v-model="form.description"
                placeholder="Breve descripción de esta sección..."
                :rows="3"
              />
            </div>

            <div class="mb-4">
              <h6 class="mb-3">Botones CTA</h6>
              <div v-for="(btn, index) in form.buttons" :key="index" class="row g-3 mb-3 p-3 bg-light rounded">
                <div class="col-md-4">
                  <FieldText
                    id="btn-text"
                    label="Texto"
                    v-model="btn.text"
                    placeholder="Ver más"
                  />
                </div>
                <div class="col-md-5">
                  <FieldText
                    id="btn-url"
                    label="URL"
                    v-model="btn.url"
                    placeholder="https://..."
                  />
                </div>
                <div class="col-md-2">
                  <FieldSelect
                    id="btn-style"
                    label="Estilo"
                    v-model="btn.style"
                  >
                    <option value="primary">Primario</option>
                    <option value="secondary">Secundario</option>
                    <option value="outline">Outline</option>
                  </FieldSelect>
                </div>
                <div class="col-md-1 d-flex align-items-end pb-3">
                  <button type="button" class="btn btn-outline-danger" @click="removeButton(index)">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
              <button type="button" class="btn btn-outline-primary btn-sm" @click="addButton">
                <i class="bi bi-plus me-1"></i>Agregar botón
              </button>
            </div>

            <div v-if="form.section_type === 'services'" class="border-top pt-4">
              <h6 class="mb-3">Configuración de Servicios</h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <FieldSelect
                    id="view-mode"
                    label="Vista"
                    v-model="config.view_mode"
                  >
                    <option value="carousel">Carrusel</option>
                    <option value="list">Lista</option>
                  </FieldSelect>
                </div>
                <div class="col-md-6 d-flex align-items-end pb-3">
                  <FieldSwitch
                    id="show-all-services"
                    label="Mostrar todos los servicios"
                    v-model="showAllServices"
                  />
                </div>
                <div class="col-12">
                  <FieldSwitch
                    id="show-image"
                    label="Mostrar imagen"
                    v-model="config.show_image"
                  />
                </div>
                <div class="col-12">
                  <FieldSwitch
                    id="show-price"
                    label="Mostrar precio"
                    v-model="config.show_price"
                  />
                </div>
                <div class="col-12">
                  <FieldSwitch
                    id="show-description"
                    label="Mostrar descripción"
                    v-model="config.show_description"
                  />
                </div>
              </div>
            </div>

            <div v-if="form.section_type === 'gallery'" class="border-top pt-4">
              <h6 class="mb-3">Configuración de Galería</h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <FieldSelect
                    id="gallery-id"
                    label="Galería"
                    v-model="config.gallery_id"
                  >
                    <option :value="null">Selecciona una galería</option>
                    <option v-for="gallery in galleries" :key="gallery.id" :value="gallery.id">
                      {{ gallery.name }}
                    </option>
                  </FieldSelect>
                </div>
                <div class="col-md-6">
                  <FieldNumber
                    id="images-limit"
                    label="Límite de imágenes"
                    v-model="config.images_limit"
                    :min="1"
                    :max="50"
                  />
                </div>
              </div>
            </div>

            <div v-if="form.section_type === 'promotions'" class="border-top pt-4">
              <h6 class="mb-3">Configuración de Promociones</h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <FieldSwitch
                    id="show-all-promos"
                    label="Mostrar todas las promociones"
                    v-model="config.show_all"
                  />
                </div>
              </div>
            </div>

            <div v-if="form.section_type === 'contact_form'" class="border-top pt-4">
              <h6 class="mb-3">Configuración del Formulario</h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <FieldSelect
                    id="form-id"
                    label="Formulario"
                    v-model="config.form_id"
                  >
                    <option :value="null">Selecciona un formulario</option>
                    <option v-for="formItem in forms" :key="formItem.id" :value="formItem.id">
                      {{ formItem.name }}
                    </option>
                  </FieldSelect>
                </div>
              </div>
            </div>

            <div v-if="form.section_type === 'locations'" class="border-top pt-4">
              <h6 class="mb-3">Configuración de Ubicaciones</h6>
              <div class="row g-3">
                <div class="col-12">
                  <FieldSwitch
                    id="show-all-locations"
                    label="Mostrar todas las ubicaciones"
                    v-model="config.show_all"
                  />
                </div>
                <div class="col-md-6">
                  <FieldSwitch
                    id="show-address"
                    label="Mostrar dirección"
                    v-model="config.show_address"
                  />
                </div>
                <div class="col-md-6">
                  <FieldSwitch
                    id="show-phone"
                    label="Mostrar teléfono"
                    v-model="config.show_phone"
                  />
                </div>
                <div class="col-md-6">
                  <FieldSwitch
                    id="show-email"
                    label="Mostrar email"
                    v-model="config.show_email"
                  />
                </div>
                <div class="col-md-6">
                  <FieldSwitch
                    id="show-hours"
                    label="Mostrar horarios"
                    v-model="config.show_hours"
                  />
                </div>
              </div>
            </div>

            <div v-if="form.section_type === 'about'" class="border-top pt-4">
              <h6 class="mb-3">Configuración de Nosotros</h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <FieldSwitch
                    id="show-image"
                    label="Mostrar imagen/logo"
                    v-model="config.show_image"
                  />
                </div>
                <div class="col-md-6">
                  <FieldSwitch
                    id="show-description"
                    label="Mostrar descripción"
                    v-model="config.show_description"
                  />
                </div>
              </div>
            </div>

            <div v-if="form.section_type === 'features'" class="border-top pt-4">
              <h6 class="mb-3">Configuración de Características</h6>
              <div class="row g-3">
                <div class="col-12">
                  <FieldSwitch
                    id="show-all-features"
                    label="Mostrar todas las características"
                    v-model="config.show_all"
                  />
                </div>
                <div class="col-md-4">
                  <FieldSwitch
                    id="show-icon"
                    label="Mostrar icono"
                    v-model="config.show_icon"
                  />
                </div>
                <div class="col-md-4">
                  <FieldSwitch
                    id="show-title"
                    label="Mostrar título"
                    v-model="config.show_title"
                  />
                </div>
                <div class="col-md-4">
                  <FieldSwitch
                    id="show-description"
                    label="Mostrar descripción"
                    v-model="config.show_description"
                  />
                </div>
              </div>
            </div>

            <div v-if="form.section_type === 'faqs'" class="border-top pt-4">
              <h6 class="mb-3">Configuración de Preguntas Frecuentes</h6>
              <div class="row g-3">
                <div class="col-12">
                  <FieldSwitch
                    id="show-all-faqs"
                    label="Mostrar todas las preguntas"
                    v-model="config.show_all"
                  />
                </div>
                <div class="col-12">
                  <FieldSwitch
                    id="show-questions"
                    label="Mostrar respuestas"
                    v-model="config.show_questions"
                  />
                </div>
              </div>
            </div>

            <div v-if="form.section_type === 'products'" class="border-top pt-4">
              <h6 class="mb-3">Configuración de Productos</h6>
              <div class="row g-3">
                <div class="col-12">
                  <FieldSwitch
                    id="show-all-products"
                    label="Mostrar todos los productos"
                    v-model="config.show_all"
                  />
                </div>
                <div class="col-md-6">
                  <FieldSelect
                    id="view-mode"
                    label="Vista"
                    v-model="config.view_mode"
                  >
                    <option value="grid">Cuadrícula</option>
                    <option value="carousel">Carrusel</option>
                    <option value="list">Lista</option>
                  </FieldSelect>
                </div>
                <div class="col-md-4">
                  <FieldSwitch
                    id="show-image"
                    label="Mostrar imagen"
                    v-model="config.show_image"
                  />
                </div>
                <div class="col-md-4">
                  <FieldSwitch
                    id="show-price"
                    label="Mostrar precio"
                    v-model="config.show_price"
                  />
                </div>
                <div class="col-md-4">
                  <FieldSwitch
                    id="show-compare-price"
                    label="Mostrar precio anterior"
                    v-model="config.show_compare_price"
                  />
                </div>
              </div>
            </div>

            <div v-if="form.section_type === 'properties'" class="border-top pt-4">
              <h6 class="mb-3">Configuración de Propiedades</h6>
              <div class="row g-3">
                <div class="col-12">
                  <FieldSwitch
                    id="show-all-properties"
                    label="Mostrar todas las propiedades"
                    v-model="config.show_all"
                  />
                </div>
                <div class="col-md-6">
                  <FieldSelect
                    id="view-mode"
                    label="Vista"
                    v-model="config.view_mode"
                  >
                    <option value="grid">Cuadrícula</option>
                    <option value="carousel">Carrusel</option>
                    <option value="list">Lista</option>
                  </FieldSelect>
                </div>
                <div class="col-md-4">
                  <FieldSwitch
                    id="show-image"
                    label="Mostrar imagen"
                    v-model="config.show_image"
                  />
                </div>
                <div class="col-md-4">
                  <FieldSwitch
                    id="show-price"
                    label="Mostrar precio"
                    v-model="config.show_price"
                  />
                </div>
                <div class="col-md-4">
                  <FieldSwitch
                    id="show-location"
                    label="Mostrar ubicación"
                    v-model="config.show_location"
                  />
                </div>
                <div class="col-md-4">
                  <FieldSwitch
                    id="show-description"
                    label="Mostrar descripción"
                    v-model="config.show_description"
                  />
                </div>
              </div>
            </div>
          </div>

          <FormActions
            :submitText="'Crear Sección'"
            :submittingText="'Creando...'"
            :cancelHref="`/member/listings/${listing?.id}/minisite/sections`"
            :sending="sending"
            :disabled="!form.section_type"
          />
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FormActions from '@/Components/FormActions.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const sectionTypes = computed(() => page.props.sectionTypes || {})
const galleries = computed(() => page.props.galleries || [])
const forms = computed(() => page.props.forms || [])
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const match = path.match(/^\/member\/listings\/(\d+)/)
  if (match) {
    const bizId = parseInt(match[1])
    const biz = businessMenu.value.find(b => b.id === bizId)
    if (biz) {
      return [
        { label: 'Inicio', href: `/member/listings/${biz.id}/modules` },
        { label: 'Minisite', href: `/member/listings/${biz.id}/minisite` },
        { label: 'Secciones', href: `/member/listings/${biz.id}/minisite/sections` },
        { label: 'Nueva', active: true },
      ]
    }
  }
  return [
    { label: 'Inicio', href: '/member/dashboard' },
    { label: 'Minisite', href: `/member/listings/${listing.value?.id}/minisite` },
    { label: 'Secciones', href: `/member/listings/${listing.value?.id}/minisite/sections` },
    { label: 'Nueva', active: true },
  ]
})

const sending = ref(false)
const form = reactive({
  section_type: '',
  title: '',
  subtitle: '',
  description: '',
  buttons: [],
})

const config = reactive({
  view_mode: 'carousel',
  show_image: true,
  show_price: true,
  show_description: false,
  gallery_id: null,
  images_limit: 10,
  show_all: true,
  form_id: null,
  show_address: true,
  show_phone: true,
  show_email: true,
  show_hours: true,
  show_icon: true,
  show_title: true,
  show_questions: true,
  show_compare_price: true,
})

const showAllServices = ref(true)

const selectType = (type) => {
  form.section_type = type
  form.title = ''
  Object.assign(config, getDefaultConfig(type))
}

const getDefaultConfig = (type) => {
  switch (type) {
    case 'services':
      return { view_mode: 'carousel', show_image: true, show_price: true, show_description: false, service_ids: [] }
    case 'gallery':
      return { gallery_id: null, images_limit: 10 }
    case 'promotions':
      return { show_all: true, promotion_ids: [] }
    case 'contact_form':
      return { form_id: null }
    case 'locations':
      return { show_all: true, location_ids: [], show_address: true, show_phone: true, show_email: true, show_hours: true }
    case 'about':
      return { show_image: true, show_description: true }
    case 'features':
      return { show_all: true, feature_ids: [], show_icon: true, show_title: true, show_description: true }
    case 'faqs':
      return { show_all: true, faq_ids: [], category_id: null, show_questions: true }
    case 'products':
      return { show_all: true, product_ids: [], show_image: true, show_price: true, show_compare_price: true, view_mode: 'grid' }
    case 'properties':
      return { show_all: true, property_ids: [], show_image: true, show_price: true, show_location: true, show_description: true, view_mode: 'grid' }
    default:
      return {}
  }
}

const addButton = () => {
  form.buttons.push({ text: '', url: '', style: 'primary' })
}

const removeButton = (index) => {
  form.buttons.splice(index, 1)
}

const createSection = () => {
  if (!form.section_type) return

  sending.value = true

  const data = {
    section_type: form.section_type,
    title: form.title || null,
    subtitle: form.subtitle || null,
    description: form.description || null,
    config: { ...config },
    buttons: form.buttons.filter(b => b.text && b.url),
    is_active: true,
  }

  router.post(`/member/listings/${listing.value.id}/minisite/sections`, data, {
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>

<style lang="less">
.section-type-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 16px;
  border: 2px solid #dee2e6;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;

  i {
    font-size: 24px;
    margin-bottom: 8px;
    color: #6c757d;
  }

  span {
    font-size: 0.875rem;
    text-align: center;
  }

  &:hover {
    border-color: #0d6efd;
  }

  &--active {
    border-color: #0d6efd;
    background: #e7f1ff;

    i {
      color: #0d6efd;
    }
  }
}
</style>
