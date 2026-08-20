<template>
  <AdminLayout>
    <Head title="Galerías" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-0">Galerías</h1>
        <p class="text-muted mb-0">{{ listing.name }}</p>
      </div>
      <Link :href="`/admin/listings/${listing.id}/galleries/create`" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>
        Nueva galería
      </Link>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="!galleries.length" class="text-center py-5">
          <i class="bi bi-images display-1 text-muted"></i>
          <h3 class="h5 mt-3">No hay galerías</h3>
          <p class="text-muted">Crea la galería principal para empezar.</p>
        </div>

        <div v-else class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>Nombre</th>
                <th>Imagenes</th>
                <th>Estado</th>
                <th>Orden</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="gallery in galleries" :key="gallery.id">
                <td>
                  <div class="fw-semibold">{{ gallery.name }}</div>
                  <span v-if="gallery.is_primary" class="badge bg-primary me-1">Principal</span>
                  <span :class="gallery.is_active ? 'badge bg-success' : 'badge bg-secondary'">
                    {{ gallery.is_active ? 'Activa' : 'Inactiva' }}
                  </span>
                </td>
                <td>{{ gallery.images_count }}</td>
                <td>{{ gallery.sort_order }}</td>
                <td class="text-end">
                  <button
                    v-if="!gallery.is_primary"
                    type="button"
                    class="btn btn-sm btn-outline-success me-2"
                    @click="setPrimary(gallery)"
                  >
                    <i class="bi bi-star me-1"></i>Hacer principal
                  </button>
                  <Link :href="`/admin/listings/${listing.id}/galleries/${gallery.id}/edit`" class="btn btn-sm btn-outline-primary me-2">
                    Editar
                  </Link>
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-danger"
                    @click="confirmDestroy(gallery)"
                  >
                    Eliminar
                  </button>
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
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const galleries = computed(() => page.props.galleries || [])

const setPrimary = (gallery) => {
  if (confirm(`Marcar "${gallery.name}" como galería principal?`)) {
    router.post(`/admin/listings/${listing.value.id}/galleries/${gallery.id}/set-primary`)
  }
}

const confirmDestroy = (gallery) => {
  if (confirm(`Eliminar "${gallery.name}" y todas sus imagenes?`)) {
    router.delete(`/admin/listings/${listing.value.id}/galleries/${gallery.id}`)
  }
}
</script>