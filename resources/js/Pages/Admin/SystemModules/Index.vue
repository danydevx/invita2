<template>
  <AdminLayout>
    <Head title="Modulos" />

    <PageHeader :title="'Modulos'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">Modulo</th>
              <th scope="col">Descripcion</th>
              <th scope="col">Estado</th>
              <th scope="col" class="text-end">Accion</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="modules.length === 0">
              <td colspan="4" class="text-center text-muted py-4">No hay modulos registrados.</td>
            </tr>
            <tr v-for="module in modules" :key="module.id">
              <td class="fw-semibold">{{ module.name }}</td>
              <td class="text-muted">{{ module.description || '-' }}</td>
              <td>
                <span v-if="module.is_active" class="badge text-bg-success">Activo</span>
                <span v-else class="badge text-bg-secondary">Inactivo</span>
              </td>
              <td class="text-end">
                <button
                  class="btn btn-sm"
                  :class="module.is_active ? 'btn-outline-danger' : 'btn-outline-primary'"
                  type="button"
                  :disabled="module.is_critical"
                  @click="toggle(module)"
                >
                  {{ module.is_active ? 'Desactivar' : 'Activar' }}
                </button>
                <div v-if="module.is_critical" class="text-muted small mt-1">Critico</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  modules: {
    type: Array,
    default: () => [],
  },
})

const breadcrumbs = [
  { label: 'Modulos', active: true },
]

// Actualiza el estado del modulo con una accion simple.
const toggle = (module) => {
  router.put(`/admin/modules/${module.id}`, {
    is_active: !module.is_active,
  })
}
</script>
