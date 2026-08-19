<template>
  <AdminLayout>
    <Head :title="`Citas - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link href="/admin/businesses" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Negocios
        </Link>
        <h1 class="h4 mb-1 mt-1">{{ business.name }} - Citas</h1>
      </div>
      <div>
        <Link :href="`/admin/businesses/${business.id}/appointments/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nueva Cita
        </Link>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Cliente</th>
                <th scope="col">Servicio</th>
                <th scope="col">Ubicacion</th>
                <th scope="col">Fecha</th>
                <th scope="col">Hora</th>
                <th scope="col">Estado</th>
                <th scope="col" class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="appointments.data.length === 0">
                <td colspan="7" class="text-center text-muted py-4">
                  No hay citas registradas.
                </td>
              </tr>
              <tr v-for="apt in appointments.data" :key="apt.id">
                <td>
                  <div>{{ apt.customer_name }}</div>
                  <small class="text-muted">{{ apt.customer_email }}</small>
                </td>
                <td>{{ apt.service?.name || '-' }}</td>
                <td>{{ apt.location?.name || '-' }}</td>
                <td>{{ formatDate(apt.appointment_date) }}</td>
                <td>{{ apt.start_time }}</td>
                <td>
                  <span class="badge" :class="statusClass(apt.status)">{{ apt.status_label }}</span>
                </td>
                <td class="text-end">
                  <div class="d-flex gap-1 justify-content-end">
                    <Link :href="`/admin/businesses/${business.id}/appointments/${apt.id}/edit`" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i>
                    </Link>
                    <button
                      v-if="apt.status !== 'cancelled'"
                      class="btn btn-sm btn-outline-warning"
                      @click="cancelAppointment(apt)"
                    >
                      <i class="bi bi-x-lg"></i>
                    </button>
                    <button
                      class="btn btn-sm btn-outline-danger"
                      @click="deleteAppointment(apt)"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="appointments.links" class="d-flex justify-content-center mt-4">
          <Pagination :links="appointments.links" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const page = usePage()
const business = computed(() => page.props.business)
const appointments = computed(() => page.props.appointments || { data: [], links: [] })

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-AR', {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
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

const cancelAppointment = (apt) => {
  if (confirm('¿Estás seguro de cancelar esta cita?')) {
    router.post(`/admin/businesses/${business.value.id}/appointments/${apt.id}/cancel`, {}, {
      preserveScroll: true,
    })
  }
}

const deleteAppointment = (apt) => {
  if (confirm('¿Estás seguro de eliminar esta cita? Esta acción no se puede deshacer.')) {
    router.delete(`/admin/businesses/${business.value.id}/appointments/${apt.id}`, {
      preserveScroll: true,
    })
  }
}
</script>
