<template>
  <AdminLayout>
    <Head :title="`Hero - ${business.name}`" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-1">Hero</h1>
          <p class="text-muted mb-0">{{ business.name }}</p>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <form @submit.prevent="submit">
            <div class="row g-4">
              <div class="col-12">
                <h5 class="border-bottom pb-2 mb-3">Contenido</h5>
              </div>

              <div class="col-md-6">
                <label class="form-label">Título</label>
                <input v-model="form.title" type="text" class="form-control" placeholder="Tu título aquí">
              </div>

              <div class="col-md-6">
                <label class="form-label">Subtítulo</label>
                <input v-model="form.subtitle" type="text" class="form-control" placeholder="Tu subtítulo aquí">
              </div>

              <div class="col-12">
                <label class="form-label">Texto auxiliar</label>
                <textarea v-model="form.text_aux" class="form-control" rows="2" placeholder="Texto adicional que aparece debajo del subtítulo"></textarea>
              </div>

              <div class="col-12">
                <h5 class="border-bottom pb-2 mb-3 mt-4">Botones CTA</h5>
              </div>

              <div class="col-12">
                <div v-for="(btn, index) in form.buttons" :key="index" class="row g-3 mb-3 p-3 bg-light rounded">
                  <div class="col-md-3">
                    <label class="form-label">Texto</label>
                    <input v-model="btn.text" type="text" class="form-control" placeholder="Reservar cita">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Tipo de enlace</label>
                    <select v-model="btn.link_type" class="form-select">
                      <option value="external">Externo (URL)</option>
                      <option value="internal">Interno (Ancla)</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">{{ btn.link_type === 'internal' ? 'Sección' : 'URL' }}</label>
                    <select v-if="btn.link_type === 'internal'" v-model="btn.anchor" class="form-select">
                      <option value="services">Servicios</option>
                      <option value="gallery">Galería</option>
                      <option value="products">Productos</option>
                      <option value="appointments">Reservas</option>
                      <option value="contact">Contacto</option>
                      <option value="reviews">Reseñas</option>
                      <option value="locations">Ubicaciones</option>
                      <option value="promotions">Promociones</option>
                    </select>
                    <input v-else v-model="btn.url" type="text" class="form-control" placeholder="https://...">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Estilo</label>
                    <select v-model="btn.style" class="form-select">
                      <option value="primary">Primario</option>
                      <option value="secondary">Secundario</option>
                      <option value="outline">Outline</option>
                    </select>
                  </div>
                  <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger" @click="removeButton(index)">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>

                <button type="button" class="btn btn-outline-primary btn-sm" @click="addButton">
                  <i class="bi bi-plus me-1"></i>Agregar botón
                </button>
              </div>

              <div class="col-12">
                <h5 class="border-bottom pb-2 mb-3 mt-4">Fondo</h5>
              </div>

              <div class="col-md-4">
                <label class="form-label">Tipo de fondo</label>
                <select v-model="form.background_type" class="form-select">
                  <option value="color">Color sólido</option>
                  <option value="gradient">Degradado</option>
                  <option value="image">Imagen</option>
                </select>
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
                <h5 class="border-bottom pb-2 mb-3 mt-4">Alineación y opciones</h5>
              </div>

              <div class="col-md-4">
                <label class="form-label">Alineación del texto</label>
                <select v-model="form.alignment" class="form-select">
                  <option value="left">Izquierda</option>
                  <option value="center">Centro</option>
                  <option value="right">Derecha</option>
                </select>
              </div>

              <div class="col-md-4">
                <div class="form-check form-switch mt-4">
                  <input v-model="form.show_contact_info" class="form-check-input" type="checkbox" id="showContact">
                  <label class="form-check-label" for="showContact">Mostrar información de contacto</label>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-check form-switch mt-4">
                  <input v-model="form.show_social_links" class="form-check-input" type="checkbox" id="showSocial">
                  <label class="form-check-label" for="showSocial">Mostrar redes sociales</label>
                </div>
              </div>

              <div v-if="form.show_social_links" class="col-12">
                <label class="form-label">Enlaces de redes sociales</label>
                <div v-for="(social, index) in form.social_links" :key="index" class="row g-3 mb-2 p-2 bg-light rounded">
                  <div class="col-md-3">
                    <select v-model="social.platform" class="form-select">
                      <option value="facebook">Facebook</option>
                      <option value="instagram">Instagram</option>
                      <option value="twitter">Twitter</option>
                      <option value="linkedin">LinkedIn</option>
                      <option value="youtube">YouTube</option>
                      <option value="tiktok">TikTok</option>
                      <option value="whatsapp">WhatsApp</option>
                      <option value="telegram">Telegram</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <input v-model="social.url" type="url" class="form-control" placeholder="https://...">
                  </div>
                  <div class="col-md-2">
                    <input v-model="social.name" type="text" class="form-control" placeholder="Nombre (opcional)">
                  </div>
                  <div class="col-md-1">
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
                <div class="form-check form-switch mt-4">
                  <input v-model="form.is_active" class="form-check-input" type="checkbox" id="isActive">
                  <label class="form-check-label" for="isActive">Hero activo</label>
                </div>
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
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  business: Object,
  hero: Object,
})

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

let sending = false

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
  sending = true
  const formData = { ...form }
  formData.background_color = cleanColor(form.background_color)
  formData.background_gradient_start = cleanColor(form.background_gradient_start)
  formData.background_gradient_end = cleanColor(form.background_gradient_end)
  form.post(`/admin/businesses/${props.business.id}/hero`, {
    data: formData,
    forceFormData: true,
    onFinish: () => {
      sending = false
    },
  })
}
</script>
