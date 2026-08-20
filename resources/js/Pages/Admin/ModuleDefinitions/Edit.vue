<template>
  <AdminLayout>
    <Head title="Editar Modulo" />

    <PageHeader title="Editar Modulo" :breadcrumbs="breadcrumbs" backHref="/admin/business-module-definitions" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <FieldText
                id="def-key"
                label="Key"
                v-model="form.key"
                :formError="errors.key"
                required
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="def-name"
                label="Nombre"
                v-model="form.name"
                :formError="errors.name"
                required
              />
            </div>

            <div class="col-12">
              <FieldText
                id="def-description"
                label="Descripcion"
                v-model="form.description"
                :formError="errors.description"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="def-icon"
                label="Icono (Bootstrap Icons)"
                v-model="form.icon"
                :formError="errors.icon"
              />
              <small class="text-muted">
                <a href="https://icons.getbootstrap.com/" target="_blank">Ver iconos disponibles</a>
              </small>
            </div>

            <div class="col-12">
              <div class="mb-3">
                <label class="form-label">Imagen ilustrativa</label>
                <div v-if="definition.image || previewImage" class="mb-2">
                  <div class="position-relative d-inline-block">
                    <img
                      :src="previewImage || definition.image"
                      alt="Imagen del modulo"
                      class="rounded"
                      style="max-width: 200px; max-height: 150px; object-fit: cover;"
                    />
                    <button
                      type="button"
                      class="btn btn-sm btn-danger position-absolute top-0 end-0 translate-middle"
                      style="border-radius: 50%;"
                      @click="removeImage"
                    >
                      <i class="bi bi-x"></i>
                    </button>
                  </div>
                </div>
                <div v-else class="text-muted small mb-2">Sin imagen</div>
                <label
                  for="module-image"
                  class="btn btn-outline-secondary btn-sm"
                >
                  <i class="bi bi-upload me-1"></i>Subir imagen
                </label>
                <input
                  id="module-image"
                  type="file"
                  accept="image/jpeg,image/png,image/gif,image/webp"
                  class="d-none"
                  @change="handleImageChange"
                />
                <div v-if="errors.image" class="text-danger small mt-1">{{ errors.image }}</div>
                <div class="text-muted small mt-1">JPG, PNG o WebP. Max 2MB.</div>
                <input type="hidden" name="remove_image" :value="removeImageFlag ? '1' : '0'" />
              </div>
            </div>

            <div class="col-12 col-md-6">
              <FieldNumber
                id="def-sort"
                label="Orden"
                v-model="form.sort_order"
                :formError="errors.sort_order"
                :min="0"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldSwitch
                id="def-settings"
                label="Tiene configuraciones propias"
                v-model="form.has_settings"
              />
            </div>

            <div class="col-12 col-md-6" v-if="form.has_settings">
              <FieldText
                id="def-settings-url"
                label="URL de Configuracion"
                v-model="form.settings_url"
                :formError="errors.settings_url"
                placeholder="/admin/features"
              />
              <small class="text-muted">Ruta interna para la pagina de configuracion del modulo</small>
            </div>

            <div class="col-12 col-md-6">
              <FieldSwitch
                id="def-active"
                label="Modulo activo"
                v-model="form.is_active"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldSwitch
                id="def-premium"
                label="Modulo premium"
                v-model="form.is_premium"
              />
              <small class="text-muted d-block mb-2">Requiere plan especifico para acceder</small>
            </div>

            <div class="col-12 col-md-6">
              <FieldSwitch
                id="def-show-menu"
                label="Mostrar en menu lateral"
                v-model="form.show_in_menu"
              />
              <small class="text-muted d-block mb-2">Appecer&aacute; en el sidebar de los miembros</small>
            </div>

            <div class="col-12 col-md-6" v-if="form.show_in_menu">
              <FieldText
                id="def-menu-title"
                label="Titulo en el menu"
                v-model="form.menu_title"
                :formError="errors.menu_title"
                placeholder="Ej: Galeria, Citas, Servicios"
              />
              <small class="text-muted">Texto que aparecer&aacute; en el submenu del miembro</small>
            </div>
          </div>

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              {{ sending ? 'Actualizando...' : 'Actualizar Modulo' }}
            </button>
            <Link href="/admin/business-module-definitions" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const props = defineProps({
  definition: {
    type: Object,
    required: true,
  },
})

const page = usePage()
const errors = computed(() => page.props.errors || {})
const sending = ref(false)
const removeImageFlag = ref(false)
const previewImage = ref(null)

const form = reactive({
  key: props.definition.key,
  name: props.definition.name,
  description: props.definition.description || '',
  icon: props.definition.icon || '',
  sort_order: props.definition.sort_order ?? 0,
  has_settings: !!props.definition.has_settings,
  settings_url: props.definition.settings_url || '',
  is_active: !!props.definition.is_active,
  is_premium: !!props.definition.is_premium,
  show_in_menu: !!props.definition.show_in_menu,
  menu_title: props.definition.menu_title || '',
  imageFile: null,
})

const breadcrumbs = [
  { label: 'Modulos de Negocio', href: '/admin/business-module-definitions' },
  { label: 'Editar', active: true },
]

const handleImageChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.imageFile = file
    removeImageFlag.value = false
    const reader = new FileReader()
    reader.onload = (e) => {
      previewImage.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const removeImage = () => {
  removeImageFlag.value = true
  previewImage.value = null
  form.imageFile = null
}

const submit = () => {
  sending.value = true
  const data = new FormData()
  data.append('key', form.key)
  data.append('name', form.name)
  data.append('description', form.description || '')
  data.append('icon', form.icon || '')
  data.append('sort_order', form.sort_order ?? 0)
  data.append('has_settings', form.has_settings ? '1' : '0')
  data.append('settings_url', form.settings_url || '')
  data.append('is_active', form.is_active ? '1' : '0')
  data.append('is_premium', form.is_premium ? '1' : '0')
  data.append('show_in_menu', form.show_in_menu ? '1' : '0')
  data.append('menu_title', form.menu_title || '')
  data.append('_method', 'PUT')

  if (form.imageFile) {
    data.append('image', form.imageFile)
  }

  if (removeImageFlag.value) {
    data.append('remove_image', '1')
  }

  router.post(`/admin/business-module-definitions/${props.definition.id}`, data, {
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
