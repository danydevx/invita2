<template>
  <AdminLayout>
    <Head title="Crear Theme" />

    <PageHeader
      title="Crear Theme"
      :breadcrumbs="[
        { label: 'Admin', href: '/admin' },
        { label: 'Themes', href: '/admin/minisite-themes' },
        { label: 'Crear', active: true },
      ]"
      :backHref="'/admin/minisite-themes'"
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
        <ul class="nav nav-tabs mb-4" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link" :class="{ active: activeTab === 'general' }" @click="activeTab = 'general'" type="button">
              <i class="bi bi-sliders me-1"></i>General
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" :class="{ active: activeTab === 'colors' }" @click="activeTab = 'colors'" type="button">
              <i class="bi bi-droplet me-1"></i>Colores
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" :class="{ active: activeTab === 'fonts' }" @click="activeTab = 'fonts'" type="button">
              <i class="bi bi-type me-1"></i>Tipografias
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" :class="{ active: activeTab === 'buttons' }" @click="activeTab = 'buttons'" type="button">
              <i class="bi bi-ui-checks me-1"></i>Botones
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" :class="{ active: activeTab === 'layout' }" @click="activeTab = 'layout'" type="button">
              <i class="bi bi-layout-sidebar me-1"></i>Layout
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" :class="{ active: activeTab === 'sections' }" @click="activeTab = 'sections'" type="button">
              <i class="bi bi-grid me-1"></i>Secciones
            </button>
          </li>
        </ul>

        <form @submit.prevent="submitForm">
          <div class="tab-content">
            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'general' }" role="tabpanel">
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input v-model="form.name" type="text" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Slug (identificador único)</label>
                    <input v-model="form.slug" type="text" class="form-control" required>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea v-model="form.description" class="form-control" rows="2"></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label">URL de imagen de preview</label>
                <input v-model="form.preview_image" type="text" class="form-control" placeholder="https://...">
              </div>

              <div class="mb-3 form-check">
                <input v-model="form.is_active" type="checkbox" class="form-check-input" id="activeCheck">
                <label class="form-check-label" for="activeCheck">Theme activo</label>
              </div>
            </div>

            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'colors' }" role="tabpanel">
              <div class="row g-3">
                <div class="col-12">
                  <h6 class="text-muted mb-3">Colores Principales</h6>
                </div>

                <div class="col-6 col-md-3">
                  <label class="form-label small">Brand Primary</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.brand_primary" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.brand_primary" class="form-control form-control-sm">
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label small">Brand Secondary</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.brand_secondary" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.brand_secondary" class="form-control form-control-sm">
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label small">Brand Tertiary</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.brand_tertiary" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.brand_tertiary" class="form-control form-control-sm">
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label small">Brand Quaternary</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.brand_quaternary" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.brand_quaternary" class="form-control form-control-sm">
                  </div>
                </div>
              </div>

              <div class="row g-3 mt-2">
                <div class="col-12">
                  <h6 class="text-muted mb-3">Colores de Marca</h6>
                </div>

                <div class="col-6 col-md-3">
                  <label class="form-label small">Brand Accent</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.brand_accent" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.brand_accent" class="form-control form-control-sm">
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label small">Brand Hover</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.brand_hover" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.brand_hover" class="form-control form-control-sm">
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label small">Brand Link</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.brand_link" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.brand_link" class="form-control form-control-sm">
                  </div>
                </div>
              </div>

              <div class="row g-3 mt-2">
                <div class="col-12">
                  <h6 class="text-muted mb-3">Colores de Fondo y Texto</h6>
                </div>

                <div class="col-6 col-md-3">
                  <label class="form-label small">Brand Background</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.brand_background" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.brand_background" class="form-control form-control-sm">
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label small">Brand Text</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.brand_text" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.brand_text" class="form-control form-control-sm">
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label small">Brand Text Light</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.brand_text_light" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.brand_text_light" class="form-control form-control-sm">
                  </div>
                </div>
              </div>

              <div class="row g-3 mt-2">
                <div class="col-6 col-md-3">
                  <label class="form-label small">Header BG Color</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.brand_bgcolor_header" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.brand_bgcolor_header" class="form-control form-control-sm">
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label small">Footer BG Color</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.brand_bgcolor_footer" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.brand_bgcolor_footer" class="form-control form-control-sm">
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'fonts' }" role="tabpanel">
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label">Fuente para Encabezados</label>
                  <input v-model="form.css_variables.fonts.font_heading" type="text" class="form-control" placeholder="Poppins">
                  <small class="text-muted d-block mb-3">Usada en títulos y subtítulos</small>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label">Fuente para Cuerpo de Texto</label>
                  <input v-model="form.css_variables.fonts.font_body" type="text" class="form-control" placeholder="Open Sans">
                  <small class="text-muted d-block mb-3">Usada en párrafos y texto general</small>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label">Fuente para Botones</label>
                  <input v-model="form.css_variables.fonts.font_buttons" type="text" class="form-control" placeholder="Poppins">
                  <small class="text-muted d-block mb-3">Usada en botones y elementos interactivos</small>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'buttons' }" role="tabpanel">
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label">Estilo de Botones</label>
                  <div class="d-flex gap-2">
                    <div
                      class="btn btn-primary flex-grow-1"
                      :style="{ borderRadius: getButtonsBorderRadius('rounded') }"
                      :class="{ 'border border-2 border-dark': form.css_variables.buttons_style === 'rounded' }"
                      @click="form.css_variables.buttons_style = 'rounded'"
                      style="cursor: pointer;"
                    >
                      <i class="bi bi-circle me-1"></i> Redondos
                    </div>
                    <div
                      class="btn btn-primary flex-grow-1"
                      :style="{ borderRadius: getButtonsBorderRadius('square') }"
                      :class="{ 'border border-2 border-dark': form.css_variables.buttons_style === 'square' }"
                      @click="form.css_variables.buttons_style = 'square'"
                      style="cursor: pointer;"
                    >
                      <i class="bi bi-square me-1"></i> Cuadrados
                    </div>
                    <div
                      class="btn btn-primary flex-grow-1"
                      :style="{ borderRadius: getButtonsBorderRadius('round') }"
                      :class="{ 'border border-2 border-dark': form.css_variables.buttons_style === 'round' }"
                      @click="form.css_variables.buttons_style = 'round'"
                      style="cursor: pointer;"
                    >
                      <i class="bi bi-app me-1"></i> Esquinas Redondas
                    </div>
                  </div>
                  <small class="text-muted d-block mt-2">Selecciona el estilo de los botones</small>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label d-block">Botones en Mayúsculas</label>
                  <div class="form-check form-switch">
                    <input v-model="form.css_variables.buttons_uppercase" type="checkbox" class="form-check-input" id="buttonsUppercaseSwitch">
                    <label class="form-check-label" for="buttonsUppercaseSwitch">
                      {{ form.css_variables.buttons_uppercase ? 'Sí' : 'No' }}
                    </label>
                  </div>
                  <small class="text-muted d-block mt-2">Muestra el texto de los botones en mayúsculas</small>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'layout' }" role="tabpanel">
              <div class="row g-3">
                <div class="col-12">
                  <h6 class="text-muted mb-3">Estilo de Página</h6>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label">Estilo de Página</label>
                  <select v-model="form.layout_config.page_style" class="form-select">
                    <option value="light">Claro</option>
                    <option value="dark">Oscuro</option>
                    <option value="clean">Limpio</option>
                    <option value="warm">Cálido</option>
                    <option value="fresh">Fresco</option>
                    <option value="dramatic">Dramático</option>
                  </select>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label">Estilo de Secciones</label>
                  <select v-model="form.layout_config.section_style" class="form-select">
                    <option value="spacious">Espacioso</option>
                    <option value="classic">Clásico</option>
                    <option value="cozy">Acogedor</option>
                    <option value="dramatic">Dramático</option>
                    <option value="balanced">Balanceado</option>
                    <option value="rounded">Redondeado</option>
                  </select>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label">Estilo de Hero</label>
                  <select v-model="form.layout_config.hero_style" class="form-select">
                    <option value="fullwidth">Ancho Completo</option>
                    <option value="centered">Centrado</option>
                    <option value="split">Dividido</option>
                    <option value="boxed">En Caja</option>
                    <option value="fullbleed">Full Bleed</option>
                    <option value="friendly">Amigable</option>
                  </select>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label">Cards por Fila</label>
                  <select v-model="form.layout_config.cards_per_row" class="form-select">
                    <option :value="2">2 Cards</option>
                    <option :value="3">3 Cards</option>
                    <option :value="4">4 Cards</option>
                  </select>
                </div>
              </div>
</div>
            </div>

            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'sections' }" role="tabpanel">
              <div class="row g-4">
                <div class="col-12">
                  <h6 class="text-muted mb-3">Variantes de Seccion</h6>
                  <p class="text-muted small">Define como se mostrara cada seccion en el minisitio.</p>
                </div>

                <div class="col-12 col-md-6">
                  <div class="card">
                    <div class="card-header">
                      <h6 class="mb-0"><i class="bi bi-briefcase me-2"></i>Servicios</h6>
                    </div>
                    <div class="card-body">
                      <div class="d-flex gap-3">
                        <div
                          class="variant-option"
                          :class="{ active: form.section_config?.section_variants?.services === 'cards' || !form.section_config?.section_variants?.services }"
                          @click="setSectionVariant('services', 'cards')"
                        >
                          <div class="variant-preview">
                            <div class="d-flex gap-2">
                              <div class="card-preview" v-for="i in 3" :key="i"></div>
                            </div>
                          </div>
                          <span class="variant-label">Tarjetas</span>
                        </div>
                        <div
                          class="variant-option"
                          :class="{ active: form.section_config?.section_variants?.services === 'list' }"
                          @click="setSectionVariant('services', 'list')"
                        >
                          <div class="variant-preview">
                            <div class="list-preview">
                              <div class="line" v-for="i in 3" :key="i"></div>
                            </div>
                          </div>
                          <span class="variant-label">Lista</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-6">
                  <div class="card">
                    <div class="card-header">
                      <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Ubicaciones</h6>
                    </div>
                    <div class="card-body">
                      <div class="d-flex gap-3">
                        <div
                          class="variant-option"
                          :class="{ active: form.section_config?.section_variants?.locations === 'cards' || !form.section_config?.section_variants?.locations }"
                          @click="setSectionVariant('locations', 'cards')"
                        >
                          <div class="variant-preview">
                            <div class="d-flex gap-2">
                              <div class="card-preview" v-for="i in 3" :key="i"></div>
                            </div>
                          </div>
                          <span class="variant-label">Tarjetas</span>
                        </div>
                        <div
                          class="variant-option"
                          :class="{ active: form.section_config?.section_variants?.locations === 'list' }"
                          @click="setSectionVariant('locations', 'list')"
                        >
                          <div class="variant-preview">
                            <div class="list-preview">
                              <div class="line" v-for="i in 3" :key="i"></div>
                            </div>
                          </div>
                          <span class="variant-label">Lista</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-6">
                  <div class="card">
                    <div class="card-header">
                      <h6 class="mb-0"><i class="bi bi-images me-2"></i>Galeria</h6>
                    </div>
                    <div class="card-body">
                      <div class="d-flex gap-3">
                        <div
                          class="variant-option"
                          :class="{ active: form.section_config?.section_variants?.gallery === 'grid' || !form.section_config?.section_variants?.gallery }"
                          @click="setSectionVariant('gallery', 'grid')"
                        >
                          <div class="variant-preview">
                            <div class="grid-preview"></div>
                          </div>
                          <span class="variant-label">Cuadricula</span>
                        </div>
                        <div
                          class="variant-option"
                          :class="{ active: form.section_config?.section_variants?.gallery === 'carousel' }"
                          @click="setSectionVariant('gallery', 'carousel')"
                        >
                          <div class="variant-preview">
                            <div class="carousel-preview">
                              <div class="carousel-track">
                                <div class="carousel-item" v-for="i in 3" :key="i"></div>
                              </div>
                            </div>
                          </div>
                          <span class="variant-label">Carrusel</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-6">
                  <div class="card">
                    <div class="card-header">
                      <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>Productos</h6>
                    </div>
                    <div class="card-body">
                      <div class="d-flex gap-3">
                        <div
                          class="variant-option"
                          :class="{ active: form.section_config?.section_variants?.products === 'cards' || !form.section_config?.section_variants?.products }"
                          @click="setSectionVariant('products', 'cards')"
                        >
                          <div class="variant-preview">
                            <div class="d-flex gap-2">
                              <div class="card-preview" v-for="i in 3" :key="i"></div>
                            </div>
                          </div>
                          <span class="variant-label">Tarjetas</span>
                        </div>
                        <div
                          class="variant-option"
                          :class="{ active: form.section_config?.section_variants?.products === 'list' }"
                          @click="setSectionVariant('products', 'list')"
                        >
                          <div class="variant-preview">
                            <div class="list-preview">
                              <div class="line" v-for="i in 3" :key="i"></div>
                            </div>
                          </div>
                          <span class="variant-label">Lista</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          <div class="d-flex gap-2 mt-4 pt-4 border-top">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              {{ sending ? 'Creando...' : 'Crear Theme' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import { toast } from 'vue3-toastify'

const activeTab = ref('general')
const sending = ref(false)

const form = reactive({
  name: '',
  slug: '',
  description: '',
  preview_image: '',
  is_active: true,
  css_variables: {
    colors: {
      brand_primary: '#3B82F6',
      brand_secondary: '#8B5CF6',
      brand_tertiary: '#EC4899',
      brand_quaternary: '#10B981',
      brand_accent: '#F59E0B',
      brand_hover: '#1F2937',
      brand_link: '#3B82F6',
      brand_background: '#FFFFFF',
      brand_text: '#1a1a2e',
      brand_text_light: '#6B7280',
      brand_bgcolor_header: '#FFFFFF',
      brand_bgcolor_footer: '#F8F9FA',
    },
    fonts: {
      font_heading: 'Poppins',
      font_body: 'Open Sans',
      font_buttons: 'Poppins',
    },
    buttons_style: 'round',
    buttons_uppercase: false,
  },
  layout_config: {
    page_style: 'light',
    section_style: 'spacious',
    cards_per_row: 3,
    hero_style: 'fullwidth',
  },
  section_config: {
    section_variants: {
      services: 'cards',
      locations: 'cards',
      gallery: 'grid',
      products: 'cards',
    },
  },
})

const setSectionVariant = (section, variant) => {
  if (!form.section_config.section_variants) {
    form.section_config.section_variants = {
      services: 'cards',
      locations: 'cards',
      gallery: 'grid',
      products: 'cards',
    }
  }
  form.section_config.section_variants[section] = variant
}

const getButtonsBorderRadius = (style) => {
  const radius = {
    rounded: '50px',
    square: '0px',
    round: '8px',
  }
  return radius[style] || '8px'
}

const submitForm = () => {
  sending.value = true

  router.post('/admin/minisite-themes', form, {
    onSuccess: () => {
      sending.value = false
      toast.success('Theme creado correctamente')
    },
    onError: () => {
      sending.value = false
      toast.error('Error al crear el theme')
    },
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>

<style scoped>
.variant-option {
  flex: 1;
  border: 2px solid #dee2e6;
  border-radius: 8px;
  padding: 12px;
  cursor: pointer;
  transition: all 0.2s;
  text-align: center;
}

.variant-option:hover {
  border-color: #adb5bd;
}

.variant-option.active {
  border-color: #3B82F6;
  background-color: rgba(59, 130, 246, 0.05);
}

.variant-preview {
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 8px;
}

.card-preview {
  width: 30px;
  height: 40px;
  background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
  border-radius: 4px;
}

.list-preview {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 6px;
  padding: 0 8px;
}

.list-preview .line {
  height: 8px;
  background: linear-gradient(90deg, #e9ecef 0%, #dee2e6 100%);
  border-radius: 2px;
}

.list-preview .line:nth-child(2) {
  width: 80%;
}

.list-preview .line:nth-child(3) {
  width: 60%;
}

.grid-preview {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #e9ecef 25%, transparent 25%, transparent 50%, #e9ecef 50%, #e9ecef 75%, transparent 75%);
  background-size: 12px 12px;
  border-radius: 4px;
  border: 1px solid #dee2e6;
}

.carousel-preview {
  width: 80px;
  height: 50px;
  position: relative;
  overflow: hidden;
  border-radius: 4px;
}

.carousel-track {
  display: flex;
  gap: 4px;
}

.carousel-item {
  width: 20px;
  height: 50px;
  background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
  border-radius: 2px;
  flex-shrink: 0;
}

.variant-label {
  font-size: 0.85rem;
  font-weight: 500;
  color: #495057;
}

.variant-option.active .variant-label {
  color: #3B82F6;
}
</style>
