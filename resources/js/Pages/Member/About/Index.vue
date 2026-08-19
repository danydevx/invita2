<template>
  <MemberLayout>
    <Head :title="`Acerca de - ${business.name}`" />

    <PageHeader
      title="Acerca de"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
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
        <form @submit.prevent="submit">
          <div class="row g-4">
            <div class="col-12">
              <h5 class="border-bottom pb-2 mb-3">Contenido</h5>
            </div>

            <div class="col-md-6">
              <FieldText
                id="about-title"
                label="Titulo"
                v-model="form.title"
                placeholder="Nuestra historia"
              />
            </div>

            <div class="col-md-6">
              <FieldText
                id="about-subtitle"
                label="Subtitulo"
                v-model="form.subtitle"
                placeholder="Conocé quienes somos"
              />
            </div>

            <div class="col-12">
              <FieldTextarea
                id="about-description"
                label="Descripcion"
                v-model="form.description"
                placeholder="Escribi la descripcion de tu negocio..."
                :rows="4"
              />
            </div>

            <div class="col-12">
              <h5 class="border-bottom pb-2 mb-3 mt-4">Imagenes</h5>
            </div>

            <div class="col-md-6">
              <label class="form-label">Imagen principal</label>
              <input
                ref="imageInput"
                type="file"
                class="form-control"
                accept="image/jpeg,image/png,image/webp,image/gif"
                @change="handleImageChange"
              />
              <div v-if="imagePreview || form.image_path" class="mt-2">
                <img :src="imagePreview || form.image_path" class="img-thumbnail" style="max-height: 200px;" alt="Preview" />
              </div>
              <small class="text-muted d-block">JPG, PNG o WebP, max 5MB.</small>
              <button
                v-if="form.image_path && !form.remove_image"
                type="button"
                class="btn btn-outline-danger btn-sm mt-2"
                @click="removeImage('image')"
              >
                <i class="bi bi-trash me-1"></i>Eliminar imagen
              </button>
            </div>

            <div class="col-md-6">
              <label class="form-label">Logotipo</label>
              <input
                ref="logoInput"
                type="file"
                class="form-control"
                accept="image/jpeg,image/png,image/webp,image/gif"
                @change="handleLogoChange"
              />
              <div v-if="logoPreview || form.logo_path" class="mt-2">
                <img :src="logoPreview || form.logo_path" class="img-thumbnail" style="max-height: 150px;" alt="Logo Preview" />
              </div>
              <small class="text-muted d-block">JPG, PNG o WebP, max 5MB.</small>
              <button
                v-if="form.logo_path && !form.remove_logo"
                type="button"
                class="btn btn-outline-danger btn-sm mt-2"
                @click="removeImage('logo')"
              >
                <i class="bi bi-trash me-1"></i>Eliminar logotipo
              </button>
            </div>

            <div class="col-md-4">
              <FieldSwitch
                id="is-active"
                label="Acerca de activo"
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
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const props = defineProps({
  business: Object,
  about: Object,
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
        { label: 'Acerca De', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Acerca De', active: true },
  ]
})

const imagePreview = ref(null)
const logoPreview = ref(null)
const imageInput = ref(null)
const logoInput = ref(null)

const form = useForm({
  title: props.about?.title || '',
  subtitle: props.about?.subtitle || '',
  description: props.about?.description || '',
  image: null,
  image_path: props.about?.image_path || '',
  logo: null,
  logo_path: props.about?.logo_path || '',
  remove_image: false,
  remove_logo: false,
  is_active: props.about?.is_active ?? true,
})

const sending = ref(false)

const handleImageChange = (event) => {
  const file = event.target.files[0]
  if (!file) return

  if (file.size > 5 * 1024 * 1024) {
    alert('El archivo supera el tamaño máximo de 5MB.')
    return
  }

  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif']
  if (!allowedTypes.includes(file.type)) {
    alert('Solo se permiten imágenes (JPEG, PNG, WebP, GIF).')
    return
  }

  form.image = file
  imagePreview.value = URL.createObjectURL(file)
  form.remove_image = false
}

const handleLogoChange = (event) => {
  const file = event.target.files[0]
  if (!file) return

  if (file.size > 5 * 1024 * 1024) {
    alert('El archivo supera el tamaño máximo de 5MB.')
    return
  }

  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif']
  if (!allowedTypes.includes(file.type)) {
    alert('Solo se permiten imágenes (JPEG, PNG, WebP, GIF).')
    return
  }

  form.logo = file
  logoPreview.value = URL.createObjectURL(file)
  form.remove_logo = false
}

const removeImage = (type) => {
  if (type === 'image') {
    form.remove_image = true
    imagePreview.value = null
    form.image = null
    if (imageInput.value) imageInput.value.value = ''
  } else {
    form.remove_logo = true
    logoPreview.value = null
    form.logo = null
    if (logoInput.value) logoInput.value.value = ''
  }
}

const submit = () => {
  sending.value = true

  const data = new FormData()
  data.append('title', form.title || '')
  data.append('subtitle', form.subtitle || '')
  data.append('description', form.description || '')
  data.append('is_active', form.is_active ? '1' : '0')

  if (form.image) {
    data.append('image', form.image)
  }
  if (form.logo) {
    data.append('logo', form.logo)
  }
  if (form.remove_image) {
    data.append('remove_image', '1')
  }
  if (form.remove_logo) {
    data.append('remove_logo', '1')
  }

  form.post(`/member/businesses/${props.business.id}/about`, {
    forceFormData: true,
    preserveScroll: true,
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
