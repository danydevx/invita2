<template>
  <AdminLayout>
    <Head title="Colas" />

    <PageHeader :title="'Colas'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <div class="row g-3">
      <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="text-muted small">Jobs pendientes</div>
            <div class="h3 mb-0">{{ stats.pending }}</div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="text-muted small">Jobs fallidos</div>
            <div class="h3 mb-0">{{ stats.failed }}</div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="text-muted small">Ultimo fallo</div>
            <div class="h6 mb-0">{{ stats.last_failed_at || '-' }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-4">
      <Link href="/admin/failed-jobs" class="btn btn-outline-primary">Ver jobs fallidos</Link>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  stats: {
    type: Object,
    required: true,
  },
})

const breadcrumbs = [
  { label: 'Colas', active: true },
]
</script>
