<template>
  <AdminLayout>
    <Head :title="`Leads - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link href="/admin/businesses" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Negocios
        </Link>
        <h1 class="h4 mb-1 mt-1">{{ business.name }} - Leads</h1>
      </div>
      <div>
        <Link :href="`/admin/businesses/${business.id}/leads/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nuevo Lead
        </Link>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div v-if="leads.data.length === 0" class="text-center text-muted py-5">
          No hay leads registrados.
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Email</th>
                <th scope="col">Telefono</th>
                <th scope="col">Estado</th>
                <th scope="col">Fuente</th>
                <th scope="col">Fecha</th>
                <th scope="col" class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="lead in leads.data" :key="lead.id">
                <td class="fw-semibold">{{ lead.name }}</td>
                <td>{{ lead.email }}</td>
                <td>{{ lead.phone || '-' }}</td>
                <td>
                  <span class="badge" :class="statusClass(lead.status)">{{ lead.status_label }}</span>
                </td>
                <td>{{ lead.source_label }}</td>
                <td>{{ formatDate(lead.created_at) }}</td>
                <td class="text-end">
                  <div class="d-flex gap-1 justify-content-end">
                    <Link :href="`/admin/businesses/${business.id}/leads/${lead.id}`" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-eye"></i>
                    </Link>
                    <Link :href="`/admin/businesses/${business.id}/leads/${lead.id}/edit`" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i>
                    </Link>
                    <button
                      class="btn btn-sm btn-outline-danger"
                      @click="deleteLead(lead)"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="leads.links" class="d-flex justify-content-center mt-4">
          <Pagination :links="leads.links" />
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
const leads = computed(() => page.props.leads || { data: [], links: [] })

const statusClass = (status) => {
  const classes = {
    new: 'bg-info',
    contacted: 'bg-primary',
    qualified: 'bg-success',
    converted: 'bg-dark',
    lost: 'bg-secondary',
  }
  return classes[status] || 'bg-secondary'
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('es-AR')
}

const deleteLead = (lead) => {
  if (confirm('¿Estás seguro de eliminar este lead?')) {
    router.delete(`/admin/businesses/${business.value.id}/leads/${lead.id}`, {
      preserveScroll: true,
    })
  }
}
</script>
