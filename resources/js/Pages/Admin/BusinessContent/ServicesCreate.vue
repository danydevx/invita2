<template>
  <AdminLayout>
    <Head :title="`Nuevo Servicio - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link :href="`/admin/businesses/${business.id}/services`" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
        <h1 class="h4 mb-1 mt-1">Nuevo Servicio</h1>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre *</label>
              <input type="text" v-model="form.name" class="form-control" required />
              <div v-if="errors.name" class="text-danger small">{{ errors.name }}</div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Ubicacion</label>
              <select v-model="form.business_location_id" class="form-select">
                <option :value="null">General (sin ubicacion)</option>
                <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Descripcion</label>
              <textarea v-model="form.description" class="form-control" rows="3"></textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Imagen del servicio</label>
              <input
                ref="imageInput"
                type="file"
                accept="image/*"
                class="form-control"
                @change="handleImageChange"
              />
              <div v-if="imagePreview" class="mt-2">
                <img :src="imagePreview" class="img-thumbnail" style="max-height: 150px;" alt="Preview" />
              </div>
              <div class="text-muted small mt-1"> JPG, PNG o WebP, max 2MB</div>
            </div>

            <div class="col-md-4">
              <label class="form-label">Duracion (minutos) *</label>
              <input type="number" v-model="form.duration_minutes" class="form-control" required min="1" />
            </div>

            <div class="col-md-4">
              <label class="form-label">Precio</label>
              <input type="number" v-model="form.price" class="form-control" min="0" step="0.01" />
            </div>

            <div class="col-md-4">
              <label class="form-label">Orden</label>
              <input type="number" v-model="form.sort_order" class="form-control" min="0" />
            </div>

            <div class="col-12">
              <div class="form-check">
                <input type="checkbox" v-model="form.allows_online_booking" class="form-check-input" id="allows_online_booking" />
                <label class="form-check-label" for="allows_online_booking">Permite reserva online</label>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input type="checkbox" v-model="form.whatsapp_contact" class="form-check-input" id="whatsapp_contact" />
                <label class="form-check-label" for="whatsapp_contact">Contactar por WhatsApp</label>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input type="checkbox" v-model="form.is_active" class="form-check-input" id="is_active" />
                <label class="form-check-label" for="is_active">Activo</label>
              </div>
            </div>

            <div class="col-12">
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Guardando...' : 'Guardar' }}
              </button>
              <Link :href="`/admin/businesses/${business.id}/services`" class="btn btn-outline-secondary ms-2">
                Cancelar
              </Link>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const business = computed(() => page.props.business)
const locations = computed(() => page.props.locations || [])
const errors = computed(() => page.props.errors || {})
const sending = computed(() => false)

const imagePreview = ref(null)

const form = reactive({
  name: '',
  description: '',
  image: '',
  duration_minutes: 30,
  price: '',
  business_location_id: null,
  allows_online_booking: true,
  whatsapp_contact: false,
  is_active: true,
  sort_order: 0,
})

const handleImageChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    imagePreview.value = URL.createObjectURL(file)
    const reader = new FileReader()
    reader.onload = (e) => {
      form.image = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const submit = () => {
  router.post(`/admin/businesses/${business.value.id}/services`, form)
}
</script>
