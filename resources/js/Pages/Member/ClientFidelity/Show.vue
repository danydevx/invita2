<template>
  <MemberLayout>
    <Head :title="`${card?.client_name || 'Tarjeta'} - ${listing?.name || ''}`" />

    <PageHeader
      title="Tarjeta"
      :breadcrumbs="breadcrumbs"
      backHref="/member/dashboard"
      backLabel="Regresar"
    />

    <div class="row">
      <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body text-center">
            <div class="mb-3">
              <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="bi bi-person text-muted fs-1"></i>
              </div>
            </div>
            <h5>{{ card?.client_name }}</h5>
            <p v-if="card?.client_email" class="text-muted mb-1">{{ card.client_email }}</p>
            <p v-if="card?.client_phone" class="text-muted mb-3">{{ card.client_phone }}</p>

            <div class="mb-3">
              <span v-if="card?.is_completed" class="badge bg-success">Completada</span>
              <span v-else-if="card?.is_active" class="badge bg-primary">Activa</span>
              <span v-else class="badge bg-secondary">Inactiva</span>
            </div>

            <div v-if="card?.public_code" class="alert alert-info">
              <strong>Código:</strong> {{ card.public_code }}
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h6 class="mb-3">Acciones</h6>
            <div class="d-grid gap-2">
              <Link
                v-if="card?.is_active && !card?.is_completed"
                :href="`/member/listings/${listing?.id}/fidelity-cards/${card?.id}/scan`"
                method="post"
                class="btn btn-success btn-sm"
                as="button"
              >
                <i class="bi bi-qr-code-scan me-1"></i>Registrar Visita
              </Link>
              <Link
                v-if="card?.is_completed || card?.reset_count > 0"
                :href="`/member/listings/${listing?.id}/fidelity-cards/${card?.id}/reset`"
                method="post"
                class="btn btn-warning btn-sm"
                as="button"
              >
                <i class="bi bi-arrow-counterclockwise me-1"></i>Reiniciar
              </Link>
              <Link
                :href="`/member/listings/${listing?.id}/fidelity-cards/${card?.id}/edit`"
                class="btn btn-outline-primary btn-sm"
              >
                <i class="bi bi-pencil me-1"></i>Editar
              </Link>
              <button
                class="btn btn-outline-danger btn-sm"
                @click="deleteCard"
              >
                <i class="bi bi-trash me-1"></i>Eliminar
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <h6 class="mb-3">Progreso</h6>
            <div class="mb-2">
              <div class="d-flex justify-content-between mb-1">
                <span>{{ card?.current_visits }} / {{ card?.max_visits }} visitas</span>
                <span>{{ card?.progress_percentage }}%</span>
              </div>
              <div class="progress" style="height: 12px;">
                <div
                  class="progress-bar"
                  :class="card?.is_completed ? 'bg-success' : 'bg-primary'"
                  :style="{ width: card?.progress_percentage + '%' }"
                ></div>
              </div>
            </div>
            <p class="text-muted small mb-0">
              {{ card?.visits_remaining }} visitas restantes
            </p>
          </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <h6 class="mb-3">Información</h6>
            <table class="table table-sm">
              <tbody>
                <tr>
                  <td class="text-muted">Descripción</td>
                  <td>{{ card?.description || '-' }}</td>
                </tr>
                <tr>
                  <td class="text-muted">Veces reiniciada</td>
                  <td>{{ card?.reset_count || 0 }}</td>
                </tr>
                <tr>
                  <td class="text-muted">Fecha de creación</td>
                  <td>{{ formatDate(card?.created_at) }}</td>
                </tr>
                <tr v-if="card?.completed_at">
                  <td class="text-muted">Fecha de completación</td>
                  <td>{{ formatDate(card.completed_at) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h6 class="mb-3">Código QR</h6>
            <div class="text-center">
              <div class="bg-light p-3 d-inline-block rounded">
                <QRCode :value="qrUrl" :size="200" />
              </div>
              <p class="text-muted small mt-2 mb-0">
                Este código QR lleva al cliente a la página pública de su tarjeta
              </p>
            </div>
          </div>
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
import QRCode from '@/Components/QRCode/QRCode.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const card = computed(() => page.props.card)
const businessMenu = computed(() => page.props.businessMenu || [])

const qrUrl = computed(() => {
  if (!card.value) return ''
  return `${window.location.origin}/b/${listing.value?.slug}/fidelity/${card.value.public_code}`
})

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Fidelización', href: `/member/listings/${listing.value?.id}/fidelity-cards` },
  { label: card.value?.client_name },
])

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const deleteCard = () => {
  if (!confirm(`¿Estás seguro de eliminar la tarjeta de "${card.value?.client_name}"?`)) {
    return
  }
  router.delete(`/member/listings/${listing.value?.id}/fidelity-cards/${card.value?.id}`, {
    preserveScroll: true,
  })
}
</script>
