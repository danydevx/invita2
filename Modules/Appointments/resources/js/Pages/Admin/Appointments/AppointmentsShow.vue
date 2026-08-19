<template>
  <AdminLayout>
    <Head :title="`Cita - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link :href="`/admin/businesses/${business.id}/appointments`" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
        <h1 class="h4 mb-1 mt-1">Detalle de la Cita</h1>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-6">
            <h6 class="text-muted mb-1">Cliente</h6>
            <p class="mb-1">{{ appointment.customer_name }}</p>
            <p class="mb-1 text-muted small">{{ appointment.customer_email }}</p>
            <p v-if="appointment.customer_phone" class="mb-0 text-muted small">
              <i class="bi bi-telephone me-1"></i>{{ appointment.customer_phone }}
            </p>
          </div>

          <div class="col-md-6">
            <h6 class="text-muted mb-1">Servicio</h6>
            <p class="mb-0">{{ appointment.service?.name || '-' }}</p>
          </div>

          <div class="col-md-6">
            <h6 class="text-muted mb-1">Ubicacion</h6>
            <p class="mb-0">{{ appointment.location?.name || '-' }}</p>
          </div>

          <div class="col-md-6">
            <h6 class="text-muted mb-1">Estado</h6>
            <span class="badge" :class="statusClass(appointment.status)">{{ appointment.status_label }}</span>
          </div>

          <div class="col-md-6">
            <h6 class="text-muted mb-1">Fecha y Hora</h6>
            <p class="mb-0">{{ formatDate(appointment.appointment_date) }} - {{ appointment.start_time }}</p>
          </div>

          <div class="col-12" v-if="appointment.notes">
            <h6 class="text-muted mb-1">Notas</h6>
            <p class="mb-0">{{ appointment.notes }}</p>
          </div>
        </div>

        <div class="mt-4 d-flex gap-2">
          <Link :href="`/admin/businesses/${business.id}/appointments/${appointment.id}/edit`" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i>Editar
          </Link>
          <button
            v-if="appointment.status !== 'cancelled'"
            class="btn btn-outline-warning"
            @click="cancelAppointment"
          >
            <i class="bi bi-x-lg me-1"></i>Cancelar
          </button>
          <button
            class="btn btn-outline-danger"
            @click="deleteAppointment"
          >
            <i class="bi bi-trash me-1"></i>Eliminar
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const business = computed(() => page.props.business)
const appointment = computed(() => page.props.appointment)

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-AR', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const statusClass = (status) => {
  const classes = {
    pending: 'bg-warning text-dark',
    confirmed: 'bg-primary',
    cancelled: 'bg-secondary',
    completed: 'bg-success',
    no_show: 'bg-danger',
  }
  return classes[status] || 'bg-secondary'
}

const cancelAppointment = () => {
  if (confirm('¿Estás seguro de cancelar esta cita?')) {
    router.post(`/admin/businesses/${business.value.id}/appointments/${appointment.value.id}/cancel`, {}, {
      preserveScroll: true,
    })
  }
}

const deleteAppointment = () => {
  if (confirm('¿Estás seguro de eliminar esta cita? Esta acción no se puede deshacer.')) {
    router.delete(`/admin/businesses/${business.value.id}/appointments/${appointment.value.id}`, {
      preserveScroll: true,
    })
  }
}
</script>
