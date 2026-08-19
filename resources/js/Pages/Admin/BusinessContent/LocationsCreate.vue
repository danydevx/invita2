<template>
  <AdminLayout>
    <Head :title="`Nueva Ubicacion - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link :href="`/admin/businesses/${business.id}/locations`" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
        <h1 class="h4 mb-1 mt-1">Nueva Ubicacion</h1>
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
              <label class="form-label">Telefono</label>
              <input type="text" v-model="form.phone" class="form-control" />
            </div>

            <div class="col-12">
              <label class="form-label">Direccion *</label>
              <input type="text" v-model="form.address_line_1" class="form-control" required />
              <div v-if="errors.address_line_1" class="text-danger small">{{ errors.address_line_1 }}</div>
            </div>

            <div class="col-12">
              <label class="form-label">Direccion 2</label>
              <input type="text" v-model="form.address_line_2" class="form-control" />
            </div>

            <div class="col-md-4">
              <label class="form-label">Ciudad *</label>
              <input type="text" v-model="form.city" class="form-control" required />
              <div v-if="errors.city" class="text-danger small">{{ errors.city }}</div>
            </div>

            <div class="col-md-4">
              <label class="form-label">Estado/Provincia</label>
              <input type="text" v-model="form.state" class="form-control" />
            </div>

            <div class="col-md-4">
              <label class="form-label">Codigo Postal</label>
              <input type="text" v-model="form.postal_code" class="form-control" />
            </div>

            <div class="col-md-6">
              <label class="form-label">Pais</label>
              <input type="text" v-model="form.country" class="form-control" />
            </div>

            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" v-model="form.email" class="form-control" />
            </div>

            <div class="col-12">
              <MapPicker
                label="Ubicacion en el mapa"
                :lat="form.latitude"
                :lng="form.longitude"
                @update:lat="form.latitude = $event"
                @update:lng="form.longitude = $event"
              />
            </div>

            <div class="col-12">
              <label class="form-label">Como llegar (URL de Google Maps)</label>
              <input type="url" v-model="form.directions_url" class="form-control" placeholder="https://www.google.com/maps/dir/?api=1..." />
            </div>

            <div class="col-12">
              <div class="form-check">
                <input type="checkbox" v-model="form.is_primary" class="form-check-input" id="is_primary" />
                <label class="form-check-label" for="is_primary">Ubicacion principal</label>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input type="checkbox" v-model="form.is_active" class="form-check-input" id="is_active" />
                <label class="form-check-label" for="is_active">Activa</label>
              </div>
            </div>

            <div class="col-12">
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Guardando...' : 'Guardar' }}
              </button>
              <Link :href="`/admin/businesses/${business.id}/locations`" class="btn btn-outline-secondary ms-2">
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
import { computed, reactive } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import MapPicker from '@/Components/MapPicker.vue'

const page = usePage()
const business = computed(() => page.props.business)
const errors = computed(() => page.props.errors || {})
const sending = computed(() => false)

const form = reactive({
  name: '',
  address_line_1: '',
  address_line_2: '',
  city: '',
  state: '',
  postal_code: '',
  country: '',
  phone: '',
  email: '',
  latitude: '',
  longitude: '',
  directions_url: '',
  is_primary: false,
  is_active: true,
})

const submit = () => {
  router.post(`/admin/businesses/${business.value.id}/locations`, form)
}
</script>
