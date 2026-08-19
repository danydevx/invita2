<template>
  <AdminLayout>
    <Head title="Jobs fallidos" />

    <PageHeader :title="'Jobs fallidos'" :breadcrumbs="breadcrumbs" backHref="/admin/queues">
      <template #actions>
        <button class="btn btn-outline-primary" type="button" @click="retryAll">Reintentar todos</button>
        <button class="btn btn-outline-danger" type="button" @click="flushAll">Limpiar todos</button>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Job</th>
              <th scope="col">Queue</th>
              <th scope="col">Fecha</th>
              <th scope="col">Error</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="failedJobs.data.length === 0">
              <td colspan="6" class="text-center text-muted py-4">No hay jobs fallidos.</td>
            </tr>
            <tr v-for="job in failedJobs.data" :key="job.id">
              <td class="text-muted">{{ job.id }}</td>
              <td class="fw-semibold">{{ job.job }}</td>
              <td class="text-muted">{{ job.queue }}</td>
              <td class="text-muted">{{ job.failed_at }}</td>
              <td class="text-muted">{{ job.exception }}</td>
              <td class="text-end">
                <div class="d-inline-flex gap-2">
                  <button class="btn btn-sm btn-outline-primary" type="button" @click="retry(job)">
                    Reintentar
                  </button>
                  <button class="btn btn-sm btn-outline-danger" type="button" @click="forget(job)">
                    Eliminar
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">Mostrando {{ failedJobs.data.length }} de {{ failedJobs.total }} registros</div>
        <Pagination :links="failedJobs.links" />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const props = defineProps({
  failedJobs: {
    type: Object,
    required: true,
  },
})

const breadcrumbs = [
  { label: 'Colas', href: '/admin/queues' },
  { label: 'Jobs fallidos', active: true },
]

const retry = (job) => {
  router.post(`/admin/failed-jobs/${job.id}/retry`, {}, { preserveScroll: true })
}

const forget = (job) => {
  if (!confirm('Eliminar este job fallido?')) return
  router.delete(`/admin/failed-jobs/${job.id}`)
}

const retryAll = () => {
  router.post('/admin/failed-jobs/retry-all', {}, { preserveScroll: true })
}

const flushAll = () => {
  if (!confirm('Eliminar todos los jobs fallidos?')) return
  router.delete('/admin/failed-jobs/flush')
}
</script>
