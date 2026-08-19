<template>
  <AdminLayout>
    <Head :title="`Ubicaciones - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link href="/admin/businesses" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Negocios
        </Link>
        <h1 class="h4 mb-1 mt-1">{{ business.name }} - Ubicaciones</h1>
      </div>
      <Link :href="`/admin/businesses/${business.id}/locations/create`" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>
        Nueva Ubicacion
      </Link>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Direccion</th>
                <th scope="col">Telefono</th>
                <th scope="col">Principal</th>
                <th scope="col">Activa</th>
                <th scope="col" class="text-end">Accion</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="locations.data.length === 0">
                <td colspan="6" class="text-center text-muted py-4">
                  No hay ubicaciones registradas.
                </td>
              </tr>
              <tr v-for="loc in locations.data" :key="loc.id">
                <td class="fw-semibold">{{ loc.name }}</td>
                <td>
                  {{ loc.address_line_1 }}
                  <span v-if="loc.address_line_2">, {{ loc.address_line_2 }}</span>
                  <br>
                  <small class="text-muted">{{ loc.city }}{{ loc.state ? ', ' + loc.state : '' }} {{ loc.postal_code }}</small>
                </td>
                <td>{{ loc.phone || '-' }}</td>
                <td>
                  <span v-if="loc.is_primary" class="badge bg-warning">Principal</span>
                </td>
                <td>
                  <span v-if="loc.is_active" class="badge bg-success">Activa</span>
                  <span v-else class="badge bg-secondary">Inactiva</span>
                </td>
                <td class="text-end">
                  <Link :href="`/admin/businesses/${business.id}/locations/${loc.id}/edit`" class="btn btn-sm btn-outline-primary">
                    Editar
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="locations.links" class="d-flex justify-content-center mt-4">
          <Pagination :links="locations.links" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const page = usePage()
const business = computed(() => page.props.business)
const locations = computed(() => page.props.locations || { data: [], links: [] })
</script>
