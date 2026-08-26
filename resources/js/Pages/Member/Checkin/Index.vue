<template>
  <MemberLayout>
    <Head :title="`Check-in - ${listing?.name || ''}`" />

    <PageHeader
      title="Check-in"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/modules`"
    />

    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body text-center">
            <h3 class="mb-0">{{ stats.total }}</h3>
            <small class="text-muted">Total Invitados</small>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body text-center">
            <h3 class="mb-0 text-success">{{ stats.checked_in }}</h3>
            <small class="text-muted">Registrados</small>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body text-center">
            <h3 class="mb-0 text-warning">{{ stats.pending }}</h3>
            <small class="text-muted">Pendientes</small>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="checkins.data.length === 0" class="text-center text-muted py-5">
          <i class="bi bi-qr-code-scan display-1"></i>
          <h5 class="mt-3">No hay registros de check-in</h5>
          <p>Los invitados se registrarán cuando lleguen al evento.</p>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Invitado</th>
                <th>Hora de registro</th>
                <th>Notas</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="checkin in checkins.data" :key="checkin.id">
                <td>
                  <strong>{{ checkin.guest?.name || '-' }}</strong>
                  <br />
                  <small class="text-muted">{{ checkin.guest?.email || '' }}</small>
                </td>
                <td>{{ checkin.checkin_time ? formatDate(checkin.checkin_time) : '-' }}</td>
                <td>{{ checkin.notes || '-' }}</td>
                <td>
                  <button
                    class="btn btn-sm btn-outline-danger"
                    @click="deleteCheckin(checkin)"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="checkins.data.length > 0" class="d-flex justify-content-center mt-4">
          <Pagination :links="checkins.links" />
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import Pagination from '@/Components/Member/Pagination.vue'

const props = defineProps({
  listing: Object,
  checkins: Object,
  stats: Object,
})

const page = usePage()
const listing = computed(() => page.props.listing)
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: `/member/listings/${listing.value?.id}/modules` },
  { label: 'Check-in', active: true },
])

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleString('es-MX')
}

const deleteCheckin = (checkin) => {
  if (confirm('¿Eliminar este registro de check-in?')) {
    router.delete(`/member/listings/${listing.value.id}/checkin/${checkin.id}`, {
      preserveScroll: true,
    })
  }
}
</script>
