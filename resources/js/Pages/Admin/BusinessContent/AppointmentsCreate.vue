<template>
  <AdminLayout>
    <Head :title="`Nueva Cita - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link :href="`/admin/businesses/${business.id}/appointments`" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
        <h1 class="h4 mb-1 mt-1">Nueva Cita</h1>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="customer_name" class="form-label">Nombre del cliente *</label>
              <input
                id="customer_name"
                type="text"
                class="form-control"
                v-model="form.customer_name"
                required
              />
              <div v-if="errors.customer_name" class="text-danger small">{{ errors.customer_name }}</div>
            </div>

            <div class="col-md-6">
              <label for="customer_email" class="form-label">Email del cliente *</label>
              <input
                id="customer_email"
                type="email"
                class="form-control"
                v-model="form.customer_email"
                required
              />
              <div v-if="errors.customer_email" class="text-danger small">{{ errors.customer_email }}</div>
            </div>

            <div class="col-md-6">
              <label for="customer_phone" class="form-label">Telefono</label>
              <input
                id="customer_phone"
                type="text"
                class="form-control"
                v-model="form.customer_phone"
              />
            </div>

            <div class="col-md-6">
              <label for="business_service_id" class="form-label">Servicio *</label>
              <select
                id="business_service_id"
                class="form-select"
                v-model="form.business_service_id"
                required
              >
                <option :value="null" disabled>Seleccionar servicio</option>
                <option v-for="svc in services" :key="svc.id" :value="svc.id">
                  {{ svc.name }} ({{ svc.duration_minutes }} min)
                </option>
              </select>
              <div v-if="errors.business_service_id" class="text-danger small">{{ errors.business_service_id }}</div>
            </div>

            <div class="col-md-6">
              <label for="business_location_id" class="form-label">Ubicacion *</label>
              <select
                id="business_location_id"
                class="form-select"
                v-model="form.business_location_id"
                required
              >
                <option :value="null" disabled>Seleccionar ubicacion</option>
                <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                  {{ loc.name }}
                </option>
              </select>
              <div v-if="errors.business_location_id" class="text-danger small">{{ errors.business_location_id }}</div>
            </div>

            <div class="col-md-3">
              <label for="appointment_date" class="form-label">Fecha *</label>
              <input
                id="appointment_date"
                type="date"
                class="form-control"
                v-model="form.appointment_date"
                required
                :min="today"
              />
              <div v-if="errors.appointment_date" class="text-danger small">{{ errors.appointment_date }}</div>
            </div>

            <div class="col-md-3">
              <label for="start_time" class="form-label">Hora *</label>
              <input
                id="start_time"
                type="time"
                class="form-control"
                v-model="form.start_time"
                required
              />
              <div v-if="errors.start_time" class="text-danger small">{{ errors.start_time }}</div>
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
                {{ sending ? 'Guardando...' : 'Crear Cita' }}
              </button>
              <Link :href="`/admin/businesses/${business.id}/appointments`" class="btn btn-outline-secondary ms-2">
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
const services = computed(() => page.props.services || [])
const locations = computed(() => page.props.locations || [])
const errors = computed(() => page.props.errors || {})

const sending = ref(false)
const today = computed(() => new Date().toISOString().split('T')[0])

const form = reactive({
  customer_name: '',
  customer_email: '',
  customer_phone: '',
  business_service_id: null,
  business_location_id: null,
  appointment_date: '',
  start_time: '',
  notes: '',
})

const submit = () => {
  sending.value = true
  router.post(`/admin/businesses/${business.value.id}/appointments`, form, {
    preserveScroll: true,
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
