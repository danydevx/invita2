<template>
  <AdminLayout>
    <Head title="Entregas" />

    <PageHeader :title="'Entregas'" :breadcrumbs="breadcrumbs" backHref="/admin/webhooks" />

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold">{{ webhook.name }}</div>
        <div class="text-muted">{{ webhook.url }}</div>
      </div>
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
            </tr>
          </thead>
          <tbody>
            <tr v-if="deliveries.data.length === 0">
              <td colspan="4" class="text-center text-muted py-4">No hay entregas registradas.</td>
            </tr>
            <tr v-for="delivery in deliveries.data" :key="delivery.id">
              <td class="fw-semibold">{{ delivery.event }}</td>
              <td>
                <span v-if="delivery.delivered_at" class="badge text-bg-success">Entregado</span>
                <span v-else class="badge text-bg-warning">Fallido</span>
              </td>
              <td class="text-muted">{{ delivery.delivered_at || delivery.failed_at || delivery.created_at }}</td>
              <td class="text-muted">{{ delivery.attempt_count }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">Mostrando {{ deliveries.data.length }} de {{ deliveries.total }} registros</div>
        <Pagination :links="deliveries.links" />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

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

const breadcrumbs = [
  { label: 'Webhooks', href: '/admin/webhooks' },
  { label: `#${props.webhook.id}`, active: true },
  { label: 'Entregas', active: true },
]
</script>
