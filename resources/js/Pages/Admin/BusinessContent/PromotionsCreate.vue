<template>
  <AdminLayout>
    <Head :title="`Nueva Promocion - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link :href="`/admin/businesses/${business.id}/promotions`" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
        <h1 class="h4 mb-1 mt-1">Nueva Promocion</h1>
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
                <option :value="null">Todas las ubicaciones</option>
                <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Descripcion</label>
              <textarea v-model="form.description" class="form-control" rows="3"></textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Imagen de la promocion</label>
              <input
                ref="imageInput"
                type="file"
                class="form-control"
                accept="image/jpeg,image/png,image/webp,image/gif"
                @change="handleImageChange"
              />
              <div v-if="imagePreview" class="mt-2">
                <img :src="imagePreview" class="img-thumbnail" style="max-height: 150px;" alt="Preview" />
              </div>
              <small class="text-muted">JPG, PNG o WebP, max 5MB</small>
            </div>

            <div class="col-md-4">
              <label class="form-label">Precio Regular</label>
              <input type="number" v-model="form.regular_price" class="form-control" min="0" step="0.01" />
            </div>

            <div class="col-md-4">
              <label class="form-label">Precio Promo</label>
              <input type="number" v-model="form.promotion_price" class="form-control" min="0" step="0.01" />
            </div>

            <div class="col-md-4">
              <label class="form-label">Codigo de Cupon</label>
              <input type="text" v-model="form.coupon_code" class="form-control" />
            </div>

            <div class="col-md-6">
              <label class="form-label">Fecha de Inicio</label>
              <input type="datetime-local" v-model="form.starts_at" class="form-control" />
            </div>

            <div class="col-md-6">
              <label class="form-label">Fecha de Vencimiento</label>
              <input type="datetime-local" v-model="form.expires_at" class="form-control" />
            </div>

            <div class="col-md-4">
              <label class="form-label">Orden</label>
              <input type="number" v-model="form.sort_order" class="form-control" min="0" />
            </div>

            <div class="col-md-8 d-flex align-items-end">
              <div class="form-check mb-3 w-100">
                <input type="checkbox" v-model="form.is_active" class="form-check-input" id="is_active" />
                <label class="form-check-label" for="is_active">Activo</label>
              </div>
            </div>

            <div class="col-12">
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Guardando...' : 'Guardar' }}
              </button>
              <Link :href="`/admin/businesses/${business.id}/promotions`" class="btn btn-outline-secondary ms-2">
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

const imageInput = ref(null)
const imagePreview = ref(null)

const form = reactive({
  name: '',
  description: '',
  image: '',
  business_location_id: null,
  regular_price: '',
  promotion_price: '',
  coupon_code: '',
  starts_at: '',
  expires_at: '',
  sort_order: 0,
  is_active: true,
})

const handleImageChange = (e) => {
  const file = e.target.files[0]
  if (!file) return

  const maxSize = 5 * 1024 * 1024
  if (file.size > maxSize) {
    alert('El archivo supera el tamaño máximo de 5MB.')
    return
  }

  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif']
  if (!allowedTypes.includes(file.type)) {
    alert('Solo se permiten imágenes (JPEG, PNG, WebP, GIF).')
    return
  }

  const reader = new FileReader()
  reader.onload = (e) => {
    form.image = e.target.result
    imagePreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const submit = () => {
  router.post(`/admin/businesses/${business.value.id}/promotions`, form)
}
</script>
