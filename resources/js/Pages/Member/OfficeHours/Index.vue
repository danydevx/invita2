<template>
  <MemberLayout>
    <Head :title="`Horarios - ${location.name}`" />

    <PageHeader
      :title="'Horarios de Atención'"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing.id}/locations/${location.id}/edit`"
    />

    <div v-if="flashSuccess" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ flashSuccess }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <p class="text-muted mb-0">
          {{ schedules.length }} horario(s) configurado(s) para esta ubicación.
        </p>
      </div>
      <Link
        :href="`/member/listings/${listing.id}/locations/${location.id}/schedules/create`"
        class="btn btn-primary btn-sm"
      >
        <i class="bi bi-plus-lg me-1"></i>
        Nuevo Horario
      </Link>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body p-0">
        <div v-if="schedules.length === 0" class="text-center py-5">
          <i class="bi bi-clock text-muted" style="font-size: 3rem;"></i>
          <p class="text-muted mt-3">No hay horarios configurados.</p>
          <Link
            :href="`/member/listings/${listing.id}/locations/${location.id}/schedules/create`"
            class="btn btn-primary btn-sm"
          >
            <i class="bi bi-plus-lg me-1"></i>
            Crear primer horario
          </Link>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Nombre</th>
                <th>Días</th>
                <th>Horario</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="schedule in schedules" :key="schedule.id">
                <td>
                  <strong>{{ schedule.name }}</strong>
                </td>
                <td>{{ schedule.days_display }}</td>
                <td>
                  <small>{{ schedule.time_display }}</small>
                </td>
                <td>
                  <span v-if="schedule.is_active" class="badge bg-success">Activo</span>
                  <span v-else class="badge bg-secondary">Inactivo</span>
                </td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <Link
                      :href="`/member/listings/${listing.id}/locations/${location.id}/schedules/${schedule.id}/edit`"
                      class="btn btn-outline-secondary"
                      title="Editar"
                    >
                      <i class="bi bi-pencil"></i>
                    </Link>
                    <button
                      type="button"
                      class="btn btn-outline-secondary"
                      title="Clonar"
                      @click="cloneSchedule(schedule.id)"
                    >
                      <i class="bi bi-copy"></i>
                    </button>
                    <button
                      type="button"
                      class="btn btn-outline-danger"
                      title="Eliminar"
                      @click="deleteSchedule(schedule.id)"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const location = computed(() => page.props.location)
const schedules = computed(() => page.props.schedules || [])

const flashSuccess = computed(() => page.props.flash?.success || null)

const businessMenu = computed(() => page.props.listingMenu || [])

const breadcrumbs = computed(() => {
  const biz = businessMenu.value.find(b => b.id === listing.value.id)
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: biz?.name || '', href: `/member/listings/${listing.value.id}/edit` },
    { label: 'Ubicaciones', href: `/member/listings/${listing.value.id}/locations` },
    { label: location.value.name, href: `/member/listings/${listing.value.id}/locations/${location.value.id}/edit` },
    { label: 'Horarios', active: true },
  ]
})

const cloneSchedule = (scheduleId) => {
  if (confirm('¿Clonar este horario?')) {
    router.post(`/member/listings/${listing.value.id}/locations/${location.value.id}/schedules/${scheduleId}/clone`)
  }
}

const deleteSchedule = (scheduleId) => {
  if (confirm('¿Eliminar este horario? Esta acción no se puede deshacer.')) {
    router.delete(`/member/listings/${listing.value.id}/locations/${location.value.id}/schedules/${scheduleId}`)
  }
}
</script>
