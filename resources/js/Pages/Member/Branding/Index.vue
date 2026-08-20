<template>
  <MemberLayout>
    <Head :title="`Personalizacion de Marca - ${business.name}`" />

    <PageHeader
      title="Personalizacion de Marca"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <ul class="nav nav-tabs mb-4" role="tablist">
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
            <button class="nav-link" :class="{ active: activeTab === 'styles' }" @click="activeTab = 'styles'" type="button">
              <i class="bi bi-sliders me-1"></i>Estilos
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
          <li class="nav-item" role="presentation">
            <button class="nav-link" :class="{ active: activeTab === 'preview' }" @click="activeTab = 'preview'" type="button">
              <i class="bi bi-eye me-1"></i>Vista Previa
            </button>
          </li>
        </ul>

        <form @submit.prevent="submit">
          <div class="tab-content">
            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'colors' }" role="tabpanel">
              <div class="row g-3">
                <div class="col-12">
                  <h6 class="text-muted mb-3">Modo Claro</h6>
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-primary-light"
                    label="Color Principal"
                    v-model="form.colors.brand_primary.light"
                    placeholder="#3B82F6"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-secondary-light"
                    label="Color Secundario"
                    v-model="form.colors.brand_secondary.light"
                    placeholder="#8B5CF6"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-tertiary-light"
                    label="Color Terciario"
                    v-model="form.colors.brand_tertiary.light"
                    placeholder="#EC4899"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-quaternary-light"
                    label="Color Cuaternario"
                    v-model="form.colors.brand_quaternary.light"
                    placeholder="#10B981"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="accent-light"
                    label="Color Acento"
                    v-model="form.colors.brand_accent.light"
                    placeholder="#F59E0B"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="hover-light"
                    label="Color Hover"
                    v-model="form.colors.brand_hover.light"
                    placeholder="#1F2937"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-link-light"
                    label="Color Enlace"
                    v-model="form.colors.brand_link.light"
                    placeholder="#3B82F6"
                    hint="Color para enlaces del body"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-bgcolor-header-light"
                    label="Color Header"
                    v-model="form.colors.brand_bgcolor_header.light"
                    placeholder="#FFFFFF"
                    hint="Fondo del header"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-bgcolor-footer-light"
                    label="Color Footer"
                    v-model="form.colors.brand_bgcolor_footer.light"
                    placeholder="#F8F9FA"
                    hint="Fondo del footer"
                  />
                </div>

                <div class="col-12">
                  <hr class="my-4" />
                  <h6 class="text-muted mb-3">Modo Oscuro</h6>
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-primary-dark"
                    label="Color Principal"
                    v-model="form.colors.brand_primary.dark"
                    placeholder="#60A5FA"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-secondary-dark"
                    label="Color Secundario"
                    v-model="form.colors.brand_secondary.dark"
                    placeholder="#A78BFA"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-tertiary-dark"
                    label="Color Terciario"
                    v-model="form.colors.brand_tertiary.dark"
                    placeholder="#F472B6"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-quaternary-dark"
                    label="Color Cuaternario"
                    v-model="form.colors.brand_quaternary.dark"
                    placeholder="#34D399"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="accent-dark"
                    label="Color Acento"
                    v-model="form.colors.brand_accent.dark"
                    placeholder="#FBBF24"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="hover-dark"
                    label="Color Hover"
                    v-model="form.colors.brand_hover.dark"
                    placeholder="#374151"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-link-dark"
                    label="Color Enlace"
                    v-model="form.colors.brand_link.dark"
                    placeholder="#60A5FA"
                    hint="Color para enlaces del body"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-bgcolor-header-dark"
                    label="Color Header"
                    v-model="form.colors.brand_bgcolor_header.dark"
                    placeholder="#1A1A2E"
                    hint="Fondo del header"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-bgcolor-footer-dark"
                    label="Color Footer"
                    v-model="form.colors.brand_bgcolor_footer.dark"
                    placeholder="#16213E"
                    hint="Fondo del footer"
                  />
                </div>
              </div>
            </div>

            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'fonts' }" role="tabpanel">
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <FieldSelect
                    id="font-heading"
                    label="Fuente para Encabezados"
                    v-model="form.fonts.heading"
                    :options="fontOptions"
                    :formError="errors.fonts?.heading"
                  />
                  <small class="text-muted d-block mb-3">Usada en titulos y subtitulos</small>
                </div>

                <div class="col-12 col-md-6">
                  <FieldSelect
                    id="font-body"
                    label="Fuente para Cuerpo de Texto"
                    v-model="form.fonts.body"
                    :options="fontOptions"
                    :formError="errors.fonts?.body"
                  />
                  <small class="text-muted d-block mb-3">Usada en parrafos y texto general</small>
                </div>

                <div class="col-12 col-md-6">
                  <FieldSelect
                    id="font-buttons"
                    label="Fuente para Botones"
                    v-model="form.fonts.buttons"
                    :options="fontOptions"
                    :formError="errors.fonts?.buttons"
                  />
                  <small class="text-muted d-block mb-3">Usada en botones y elementos interactivos</small>
                </div>

                <div class="col-12 col-md-8">
                  <FieldUrl
                    id="custom-font-url"
                    label="URL de Fuente Personalizada (opcional)"
                    v-model="form.custom_font_url"
                    :formError="errors.custom_font_url"
                    placeholder="https://fonts.googleapis.com/css2?family=TuFuente"
                  />
                  <small class="text-muted d-block">Pega el link de Google Fonts para usar una fuente no listada</small>
                </div>

                <div class="col-12">
                  <div class="card bg-light">
                    <div class="card-body">
                      <h6 class="card-title">Vista Previa de Fuentes</h6>
                      <div class="p-3 border rounded bg-white">
                        <h4 :style="{ fontFamily: getFontFamily(form.fonts.heading) }">Encabezado H4</h4>
                        <p class="mb-2" :style="{ fontFamily: getFontFamily(form.fonts.body) }">
                          Este es un parrafo de ejemplo. El cuerpo de texto se muestra asi.
                        </p>
                        <button
                          class="btn btn-primary"
                          :style="{ fontFamily: getFontFamily(form.fonts.buttons) }"
                        >
                          Boton de Ejemplo
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'styles' }" role="tabpanel">
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <FieldSwitch
                    id="dark-mode"
                    label="Habilitar modo oscuro"
                    v-model="form.dark_mode"
                  />
                  <small class="text-muted d-block mb-3">Permite a los visitantes cambiar al tema oscuro</small>
                </div>

                <div class="col-12 col-md-6">
                  <FieldSwitch
                    id="buttons-uppercase"
                    label="Botones en mayusculas"
                    v-model="form.buttons_uppercase"
                  />
                  <small class="text-muted d-block mb-3">Muestra el texto de los botones en mayusculas</small>
                </div>

                <div class="col-12">
                  <label class="form-label">Estilo de Botones</label>
                  <div class="d-flex gap-2">
                    <div
                      class="btn btn-primary flex-grow-1"
                      :style="{ borderRadius: getButtonsBorderRadius('rounded') }"
                      :class="{ 'border border-2 border-dark': form.buttons_style === 'rounded' }"
                      @click="form.buttons_style = 'rounded'"
                      style="cursor: pointer;"
                    >
                      <i class="bi bi-circle me-1"></i> Redondos
                    </div>
                    <div
                      class="btn btn-primary flex-grow-1"
                      :style="{ borderRadius: getButtonsBorderRadius('square') }"
                      :class="{ 'border border-2 border-dark': form.buttons_style === 'square' }"
                      @click="form.buttons_style = 'square'"
                      style="cursor: pointer;"
                    >
                      <i class="bi bi-square me-1"></i> Cuadrados
                    </div>
                    <div
                      class="btn btn-primary flex-grow-1"
                      :style="{ borderRadius: getButtonsBorderRadius('round') }"
                      :class="{ 'border border-2 border-dark': form.buttons_style === 'round' }"
                      @click="form.buttons_style = 'round'"
                      style="cursor: pointer;"
                    >
                      <i class="bi bi-app me-1"></i> Esquinas Redondas
                    </div>
                  </div>
                  <small class="text-muted d-block mt-2">Selecciona el estilo de los botones</small>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'layout' }" role="tabpanel">
              <div class="row g-3">
                <div class="col-12">
                  <h6 class="text-muted mb-3">Estilo de Pagina</h6>
                  <p class="text-muted small">Define el estilo visual general de tu minisitio.</p>
                </div>

                <div class="col-12 col-md-6">
                  <FieldSelect
                    id="page-style"
                    label="Estilo de Pagina"
                    v-model="form.page_style"
                    :options="pageStyleOptions"
                    :formError="errors.page_style"
                  />
                  <small class="text-muted d-block mb-3">Afecta el fondo y tonos generales del sitio</small>
                </div>

                <div class="col-12 col-md-6">
                  <FieldSelect
                    id="section-style"
                    label="Estilo de Secciones"
                    v-model="form.section_style"
                    :options="sectionStyleOptions"
                    :formError="errors.section_style"
                  />
                  <small class="text-muted d-block mb-3">Controla el espaciado entre secciones</small>
                </div>

                <div class="col-12 col-md-6">
                  <FieldSelect
                    id="hero-style"
                    label="Estilo de Hero"
                    v-model="form.hero_style"
                    :options="heroStyleOptions"
                    :formError="errors.hero_style"
                  />
                  <small class="text-muted d-block mb-3">Configura la presentacion del hero principal</small>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'sections' }" role="tabpanel">
              <div class="row g-4">
                <div class="col-12">
                  <h6 class="text-muted mb-3">Variantes de Seccion</h6>
                  <p class="text-muted small">Selecciona como se mostrara cada seccion en tu minisitio pubblico.</p>
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
                          :class="{ active: form.section_variants?.services === 'cards' || !form.section_variants?.services }"
                          @click="form.section_variants = { ...form.section_variants, services: 'cards' }"
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
                          :class="{ active: form.section_variants?.services === 'list' }"
                          @click="form.section_variants = { ...form.section_variants, services: 'list' }"
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
                          :class="{ active: form.section_variants?.locations === 'cards' || !form.section_variants?.locations }"
                          @click="form.section_variants = { ...form.section_variants, locations: 'cards' }"
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
                          :class="{ active: form.section_variants?.locations === 'list' }"
                          @click="form.section_variants = { ...form.section_variants, locations: 'list' }"
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
                          :class="{ active: form.section_variants?.gallery === 'grid' || !form.section_variants?.gallery }"
                          @click="form.section_variants = { ...form.section_variants, gallery: 'grid' }"
                        >
                          <div class="variant-preview">
                            <div class="grid-preview"></div>
                          </div>
                          <span class="variant-label">Cuadricula</span>
                        </div>
                        <div
                          class="variant-option"
                          :class="{ active: form.section_variants?.gallery === 'carousel' }"
                          @click="form.section_variants = { ...form.section_variants, gallery: 'carousel' }"
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
                          :class="{ active: form.section_variants?.products === 'cards' || !form.section_variants?.products }"
                          @click="form.section_variants = { ...form.section_variants, products: 'cards' }"
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
                          :class="{ active: form.section_variants?.products === 'list' }"
                          @click="form.section_variants = { ...form.section_variants, products: 'list' }"
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

            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'preview' }" role="tabpanel">
              <div class="text-center mb-4">
                <div class="btn-group" role="group">
                  <button
                    type="button"
                    class="btn"
                    :class="previewMode === 'light' ? 'btn-primary' : 'btn-outline-secondary'"
                    @click="previewMode = 'light'"
                  >
                    <i class="bi bi-sun me-1"></i>Claro
                  </button>
                  <button
                    type="button"
                    class="btn"
                    :class="previewMode === 'dark' ? 'btn-primary' : 'btn-outline-secondary'"
                    @click="previewMode = 'dark'"
                  >
                    <i class="bi bi-moon me-1"></i>Oscuro
                  </button>
                </div>
              </div>

              <div class="preview-frame" :class="previewMode === 'dark' ? 'dark-mode' : 'light-mode'">
                <div class="preview-header" :style="{ backgroundColor: getColor('brand_bgcolor_header') }">
                  <h1 :style="{ color: getColor('brand_primary') }">Mi Negocio</h1>
                </div>
                <div class="preview-content">
                  <div class="preview-section" :style="{ borderLeftColor: getColor('brand_secondary') }">
                    <h3 :style="{ color: getColor('brand_primary') }">Nuestros Servicios</h3>
                    <p>Descubre todo lo que podemos ofrecerte con la mejor calidad.</p>
                    <button
                      class="btn btn-primary"
                      :style="{
                        backgroundColor: getColor('brand_primary'),
                        borderColor: getColor('brand_primary'),
                        borderRadius: getButtonsBorderRadius(form.buttons_style)
                      }"
                    >
                      Conocer mas
                    </button>
                  </div>
                  <div class="preview-section" :style="{ borderLeftColor: getColor('brand_accent') }">
                    <h3 :style="{ color: getColor('brand_accent') }">Contactanos</h3>
                    <p>Estamos aqui para ayudarte.</p>
                    <a
                      href="#"
                      :style="{ color: getColor('brand_link') }"
                      class="me-3"
                    >
                      Visitar nuestro sitio
                    </a>
                    <a
                      href="#"
                      :style="{ color: getColor('brand_hover') }"
                    >
                      Enviar mensaje (hover)
                    </a>
                  </div>
                </div>
                <div class="preview-footer" :style="{ backgroundColor: getColor('brand_bgcolor_footer') }">
                  <p :style="{ color: getColor('brand_primary') }">© 2024 Mi Negocio - Todos los derechos reservados</p>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex gap-2 mt-4 pt-4 border-top">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              {{ sending ? 'Guardando...' : 'Guardar Configuracion' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, reactive, computed, watchEffect } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldColorpicker from '@/Components/Fields/FieldColorpicker.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldUrl from '@/Components/Fields/FieldUrl.vue'
import { toast } from 'vue3-toastify'

const page = usePage()
const business = computed(() => page.props.business)
const branding = computed(() => page.props.branding)
const errors = computed(() => page.props.errors || {})
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
        { label: biz.name, href: `/member/listings/${biz.id}/edit` },
        { label: 'Branding', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Branding', active: true },
  ]
})

const activeTab = ref('colors')
const previewMode = ref('light')
const sending = ref(false)

const defaultColors = {
  brand_primary: { light: '#3B82F6', dark: '#60A5FA' },
  brand_secondary: { light: '#8B5CF6', dark: '#A78BFA' },
  brand_tertiary: { light: '#EC4899', dark: '#F472B6' },
  brand_quaternary: { light: '#10B981', dark: '#34D399' },
  brand_accent: { light: '#F59E0B', dark: '#FBBF24' },
  brand_hover: { light: '#1F2937', dark: '#374151' },
  brand_link: { light: '#3B82F6', dark: '#60A5FA' },
  brand_bgcolor_header: { light: '#FFFFFF', dark: '#1A1A2E' },
  brand_bgcolor_footer: { light: '#F8F9FA', dark: '#16213E' },
  brand_background: { light: '#FFFFFF', dark: '#1A1A2E' },
  brand_text: { light: '#1a1a2e', dark: '#ffffff' },
  brand_text_light: { light: '#6B7280', dark: '#B0B0B0' },
}

const form = reactive({
  colors: branding.value?.colors ? { ...branding.value.colors } : { ...defaultColors },
  fonts: branding.value?.fonts ? { ...branding.value.fonts } : {
    heading: 'Poppins',
    body: 'Open Sans',
    buttons: 'Poppins',
  },
  custom_font_url: branding.value?.custom_font_url || '',
  dark_mode: branding.value?.dark_mode ?? true,
  buttons_style: branding.value?.buttons_style || 'round',
  buttons_uppercase: branding.value?.buttons_uppercase ?? false,
  section_variants: branding.value?.section_variants || { services: 'cards', locations: 'cards', gallery: 'grid', products: 'cards' },
  page_style: branding.value?.page_style || '',
  section_style: branding.value?.section_style || '',
  hero_style: branding.value?.hero_style || '',
})

const fontLinkId = 'dynamic-google-fonts'

const googleFontsUrl = computed(() => {
  const fonts = [
    form.fonts.heading,
    form.fonts.body,
    form.fonts.buttons,
  ].filter(f => f && f !== 'custom')

  if (fonts.length === 0) return ''

  const families = [...new Set(fonts)].map(f => `family=${f.replace(/ /g, '+')}:wght@400;500;600;700`).join('&')
  return `https://fonts.googleapis.com/css2?${families}&display=swap`
})

watchEffect(() => {
  if (!googleFontsUrl.value) return

  let link = document.getElementById(fontLinkId)
  if (!link) {
    link = document.createElement('link')
    link.id = fontLinkId
    link.rel = 'stylesheet'
    document.head.appendChild(link)
  }
  link.href = googleFontsUrl.value
})

const fontOptions = [
  { value: 'Poppins', label: 'Poppins' },
  { value: 'Montserrat', label: 'Montserrat' },
  { value: 'Playfair Display', label: 'Playfair Display' },
  { value: 'Merriweather', label: 'Merriweather' },
  { value: 'Oswald', label: 'Oswald' },
  { value: 'Raleway', label: 'Raleway' },
  { value: 'Ubuntu', label: 'Ubuntu' },
  { value: 'Lato', label: 'Lato' },
  { value: 'Inter', label: 'Inter' },
  { value: 'Nunito', label: 'Nunito' },
  { value: 'Open Sans', label: 'Open Sans' },
  { value: 'Roboto', label: 'Roboto' },
  { value: 'Source Sans Pro', label: 'Source Sans Pro' },
]

const pageStyleOptions = [
  { value: 'light', label: 'Claro' },
  { value: 'dark', label: 'Oscuro' },
  { value: 'clean', label: 'Limpio' },
  { value: 'warm', label: 'Calido' },
  { value: 'fresh', label: 'Fresco' },
  { value: 'dramatic', label: 'Dramatico' },
]

const sectionStyleOptions = [
  { value: 'spacious', label: 'Espacioso' },
  { value: 'classic', label: 'Clasico' },
  { value: 'cozy', label: 'Acogedor' },
  { value: 'dramatic', label: 'Dramatico' },
  { value: 'balanced', label: 'Balanceado' },
  { value: 'rounded', label: 'Redondeado' },
]

const heroStyleOptions = [
  { value: 'fullwidth', label: 'Ancho Completo' },
  { value: 'centered', label: 'Centrado' },
  { value: 'split', label: 'Dividido' },
  { value: 'boxed', label: 'En Caja' },
  { value: 'fullbleed', label: 'Full Bleed' },
  { value: 'friendly', label: 'Amigable' },
]

const getColor = (colorKey) => {
  const color = form.colors[colorKey]
  if (!color) return '#000000'
  return previewMode.value === 'dark' ? (color.dark || color.light) : (color.light || color.dark || '#000000')
}

const getFontFamily = (fontName) => {
  if (!fontName || fontName === 'custom') {
    return 'sans-serif'
  }
  return `'${fontName}', sans-serif`
}

const getButtonsBorderRadius = (style) => {
  const radius = {
    rounded: '50px',
    square: '0px',
    round: '8px',
  }
  return radius[style] || '8px'
}

const submit = () => {
  sending.value = true

  const data = new FormData()
  data.append('colors', JSON.stringify(form.colors))
  data.append('fonts', JSON.stringify(form.fonts))
  data.append('custom_font_url', form.custom_font_url || '')
  data.append('dark_mode', form.dark_mode ? '1' : '0')
  data.append('buttons_style', form.buttons_style || 'round')
  data.append('buttons_uppercase', form.buttons_uppercase ? '1' : '0')
  data.append('section_variants', JSON.stringify(form.section_variants))
  data.append('page_style', form.page_style || '')
  data.append('section_style', form.section_style || '')
  data.append('hero_style', form.hero_style || '')

  router.post(`/member/listings/${business.value.id}/branding`, data, {
    onSuccess: () => {
      sending.value = false
      toast.success('Configuración de marca guardada correctamente')
    },
    onError: (errors) => {
      sending.value = false
      toast.error('Error al guardar la configuración')
    },
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>

<style scoped>
.preview-frame {
  border: 2px solid #dee2e6;
  border-radius: 12px;
  overflow: hidden;
  min-height: 400px;
}

.preview-frame.light-mode {
  background-color: #ffffff;
  color: #212529;
}

.preview-frame.dark-mode {
  background-color: #1a1a2e;
  color: #f8f9fa;
}

.preview-header {
  padding: 2rem;
  text-align: center;
}

.preview-content {
  padding: 2rem;
}

.preview-section {
  padding: 1rem;
  margin-bottom: 1rem;
  border-left: 4px solid;
  border-radius: 4px;
  background: rgba(0,0,0,0.02);
}

.preview-footer {
  padding: 1rem 2rem;
  text-align: center;
}

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
  animation: none;
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
