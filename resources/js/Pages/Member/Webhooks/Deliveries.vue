<template>
  <MemberLayout>
    <Head title="Entregas" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Entregas</h1>
        <p class="text-muted mb-0">{{ webhook.name }} · {{ webhook.url }}</p>
      </div>
      <Link href="/member/webhooks" class="btn btn-outline-secondary btn-sm">Volver</Link>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">Evento</th>
              <th scope="col">Estado</th>
              <th scope="col">Fecha</th>
              <th scope="col">Intentos</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="deliveries.data.length === 0">
              <td colspan="5" class="text-center text-muted py-4">No hay entregas registradas.</td>
            </tr>
            <tr v-for="delivery in deliveries.data" :key="delivery.id">
              <td class="fw-semibold">{{ delivery.event }}</td>
              <td>
                <span v-if="delivery.delivered_at" class="badge text-bg-success">Entregado</span>
                <span v-else class="badge text-bg-warning">Fallido</span>
              </td>
              <td class="text-muted">{{ delivery.delivered_at || delivery.failed_at || delivery.created_at }}</td>
              <td class="text-muted">{{ delivery.attempt_count }}</td>
              <td class="text-end">
                <button
                  v-if="!delivery.delivered_at"
                  class="btn btn-sm btn-outline-primary"
                  type="button"
                  @click="retry(delivery)"
                >
                  Reintentar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">Mostrando {{ deliveries.data.length }} de {{ deliveries.total }} registros</div>
        <Pagination :links="deliveries.links" />
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import Pagination from '@/Components/Member/Pagination.vue'

const props = defineProps({
  webhook: {
    type: Object,
    required: true,
  },
  deliveries: {
    type: Object,
    required: true,
  },
})

const retry = (delivery) => {
  router.post(`/member/webhooks/deliveries/${delivery.id}/retry`, {}, { preserveScroll: true })
}
</script>
