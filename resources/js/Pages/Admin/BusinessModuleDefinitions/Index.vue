<template>
  <AdminLayout>
    <Head title="Modulos de Negocio" />

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h4 mb-0">Modulos de Negocio</h1>
      <Link href="/admin/business-module-definitions/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>
        Nuevo Modulo
      </Link>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div class="mb-3">
          <input
            type="search"
            class="form-control"
            placeholder="Buscar modulos..."
            v-model="searchQuery"
            @keyup.enter="doSearch"
          />
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Orden</th>
                <th scope="col">Icono</th>
                <th scope="col">Nombre</th>
                <th scope="col">Key</th>
                <th scope="col">Settings</th>
                <th scope="col">Activo</th>
                <th scope="col" class="text-end">Accion</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="definitions.data.length === 0">
                <td colspan="7" class="text-center text-muted py-4">
                  No hay modulos registrados.
                </td>
              </tr>
              <tr v-for="def in definitions.data" :key="def.id">
                <td>{{ def.sort_order }}</td>
                <td><i :class="def.icon || 'bi bi-box'"></i></td>
                <td class="fw-semibold">{{ def.name }}</td>
                <td><code>{{ def.key }}</code></td>
                <td>
                  <span v-if="def.has_settings" class="badge bg-warning">Si</span>
                  <span v-else class="badge bg-secondary">No</span>
                </td>
                <td>
                  <span v-if="def.is_active" class="badge bg-success">Activo</span>
                  <span v-else class="badge bg-secondary">Inactivo</span>
                </td>
                <td class="text-end">
                  <Link
                    v-if="def.has_settings && def.settings_url"
                    :href="def.settings_url"
                    class="btn btn-sm btn-outline-warning me-2"
                  >
                    <i class="bi bi-gear me-1"></i>Configurar
                  </Link>
                  <Link :href="`/admin/business-module-definitions/${def.id}/edit`" class="btn btn-sm btn-outline-primary">
                    Editar
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="definitions.links" class="d-flex justify-content-center mt-4">
          <Pagination :links="definitions.links" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const page = usePage()
const definitions = computed(() => page.props.definitions || { data: [], links: [] })
const searchQuery = ref(page.props.filters?.search || '')

const doSearch = () => {
  router.get('/admin/business-module-definitions', { search: searchQuery.value }, { preserveState: true })
}
</script>
