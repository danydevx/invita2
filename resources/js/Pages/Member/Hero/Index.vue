<template>
  <MemberLayout>
    <Head :title="`Hero - ${business.name}`" />

    <PageHeader
      title="Hero"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-4">
            <div class="col-12">
              <h5 class="border-bottom pb-2 mb-3">Contenido</h5>
            </div>

            <div class="col-md-6">
              <FieldText
                id="hero-title"
                label="Titulo"
                v-model="form.title"
                placeholder="Tu titulo aqui"
              />
            </div>

            <div class="col-md-6">
              <FieldText
                id="hero-subtitle"
                label="Subtitulo"
                v-model="form.subtitle"
                placeholder="Tu subtitulo aqui"
              />
            </div>

            <div class="col-12">
              <FieldTextarea
                id="hero-text-aux"
                label="Texto auxiliar"
                v-model="form.text_aux"
                placeholder="Texto adicional que aparece debajo del subtitulo"
                :rows="2"
              />
            </div>

            <div class="col-12">
              <h5 class="border-bottom pb-2 mb-3 mt-4">Botones CTA</h5>
            </div>

            <div class="col-12">
              <div v-for="(btn, index) in form.buttons" :key="index" class="row g-3 mb-3 p-3 bg-light rounded">
                <div class="col-md-3">
                  <FieldText
                    :id="'btn-text-' + index"
                    label="Texto"
                    v-model="btn.text"
                    placeholder="Reservar cita"
                  />
                </div>
                <div class="col-md-2">
                  <FieldSelect
                    :id="'btn-link-type-' + index"
                    label="Tipo de enlace"
                    v-model="btn.link_type"
                    :options="linkTypeOptions"
                  />
                </div>
                <div class="col-md-4">
                  <FieldSelect
                    v-if="btn.link_type === 'internal'"
                    :id="'btn-anchor-' + index"
                    label="Seccion"
                    v-model="btn.anchor"
                    :options="anchorOptions"
                  />
                  <FieldUrl
                    v-else
                    :id="'btn-url-' + index"
                    label="URL"
                    v-model="btn.url"
                    placeholder="https://..."
                  />
                </div>
                <div class="col-md-2">
                  <FieldSelect
                    :id="'btn-style-' + index"
                    label="Estilo"
                    v-model="btn.style"
                    :options="styleOptions"
                  />
                </div>
                <div class="col-md-1 d-flex align-items-end">
                  <button type="button" class="btn btn-outline-danger" @click="removeButton(index)">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>

              <button type="button" class="btn btn-outline-primary btn-sm" @click="addButton">
                <i class="bi bi-plus me-1"></i>Agregar boton
              </button>
            </div>

            <div class="col-12">
              <h5 class="border-bottom pb-2 mb-3 mt-4">Fondo</h5>
            </div>

            <div class="col-md-4">
              <FieldSelect
                id="background-type"
                label="Tipo de fondo"
                v-model="form.background_type"
                :options="backgroundTypeOptions"
              />
            </div>

            <div v-if="form.background_type === 'color'" class="col-md-4">
              <label class="form-label">Color</label>
              <div class="input-group">
                <input v-model="form.background_color" type="color" class="form-control form-control-color">
                <input v-model="form.background_color" type="text" class="form-control">
              </div>
            </div>

            <div v-if="form.background_type === 'gradient'" class="col-md-4">
              <label class="form-label">Color inicial</label>
              <div class="input-group">
                <input v-model="form.background_gradient_start" type="color" class="form-control form-control-color">
                <input v-model="form.background_gradient_start" type="text" class="form-control">
              </div>
            </div>

            <div v-if="form.background_type === 'gradient'" class="col-md-4">
              <label class="form-label">Color final</label>
              <div class="input-group">
                <input v-model="form.background_gradient_end" type="color" class="form-control form-control-color">
                <input v-model="form.background_gradient_end" type="text" class="form-control">
              </div>
            </div>

            <div v-if="form.background_type === 'image'" class="col-md-8">
              <label class="form-label">Imagen de fondo</label>
              <input type="file" class="form-control" accept="image/*" @change="handleImageUpload">
              <div v-if="form.background_image_path" class="mt-2">
                <img :src="form.background_image_path" class="img-thumbnail" style="max-height: 150px;">
              </div>
            </div>

            <div class="col-12">
              <h5 class="border-bottom pb-2 mb-3 mt-4">Alineacion y opciones</h5>
            </div>

            <div class="col-md-4">
              <FieldSelect
                id="alignment"
                label="Alineacion del texto"
                v-model="form.alignment"
                :options="alignmentOptions"
              />
            </div>

            <div class="col-md-4">
              <FieldSwitch
                id="show-contact"
                label="Mostrar informacion de contacto"
                v-model="form.show_contact_info"
              />
            </div>

            <div class="col-md-4">
              <FieldSwitch
                id="show-social"
                label="Mostrar redes sociales"
                v-model="form.show_social_links"
              />
            </div>

            <div v-if="form.show_social_links" class="col-12">
              <label class="form-label">Enlaces de redes sociales</label>
              <div v-for="(social, index) in form.social_links" :key="index" class="row g-3 mb-2 p-2 bg-light rounded">
                <div class="col-md-3">
                  <FieldSelect
                    :id="'social-platform-' + index"
                    label="Plataforma"
                    v-model="social.platform"
                    :options="platformOptions"
                  />
                </div>
                <div class="col-md-6">
                  <FieldUrl
                    :id="'social-url-' + index"
                    label="URL"
                    v-model="social.url"
                    placeholder="https://..."
                  />
                </div>
                <div class="col-md-2">
                  <FieldText
                    :id="'social-name-' + index"
                    label="Nombre (opcional)"
                    v-model="social.name"
                    placeholder="Nombre"
                  />
                </div>
                <div class="col-md-1 d-flex align-items-end">
                  <button type="button" class="btn btn-outline-danger" @click="removeSocial(index)">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
              <button type="button" class="btn btn-outline-primary btn-sm mt-2" @click="addSocial">
                <i class="bi bi-plus me-1"></i>Agregar red social
              </button>
            </div>

            <div class="col-md-4">
              <FieldSwitch
                id="is-active"
                label="Hero activo"
                v-model="form.is_active"
              />
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              <span v-if="sending">Guardando...</span>
              <span v-else>Guardar cambios</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldUrl from '@/Components/Fields/FieldUrl.vue'

const props = defineProps({
  business: Object,
  hero: Object,
})

const page = usePage()
const business = computed(() => page.props.business)
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
        { label: 'Hero', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Hero', active: true },
  ]
})

const linkTypeOptions = [
  { value: 'external', label: 'Externo (URL)' },
  { value: 'internal', label: 'Interno (Ancla)' },
]

const anchorOptions = [
  { value: 'services', label: 'Servicios' },
  { value: 'gallery', label: 'Galeria' },
  { value: 'products', label: 'Productos' },
  { value: 'appointments', label: 'Reservas' },
  { value: 'contact', label: 'Contacto' },
  { value: 'reviews', label: 'Reseñas' },
  { value: 'locations', label: 'Ubicaciones' },
  { value: 'promotions', label: 'Promociones' },
]

const styleOptions = [
  { value: 'primary', label: 'Primario' },
  { value: 'secondary', label: 'Secundario' },
  { value: 'outline', label: 'Outline' },
]

const backgroundTypeOptions = [
  { value: 'color', label: 'Color solido' },
  { value: 'gradient', label: 'Degradado' },
  { value: 'image', label: 'Imagen' },
]

const alignmentOptions = [
  { value: 'left', label: 'Izquierda' },
  { value: 'center', label: 'Centro' },
  { value: 'right', label: 'Derecha' },
]

const platformOptions = [
  { value: 'facebook', label: 'Facebook' },
  { value: 'instagram', label: 'Instagram' },
  { value: 'twitter', label: 'Twitter' },
  { value: 'linkedin', label: 'LinkedIn' },
  { value: 'youtube', label: 'YouTube' },
  { value: 'tiktok', label: 'TikTok' },
  { value: 'whatsapp', label: 'WhatsApp' },
  { value: 'telegram', label: 'Telegram' },
]

const form = useForm({
  title: props.hero?.title || '',
  subtitle: props.hero?.subtitle || '',
  text_aux: props.hero?.text_aux || '',
  background_type: props.hero?.background_type || 'gradient',
  background_color: props.hero?.background_color || null,
  background_gradient_start: props.hero?.background_gradient_start || null,
  background_gradient_end: props.hero?.background_gradient_end || null,
  background_image: null,
  background_image_path: props.hero?.background_image_path || '',
  alignment: props.hero?.alignment || 'left',
  buttons: props.hero?.buttons || [],
  social_links: props.hero?.social_links || [],
  show_contact_info: props.hero?.show_contact_info ?? true,
  show_social_links: props.hero?.show_social_links ?? false,
  is_active: props.hero?.is_active ?? true,
})

const sending = ref(false)

const addButton = () => {
  form.buttons.push({ text: '', link_type: 'external', url: '', anchor: 'services', style: 'primary' })
}

const removeButton = (index) => {
  form.buttons.splice(index, 1)
}

const addSocial = () => {
  form.social_links.push({ platform: 'instagram', url: '', name: '' })
}

const removeSocial = (index) => {
  form.social_links.splice(index, 1)
}

const handleImageUpload = (event) => {
  form.background_image = event.target.files[0]
}

const cleanColor = (value) => {
  if (!value || value === '#000000') return null
  return value
}

const submit = () => {
  sending.value = true

  form.transform(data => {
    const fd = new FormData()
    Object.keys(data).forEach(key => {
      if (key === 'buttons') {
        fd.append(key, JSON.stringify(data[key]))
      } else if (key === 'background_image') {
        if (data[key]) fd.append(key, data[key])
      } else if (key === 'background_color') {
        fd.append(key, cleanColor(data[key]) || '')
      } else if (key === 'background_gradient_start') {
        fd.append(key, cleanColor(data[key]) || '')
      } else if (key === 'background_gradient_end') {
        fd.append(key, cleanColor(data[key]) || '')
      } else if (key !== 'background_image_path') {
        fd.append(key, data[key])
      }
    })
    return fd
  })

  form.post(`/member/businesses/${props.business.id}/hero`, {
    forceFormData: true,
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
