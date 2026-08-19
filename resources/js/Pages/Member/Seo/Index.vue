<template>
  <MemberLayout>
    <Head :title="`SEO - ${business.name}`" />

    <PageHeader
      title="Posicionamiento SEO"
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
            <button class="nav-link" :class="{ active: activeTab === 'basic' }" @click="activeTab = 'basic'" type="button">
              <i class="bi bi-search me-1"></i>SEO Basico
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" :class="{ active: activeTab === 'social' }" @click="activeTab = 'social'" type="button">
              <i class="bi bi-share me-1"></i>Redes Sociales
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" :class="{ active: activeTab === 'indexing' }" @click="activeTab = 'indexing'" type="button">
              <i class="bi bi-globe me-1"></i>Indexacion
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" :class="{ active: activeTab === 'advanced' }" @click="activeTab = 'advanced'" type="button">
              <i class="bi bi-gear me-1"></i>Avanzado
            </button>
          </li>
        </ul>

        <form @submit.prevent="submit">
          <div class="tab-content">
            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'basic' }" role="tabpanel">
              <div class="row g-3">
                <div class="col-12 col-md-8">
                  <FieldText
                    id="seo-title"
                    label="Titulo SEO"
                    v-model="form.seo_title"
                    :formError="errors.seo_title"
                    placeholder="Agrega un titulo para SEO"
                  />
                  <small class="text-muted d-block mb-3">{{ form.seo_title?.length || 0 }}/255 caracteres</small>
                </div>

                <div class="col-12 col-md-6">
                  <FieldText
                    id="focus-keyword"
                    label="Palabra Clave Objetivo"
                    v-model="form.focus_keyword"
                    :formError="errors.focus_keyword"
                    placeholder="palabra o frase clave"
                  />
                </div>

                <div class="col-12">
                  <FieldTextarea
                    id="seo-description"
                    label="Descripcion SEO"
                    v-model="form.seo_description"
                    :formError="errors.seo_description"
                    :rows="3"
                    placeholder="Agrega una descripcion para SEO"
                  />
                  <small class="text-muted d-block mb-3">{{ form.seo_description?.length || 0 }}/500 caracteres</small>
                </div>

                <div class="col-12">
                  <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Vista previa en Google:</strong>
                    <div class="mt-2 p-2 bg-white rounded border">
                      <div style="color: #1a0dab; font-size: 18px; line-height: 1.3;">
                        {{ form.seo_title || business.name }}
                      </div>
                      <div style="color: #006621; font-size: 14px;">
                        {{ canonicalUrl }}
                      </div>
                      <div style="color: #545722; font-size: 13px; line-height: 1.4;">
                        {{ form.seo_description || 'Sin descripcion. Agrega una descripcion SEO para mejorar el posicionamiento.' }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'social' }" role="tabpanel">
              <div class="row g-3">
                <div class="col-12 col-md-8">
                  <FieldText
                    id="og-title"
                    label="Titulo para Redes Sociales"
                    v-model="form.og_title"
                    :formError="errors.og_title"
                    placeholder="Titulo que aparecera al compartir"
                  />
                </div>

                <div class="col-12">
                  <FieldTextarea
                    id="og-description"
                    label="Descripcion para Redes Sociales"
                    v-model="form.og_description"
                    :formError="errors.og_description"
                    :rows="3"
                    placeholder="Descripcion para redes sociales"
                  />
                  <small class="text-muted d-block mb-3">{{ form.og_description?.length || 0 }}/500 caracteres</small>
                </div>

                <div class="col-12 col-md-6">
                  <FieldText
                    id="og-image-alt"
                    label="Texto Alternativo de Imagen"
                    v-model="form.og_image_alt"
                    :formError="errors.og_image_alt"
                    placeholder="Descripcion de la imagen"
                  />
                </div>

                <div class="col-12">
                  <div class="mb-3">
                    <label class="form-label">Imagen para Redes Sociales</label>
                    <div v-if="imagePreview || form.og_image" class="mb-2">
                      <img :src="imagePreview || form.og_image" alt="OG Image" class="img-thumbnail" style="max-height: 200px;" />
                    </div>
                    <input
                      ref="ogImageInput"
                      type="file"
                      accept="image/jpeg,image/png,image/webp"
                      class="form-control"
                      @change="handleOgImageChange"
                    />
                    <small class="text-muted d-block mt-1">JPG, PNG o WebP. Max 2MB. Tamano recomendado: 1200x630px</small>
                  </div>
                </div>

                <div class="col-12">
                  <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Vista previa de tarjeta:</strong>
                    <div class="mt-2 p-3 bg-light rounded">
                      <div class="d-flex gap-3">
                        <img v-if="imagePreview || form.og_image" :src="imagePreview || form.og_image" class="rounded" style="max-width: 150px; max-height: 150px; object-fit: cover;" />
                        <div>
                          <div style="color: #000; font-size: 16px; font-weight: 600;">{{ form.og_title || form.seo_title || business.name }}</div>
                          <div style="color: #000; font-size: 14px;">{{ form.og_description || form.seo_description || 'Sin descripcion' }}</div>
                          <div style="color: #888; font-size: 12px;">{{ canonicalUrl }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'indexing' }" role="tabpanel">
              <div class="row g-3">
                <div class="col-12">
                  <FieldSwitch
                    id="allow-indexing"
                    label="Permitir indexacion"
                    v-model="form.allow_indexing"
                  />
                  <small class="text-muted d-block mb-3">Si esta desactivado, los motores de busqueda no indexaran tu sitio</small>
                </div>

                <div class="col-12">
                  <FieldSwitch
                    id="follow-links"
                    label="Permitir seguir enlaces"
                    v-model="form.follow_links"
                  />
                  <small class="text-muted d-block mb-3">Los motores de busqueda seguian los enlaces de tu sitio</small>
                </div>

                <div class="col-12">
                  <FieldSwitch
                    id="include-in-sitemap"
                    label="Incluir en sitemap"
                    v-model="form.include_in_sitemap"
                  />
                  <small class="text-muted d-block mb-3">Tu sitio aparecera en el mapa del sitio XML</small>
                </div>

                <div class="col-12 col-md-8">
                  <FieldUrl
                    id="canonical-url"
                    label="URL Canonica"
                    v-model="form.canonical_url"
                    :formError="errors.canonical_url"
                    placeholder="https://..."
                  />
                  <small class="text-muted d-block">URL principal de tu sitio. Si se deja vacia, se usara la URL del negocio.</small>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'advanced' }" role="tabpanel">
              <div class="row g-3">
                <div class="col-12">
                  <FieldSwitch
                    id="schema-enabled"
                    label="Datos estructurados"
                    v-model="form.schema_enabled"
                  />
                  <small class="text-muted d-block mb-3">Activa los datos estructurados (Schema.org) para mejor posicionamiento</small>
                </div>

                <div class="col-12 col-md-6">
                  <FieldSelect
                    id="schema-type"
                    label="Tipo de Schema"
                    v-model="form.schema_type"
                    :options="schemaTypeOptions"
                    :formError="errors.schema_type"
                  />
                  <small class="text-muted d-block">Selecciona el tipo que mejor describe tu negocio</small>
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
import { ref, reactive, computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldUrl from '@/Components/Fields/FieldUrl.vue'

const page = usePage()
const business = computed(() => page.props.business)
const seo = computed(() => page.props.seo)
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
        { label: biz.name, href: `/member/businesses/${biz.id}/edit` },
        { label: 'SEO', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'SEO', active: true },
  ]
})

const activeTab = ref('basic')
const sending = ref(false)
const imagePreview = ref(null)

const schemaTypeOptions = [
  { value: 'LocalBusiness', label: 'LocalBusiness' },
  { value: 'Restaurant', label: 'Restaurant' },
  { value: 'Store', label: 'Store' },
  { value: 'MedicalBusiness', label: 'MedicalBusiness' },
  { value: 'ProfessionalService', label: 'ProfessionalService' },
  { value: 'BeautySalon', label: 'BeautySalon' },
  { value: 'HealthClub', label: 'HealthClub' },
  { value: 'AutoRepair', label: 'AutoRepair' },
  { value: 'Hotel', label: 'Hotel' },
]

const form = reactive({
  seo_title: seo.value?.seo_title || '',
  seo_description: seo.value?.seo_description || '',
  focus_keyword: seo.value?.focus_keyword || '',
  allow_indexing: seo.value?.allow_indexing ?? true,
  follow_links: seo.value?.follow_links ?? true,
  include_in_sitemap: seo.value?.include_in_sitemap ?? true,
  canonical_url: seo.value?.canonical_url || '',
  og_title: seo.value?.og_title || '',
  og_description: seo.value?.og_description || '',
  og_image: seo.value?.og_image || '',
  og_image_alt: seo.value?.og_image_alt || '',
  schema_enabled: seo.value?.schema_enabled ?? true,
  schema_type: seo.value?.schema_type || 'LocalBusiness',
  settings: seo.value?.settings || null,
})

const canonicalUrl = computed(() => {
  return form.canonical_url || `${window.location.origin}/${business.value.name.toLowerCase().replace(/\s+/g, '-')}`
})

const handleOgImageChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    imagePreview.value = URL.createObjectURL(file)
    form.og_image = file
  }
}

const submit = () => {
  sending.value = true
  const data = new FormData()
  data.append('seo_title', form.seo_title || '')
  data.append('seo_description', form.seo_description || '')
  data.append('focus_keyword', form.focus_keyword || '')
  data.append('allow_indexing', form.allow_indexing ? '1' : '0')
  data.append('follow_links', form.follow_links ? '1' : '0')
  data.append('include_in_sitemap', form.include_in_sitemap ? '1' : '0')
  data.append('canonical_url', form.canonical_url || '')
  data.append('og_title', form.og_title || '')
  data.append('og_description', form.og_description || '')
  data.append('og_image_alt', form.og_image_alt || '')
  data.append('schema_enabled', form.schema_enabled ? '1' : '0')
  data.append('schema_type', form.schema_type || 'LocalBusiness')

  if (form.og_image && typeof form.og_image !== 'string') {
    data.append('og_image', form.og_image)
  }

  router.post(`/member/businesses/${business.value.id}/seo`, data, {
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
