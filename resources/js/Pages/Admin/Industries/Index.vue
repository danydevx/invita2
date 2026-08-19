<template>
  <AdminLayout>
    <Head title="Industrias" />

    <PageHeader title="Industrias" :breadcrumbs="breadcrumbs">
      <template #actions>
        <Link href="/admin/industries/create" class="btn btn-primary">
          <i class="bi bi-plus me-1"></i>Nueva Industria
        </Link>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-text bg-light">
                <i class="bi bi-search"></i>
              </span>
              <input
                type="text"
                class="form-control"
                placeholder="Buscar industrias..."
                v-model="search"
                @keyup.enter="handleSearch"
              />
            </div>
          </div>
        </div>

        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show" role="alert">
          {{ $page.props.flash.error }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Slug</th>
                <th>Modulos</th>
                <th>Estado</th>
                <th width="150">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="industry in industries.data" :key="industry.id">
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <i v-if="industry.icon" :class="industry.icon"></i>
                    <strong>{{ industry.name }}</strong>
                  </div>
                </td>
                <td>
                  <code>{{ industry.slug }}</code>
                </td>
                <td>
                  <span class="badge bg-secondary">{{ industry.module_count }} modulos</span>
                </td>
                <td>
                  <span :class="industry.is_active ? 'badge bg-success' : 'badge bg-secondary'">
                    {{ industry.is_active ? 'Activa' : 'Inactiva' }}
                  </span>
                </td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <Link
                      :href="`/admin/industries/${industry.id}/edit`"
                      class="btn btn-outline-secondary"
                    >
                      <i class="bi bi-pencil"></i>
                    </Link>
                    <button
                      type="button"
                      class="btn btn-outline-danger"
                      @click="confirmDelete(industry)"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="industries.data.length === 0">
                <td colspan="5" class="text-center py-4">
                  <i class="bi bi-building display-1 text-muted"></i>
                  <p class="text-muted mt-2">No hay industrias creadas</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="industries.total > industries.per_page" class="d-flex justify-content-center mt-4">
          <Pagination :links="industries.links" />
        </div>
      </div>
    </div>

    <div v-if="deleteModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Eliminar Industria</h5>
            <button type="button" class="btn-close" @click="deleteModal = false"></button>
          </div>
          <div class="modal-body">
            <p>Estas seguro de eliminar la industria <strong>{{ deleteModal.name }}</strong>?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="deleteModal = false">Cancelar</button>
            <button type="button" class="btn btn-danger" @click="deleteIndustry">
              <i class="bi bi-trash me-1"></i>Eliminar
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const page = usePage()
const industries = page.props.industries
const search = ref(page.props.filters.search || '')

const deleteModal = ref(null)

const breadcrumbs = [
  { label: 'Dashboard', href: '/admin' },
  { label: 'Configuracion', href: '/admin/settings' },
  { label: 'Industrias', active: true },
]

const handleSearch = () => {
  router.get('/admin/industries', { search: search.value }, { preserveState: true })
}

const confirmDelete = (industry) => {
  deleteModal.value = industry
}

const deleteIndustry = () => {
  if (deleteModal.value) {
    router.delete(`/admin/industries/${deleteModal.value.id}`, {
      onFinish: () => {
        deleteModal.value = null
      },
    })
  }
}
</script>
