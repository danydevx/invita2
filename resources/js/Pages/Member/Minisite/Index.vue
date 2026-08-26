<template>
  <MemberLayout>
    <Head :title="`Minisite - ${listing?.name || ''}`" />

    <PageHeader
      title="Configuración del Minisite"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/minisite/sections`"
    >
      <template #actions>
        <Link :href="`/member/listings/${listing?.id}/minisite/sections`" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-layout-text-sidebar me-1"></i>Secciones
        </Link>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="saveSettings">
          <div class="row g-4">
            <div class="col-12">
              <h5 class="border-bottom pb-2 mb-3">Hero</h5>
            </div>

            <div class="col-md-6">
              <FieldSelect
                id="hero-layout"
                label="Layout del Hero"
                v-model="form.hero_layout"
              >
                <option v-for="(label, value) in heroLayouts" :key="value" :value="value">
                  {{ label }}
                </option>
              </FieldSelect>
            </div>

            <div class="col-md-6">
              <FieldText
                id="hero-title"
                label="Título del Hero"
                v-model="form.hero_title"
                placeholder="Bienvenido a mi negocio"
              />
            </div>

            <div class="col-md-6">
              <FieldText
                id="hero-subtitle"
                label="Subtítulo del Hero"
                v-model="form.hero_subtitle"
                placeholder="Los mejores servicios para ti"
              />
            </div>

            <div class="col-md-6">
              <label class="form-label">Imagen de Fondo</label>
              <input
                type="file"
                class="form-control"
                accept="image/jpeg,image/png,image/webp"
                @change="onBackgroundChange"
              />
              <div v-if="form.hero_background_image" class="mt-2">
                <img :src="form.hero_background_image" class="img-thumbnail" style="max-height: 100px;" />
              </div>
            </div>

            <div class="col-md-6 d-flex align-items-end">
              <FieldSwitch
                id="hero-social"
                label="Mostrar redes sociales"
                v-model="form.hero_show_social"
              />
            </div>

            <div class="col-12">
              <h5 class="border-bottom pb-2 mb-3">Footer</h5>
            </div>

            <div class="col-md-6">
              <FieldTextarea
                id="footer-text"
                label="Texto del Footer"
                v-model="form.footer_text"
                :rows="3"
                placeholder="© 2024 Mi Negocio. Todos los derechos reservados."
              />
            </div>

            <div class="col-md-6 d-flex align-items-end">
              <FieldSwitch
                id="footer-social"
                label="Mostrar redes sociales"
                v-model="form.footer_show_social"
              />
            </div>

            <div class="col-12">
              <h5 class="border-bottom pb-2 mb-3">Estado</h5>
            </div>

            <div class="col-md-6">
              <FieldSwitch
                id="is-active"
                label="Minisite activo"
                v-model="form.is_active"
              />
              <small class="text-muted">Cuando esté activo, el minisite será accesible públicamente.</small>
            </div>

            <div class="col-12">
              <div v-if="!setting" class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                No existe configuración. Se creará al guardar.
              </div>
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              <i class="bi bi-check-lg me-1"></i>
              {{ sending ? 'Guardando...' : 'Guardar Configuración' }}
            </button>
          </div>
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
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const setting = computed(() => page.props.setting)
const heroLayouts = computed(() => page.props.heroLayouts || {})
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
        { label: 'Minisite', active: true },
      ]
    }
  }
  return [
    { label: 'Inicio', href: '/member/dashboard' },
    { label: 'Minisite', active: true },
  ]
})

const sending = ref(false)
const form = reactive({
  hero_layout: 'left',
  hero_title: '',
  hero_subtitle: '',
  hero_background_image: null,
  hero_show_social: false,
  footer_text: '',
  footer_show_social: true,
  is_active: false,
})

if (setting.value) {
  Object.assign(form, {
    hero_layout: setting.value.hero_layout,
    hero_title: setting.value.hero_title || '',
    hero_subtitle: setting.value.hero_subtitle || '',
    hero_background_image: setting.value.hero_background_image,
    hero_show_social: setting.value.hero_show_social || false,
    footer_text: setting.value.footer_text || '',
    footer_show_social: setting.value.footer_show_social,
    is_active: setting.value.is_active,
  })
}

const onBackgroundChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.hero_background_image = file
  }
}

const saveSettings = () => {
  sending.value = true

  const formData = new FormData()
  formData.append('_method', setting.value ? 'PUT' : 'POST')
  formData.append('hero_layout', form.hero_layout)
  formData.append('hero_title', form.hero_title || '')
  formData.append('hero_subtitle', form.hero_subtitle || '')
  formData.append('hero_show_social', form.hero_show_social ? '1' : '0')
  formData.append('footer_text', form.footer_text || '')
  formData.append('footer_show_social', form.footer_show_social ? '1' : '0')
  formData.append('is_active', form.is_active ? '1' : '0')

  if (form.hero_background_image instanceof File) {
    formData.append('hero_background_image', form.hero_background_image)
  }

  router.post(`/member/listings/${listing.value.id}/minisite`, formData, {
    forceFormData: true,
    onFinish: () => {
      sending.value = false
    },
    onSuccess: () => {
      toast.success('Configuración guardada correctamente')
    },
    onError: (errors) => {
      sending.value = false
      const errorMsg = Object.values(errors).join(', ') || 'Error al guardar'
      toast.error(errorMsg)
    },
  })
}
</script>
