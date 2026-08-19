<template>
  <AdminLayout>
    <Head :title="`Servicios - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link href="/admin/businesses" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Negocios
        </Link>
        <h1 class="h4 mb-1 mt-1">{{ business.name }} - Servicios</h1>
      </div>
      <Link :href="`/admin/businesses/${business.id}/services/create`" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>
        Nuevo Servicio
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
                <th scope="col">Imagen</th>
                <th scope="col">Nombre</th>
                <th scope="col">Ubicacion</th>
                <th scope="col">Duracion</th>
                <th scope="col">Precio</th>
                <th scope="col">Reserva online</th>
                <th scope="col">Activo</th>
                <th scope="col" class="text-end">Accion</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="services.data.length === 0">
                <td colspan="8" class="text-center text-muted py-4">
                  No hay servicios registrados.
                </td>
              </tr>
              <tr v-for="svc in services.data" :key="svc.id">
                <td>
                  <img v-if="svc.image" :src="svc.image" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                  <span v-else class="text-muted">Sin imagen</span>
                </td>
                <td class="fw-semibold">{{ svc.name }}</td>
                <td>{{ svc.location?.name || '-' }}</td>
                <td>{{ svc.duration_minutes }} min</td>
                <td>{{ formatPrice(svc.price) }}</td>
                <td>
                  <span v-if="svc.allows_online_booking" class="badge bg-success">Si</span>
                  <span v-else class="badge bg-secondary">No</span>
                </td>
                <td>
                  <span v-if="svc.is_active" class="badge bg-success">Activo</span>
                  <span v-else class="badge bg-secondary">Inactivo</span>
                </td>
                <td class="text-end">
                  <Link :href="`/admin/businesses/${business.id}/services/${svc.id}/edit`" class="btn btn-sm btn-outline-primary">
                    Editar
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="services.links" class="d-flex justify-content-center mt-4">
          <Pagination :links="services.links" />
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
const services = computed(() => page.props.services || { data: [], links: [] })

const formatPrice = (price) => {
  if (!price && price !== 0) return '-'
  return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(price)
}
</script>
