<template>
  <MemberLayout>
    <Head :title="`Editar Sección - ${listing?.name || ''}`" />

    <PageHeader
      title="Editar Sección"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/minisite/sections`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="updateSection">
          <div class="mb-3">
            <label class="form-label">Tipo de Sección</label>
            <input type="text" class="form-control" :value="sectionTypes[section.section_type]" disabled />
          </div>

          <template v-if="section.section_type === 'hero'">
            <div class="mb-3">
              <FieldText
                id="hero-title"
                label="Título del Hero"
                v-model="form.hero_title"
                placeholder="Nombre de tu negocio"
              />
            </div>
            <div class="mb-3">
              <FieldText
                id="hero-subtitle"
                label="Subtítulo"
                v-model="form.hero_subtitle"
                placeholder="Breve descripción"
              />
            </div>
            <div class="mb-3">
              <FieldSelect
                id="hero-layout"
                label="Diseño del Hero"
                v-model="form.hero_layout"
              >
                <option value="left">Izquierda</option>
                <option value="center">Centrado</option>
                <option value="right">Derecha</option>
              </FieldSelect>
            </div>
          </template>

          <template v-else-if="section.section_type === 'footer'">
            <div class="mb-3">
              <FieldTextarea
                id="footer-text"
                label="Texto del Footer"
                v-model="form.footer_text"
                placeholder="Texto que aparece en el footer..."
                :rows="2"
              />
            </div>
            <div class="mb-3">
              <FieldSwitch
                id="footer-show-social"
                label="Mostrar redes sociales"
                v-model="form.footer_show_social"
              />
            </div>
          </template>

          <template v-else>
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
          </template>

          <div v-if="!['hero', 'footer'].includes(section.section_type)" class="mb-4">
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

          <div v-if="section.section_type === 'services'" class="border-top pt-4">
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
                  <option value="grid">Cuadrícula</option>
                </FieldSelect>
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
              <div class="col-md-6">
                <FieldNumber
                  id="max-items"
                  label="Máximo de items a mostrar"
                  v-model="config.max_items"
                  placeholder="12"
                />
              </div>
            </div>
          </div>

          <div v-if="section.section_type === 'gallery'" class="border-top pt-4">
            <h6 class="mb-3">Configuración de Galería</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <FieldSelect
                  id="gallery-view-mode"
                  label="Vista"
                  v-model="config.gallery_view_mode"
                >
                  <option value="grid">Cuadrícula</option>
                  <option value="carousel">Carrusel</option>
                </FieldSelect>
              </div>
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
              <div class="col-12">
                <FieldSwitch
                  id="show-captions"
                  label="Mostrar títulos de imágenes"
                  v-model="config.show_captions"
                />
              </div>
            </div>
          </div>

          <div v-if="section.section_type === 'promotions'" class="border-top pt-4">
            <h6 class="mb-3">Configuración de Promociones</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <FieldSelect
                  id="view-mode"
                  label="Vista"
                  v-model="config.view_mode"
                >
                  <option value="list">Lista</option>
                  <option value="carousel">Carrusel</option>
                </FieldSelect>
              </div>
              <div class="col-md-6">
                <FieldSwitch
                  id="show-all-promos"
                  label="Mostrar todas las promociones"
                  v-model="config.show_all"
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

          <div v-if="section.section_type === 'contact_form'" class="border-top pt-4">
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

          <div v-if="section.section_type === 'locations'" class="border-top pt-4">
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

          <div v-if="section.section_type === 'about'" class="border-top pt-4">
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

          <div v-if="section.section_type === 'features'" class="border-top pt-4">
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

          <div v-if="section.section_type === 'faqs'" class="border-top pt-4">
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

          <div v-if="section.section_type === 'products'" class="border-top pt-4">
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
              <div class="col-md-6">
                <FieldNumber
                  id="max-items"
                  label="Máximo de items a mostrar"
                  v-model="config.max_items"
                  placeholder="12"
                />
              </div>
            </div>
          </div>

          <div v-if="section.section_type === 'reviews'" class="border-top pt-4">
            <h6 class="mb-3">Configuración de Reseñas</h6>
            <div class="row g-3">
              <div class="col-12">
                <FieldSwitch
                  id="show-all-reviews"
                  label="Mostrar todas las reseñas"
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
              <div class="col-md-6">
                <FieldSwitch
                  id="show-comment"
                  label="Mostrar comentario"
                  v-model="config.show_comment"
                />
              </div>
              <div class="col-md-6">
                <FieldSwitch
                  id="show-client-name"
                  label="Mostrar nombre del cliente"
                  v-model="config.show_client_name"
                />
              </div>
              <div class="col-md-6">
                <FieldNumber
                  id="max-items"
                  label="Máximo de items a mostrar"
                  v-model="config.max_items"
                  placeholder="12"
                />
              </div>
            </div>
          </div>

          <div v-if="section.section_type === 'restaurant_menu'" class="border-top pt-4">
            <h6 class="mb-3">Configuración del Menú</h6>
            <div class="row g-3">
              <div class="col-12">
                <FieldSwitch
                  id="show-all-items"
                  label="Mostrar todos los elementos"
                  v-model="config.show_all"
                />
              </div>
              <div class="col-md-6">
                <FieldSelect
                  id="view-mode"
                  label="Vista"
                  v-model="config.view_mode"
                >
                  <option value="list">Lista</option>
                  <option value="grid">Cuadrícula</option>
                  <option value="carousel">Carrusel</option>
                  <option value="full">Completa (las 3)</option>
                </FieldSelect>
              </div>
              <div class="col-md-6">
                <FieldSwitch
                  id="show-images"
                  label="Mostrar imágenes"
                  v-model="config.show_images"
                />
              </div>
              <div class="col-md-6">
                <FieldSwitch
                  id="show-prices"
                  label="Mostrar precios"
                  v-model="config.show_prices"
                />
              </div>
              <div class="col-md-6">
                <FieldNumber
                  id="max-items"
                  label="Máximo de items por categoría"
                  v-model="config.max_items"
                  placeholder="12"
                />
              </div>
            </div>
          </div>

          <div v-if="section.section_type === 'properties'" class="border-top pt-4">
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
              <div class="col-md-6">
                <FieldSwitch
                  id="show-description"
                  label="Mostrar descripción"
                  v-model="config.show_description"
                />
              </div>
              <div class="col-md-6">
                <FieldNumber
                  id="max-items"
                  label="Máximo de items a mostrar"
                  v-model="config.max_items"
                  placeholder="12"
                />
              </div>
            </div>
          </div>

          <FormActions
            :submitText="'Guardar'"
            :submittingText="'Guardando...'"
            :cancelHref="`/member/listings/${listing?.id}/minisite/sections`"
            :sending="sending"
          />
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
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
const section = computed(() => page.props.section)
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
        { label: section.value?.title || 'Editar', active: true },
      ]
    }
  }
  return [
    { label: 'Inicio', href: '/member/dashboard' },
    { label: 'Minisite', href: `/member/listings/${listing.value?.id}/minisite` },
    { label: 'Secciones', href: `/member/listings/${listing.value?.id}/minisite/sections` },
    { label: section.value?.title || 'Editar', active: true },
  ]
})

const sending = ref(false)
const form = reactive({
  title: '',
  subtitle: '',
  description: '',
  buttons: [],
  hero_title: '',
  hero_subtitle: '',
  hero_layout: 'left',
  footer_text: '',
  footer_show_social: true,
})

const config = reactive({
  view_mode: 'carousel',
  show_image: true,
  show_price: true,
  show_description: false,
  gallery_id: null,
  gallery_view_mode: 'grid',
  images_limit: 10,
  show_captions: true,
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

onMounted(() => {
  if (section.value) {
    form.title = section.value.title || ''
    form.subtitle = section.value.subtitle || ''
    form.description = section.value.description || ''
    form.buttons = section.value.buttons || []
    form.hero_title = section.value.title || ''
    form.hero_subtitle = section.value.description || ''
    form.hero_layout = section.value.config?.layout || 'left'
    form.footer_text = section.value.config?.text || ''
    form.footer_show_social = section.value.config?.show_social !== false
    Object.assign(config, section.value.config || {})
  }
})

const addButton = () => {
  form.buttons.push({ text: '', url: '', style: 'primary' })
}

const removeButton = (index) => {
  form.buttons.splice(index, 1)
}

const updateSection = () => {
  sending.value = true

  if (section.value.section_type === 'hero') {
    router.put(`/member/listings/${listing.value.id}/minisite`, {
      hero_title: form.hero_title || '',
      hero_subtitle: form.hero_subtitle || '',
      hero_layout: form.hero_layout || 'left',
    }, {
      onFinish: () => {
        sending.value = false
      },
    })
  } else if (section.value.section_type === 'footer') {
    router.put(`/member/listings/${listing.value.id}/minisite`, {
      footer_text: form.footer_text || '',
      footer_show_social: form.footer_show_social ? 1 : 0,
    }, {
      onFinish: () => {
        sending.value = false
      },
    })
  } else {
    router.put(`/member/listings/${listing.value.id}/minisite/sections/${section.value.id}`, {
      title: form.title || null,
      subtitle: form.subtitle || null,
      description: form.description || null,
      config: { ...config },
      buttons: form.buttons.filter(b => b.text && b.url),
    }, {
      onFinish: () => {
        sending.value = false
      },
    })
  }
}
</script>
