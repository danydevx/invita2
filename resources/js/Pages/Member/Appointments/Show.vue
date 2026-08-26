<template>
  <MemberLayout>
    <Head :title="`Cita - ${listing.name}`" />

    <PageHeader
      :title="'Detalle de Cita'"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing.id}/appointments`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div class="row g-4">
          <div class="col-12 col-md-6">
            <h5 class="mb-3">Informacion del Cliente</h5>
            <dl class="row mb-0">
              <dt class="col-sm-4 text-muted">Nombre</dt>
              <dd class="col-sm-8">{{ appointment.customer_name }}</dd>

              <dt class="col-sm-4 text-muted">Email</dt>
              <dd class="col-sm-8">{{ appointment.customer_email || '-' }}</dd>

              <dt class="col-sm-4 text-muted">Telefono</dt>
              <dd class="col-sm-8">{{ appointment.customer_phone || '-' }}</dd>
            </dl>
          </div>

          <div class="col-12 col-md-6">
            <h5 class="mb-3">Informacion de la Cita</h5>
            <dl class="row mb-0">
              <dt class="col-sm-4 text-muted">Fecha</dt>
              <dd class="col-sm-8">{{ formatDate(appointment.appointment_date) }}</dd>

              <dt class="col-sm-4 text-muted">Hora</dt>
              <dd class="col-sm-8">{{ appointment.start_time }} - {{ appointment.end_time }}</dd>

              <dt class="col-sm-4 text-muted">Servicio</dt>
              <dd class="col-sm-8">{{ appointment.service?.name || '-' }}</dd>

              <dt class="col-sm-4 text-muted">Ubicacion</dt>
              <dd class="col-sm-8">{{ appointment.location?.name || '-' }}</dd>

              <dt class="col-sm-4 text-muted">Estado</dt>
              <dd class="col-sm-8">
                <span :class="statusClass(appointment.status)" class="badge">
                  {{ appointment.status_label }}
                </span>
              </dd>
            </dl>
          </div>

          <div v-if="appointment.notes" class="col-12">
            <h5 class="mb-3">Notas</h5>
            <p class="mb-0">{{ appointment.notes }}</p>
          </div>

          <div class="col-12">
            <hr />
            <h5 class="mb-3">Cambiar Estado</h5>
            <div class="d-flex flex-wrap gap-2">
              <button
                v-for="option in statusOptions"
                :key="option.value"
                class="btn"
                :class="appointment.status === option.value ? `btn-${option.color}` : `btn-outline-${option.color}`"
                :disabled="appointment.status === option.value || appointment.status === 'cancelled' || appointment.status === 'completed'"
                @click="updateStatus(option.value)"
              >
                {{ option.label }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const appointment = computed(() => page.props.appointment)
const businessMenu = computed(() => page.props.businessMenu || [])

const statusOptions = [
  { value: 'pending', label: 'Pendiente', color: 'warning' },
  { value: 'confirmed', label: 'Confirmar', color: 'success' },
  { value: 'completed', label: 'Completar', color: 'primary' },
  { value: 'no_show', label: 'No asistio', color: 'danger' },
  { value: 'cancelled', label: 'Cancelar', color: 'secondary' },
]

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Citas', href: `/member/listings/${listing.value.id}/appointments` },
  { label: appointment.value?.customer_name },
])

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
    confirmed: 'bg-success',
    cancelled: 'bg-secondary',
    completed: 'bg-primary',
    no_show: 'bg-danger',
  }
  return classes[status] || 'bg-secondary'
}

const updateStatus = (status) => {
  router.put(`/member/listings/${listing.value.id}/appointments/${appointment.value.id}`, {
    status,
  }, { preserveScroll: true })
}
</script>
