<template>
  <AdminLayout>
    <Head title="Modulos de Negocio" />

    <div class="container-fluid py-4">
      <h1 class="h4 mb-4">Modulos de Negocio</h1>

      <div class="card border-0 shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Negocio</th>
                <th scope="col">Propietario</th>
                <th scope="col">Modulos activos</th>
                <th scope="col" class="text-end">Accion</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="businesses.length === 0">
                <td colspan="4" class="text-center text-muted py-4">No hay negocios registrados.</td>
              </tr>
              <tr v-for="business in businesses" :key="business.id">
                <td class="fw-semibold">{{ business.name }}</td>
                <td class="text-muted">
                  {{ business.user?.name || '-' }}
                  <br />
                  <small>{{ business.user?.email || '' }}</small>
                </td>
                <td>
                  <span class="badge text-bg-success">{{ getEnabledCount(business.modules) }} activos</span>
                  <span class="text-muted small ms-1">/ {{ business.modules?.length || 0 }} total</span>
                </td>
                <td class="text-end">
                  <Link :href="`/admin/businesses/${business.id}/modules`" class="btn btn-sm btn-outline-primary">
                    Gestionar modulos
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const businesses = computed(() => page.props.businesses?.data || [])

const getEnabledCount = (modules) => {
  if (!modules) return 0
  return modules.filter((m) => m.is_enabled).length
}
</script>
