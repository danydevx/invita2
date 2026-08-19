<template>
  <AdminLayout>
    <Head :title="`Nuevo Lead - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link :href="`/admin/businesses/${business.id}/leads`" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
        <h1 class="h4 mb-1 mt-1">Nuevo Lead</h1>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="name" class="form-label">Nombre *</label>
              <input
                id="name"
                type="text"
                class="form-control"
                v-model="form.name"
                required
              />
              <div v-if="errors.name" class="text-danger small">{{ errors.name }}</div>
            </div>

            <div class="col-md-6">
              <label for="email" class="form-label">Email *</label>
              <input
                id="email"
                type="email"
                class="form-control"
                v-model="form.email"
                required
              />
              <div v-if="errors.email" class="text-danger small">{{ errors.email }}</div>
            </div>

            <div class="col-md-6">
              <label for="phone" class="form-label">Telefono</label>
              <input
                id="phone"
                type="text"
                class="form-control"
                v-model="form.phone"
              />
            </div>

            <div class="col-md-6">
              <label for="business_location_id" class="form-label">Ubicacion</label>
              <select id="business_location_id" class="form-select" v-model="form.business_location_id">
                <option :value="null">Sin ubicacion</option>
                <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="source" class="form-label">Fuente</label>
              <select id="source" class="form-select" v-model="form.source">
                <option value="">Seleccionar...</option>
                <option value="manual">Manual</option>
                <option value="website">Website</option>
                <option value="phone">Telefono</option>
                <option value="walk_in">Visita directa</option>
                <option value="referral">Referido</option>
                <option value="social_media">Redes sociales</option>
                <option value="other">Otro</option>
              </select>
            </div>

            <div class="col-12">
              <label for="notes" class="form-label">Notas</label>
              <textarea
                id="notes"
                class="form-control"
                rows="3"
                v-model="form.notes"
                placeholder="Notas adicionales..."
              ></textarea>
            </div>

            <div class="col-12">
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Guardando...' : 'Crear Lead' }}
              </button>
              <Link :href="`/admin/businesses/${business.id}/leads`" class="btn btn-outline-secondary ms-2">
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

const sending = ref(false)

const form = reactive({
  name: '',
  email: '',
  phone: '',
  notes: '',
  business_location_id: null,
  source: '',
})

const submit = () => {
  sending.value = true
  router.post(`/admin/businesses/${business.value.id}/leads`, form, {
    preserveScroll: true,
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
