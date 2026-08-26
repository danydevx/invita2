<template>
  <MemberLayout>
    <Head title="Galerías" />

    <PageHeader
      title="Galerías"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/listings'"
    >
      <template #actions>
        <Link :href="`/member/listings/${listing?.id}/galleries/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nueva galería
        </Link>
      </template>
    </PageHeader>

    <div v-if="!galleries.length" class="card border-0 shadow-sm">
      <div class="card-body text-center py-5">
        <i class="bi bi-images display-1 text-muted"></i>
        <h3 class="h5 mt-3">No hay galerías registradas</h3>
        <p class="text-muted">Crea tu primera galería para empezar a organizar imágenes.</p>
        <Link :href="`/member/listings/${listing?.id}/galleries/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Crear primera galería
        </Link>
      </div>
    </div>

    <div v-else class="row g-3">
      <div v-for="gallery in galleries" :key="gallery.id" class="col-12 col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div v-if="gallery.thumbnails && gallery.thumbnails.length > 0" class="card-img-top d-flex gap-1 p-2" style="background: #f8f9fa;">
            <img
              v-for="thumb in gallery.thumbnails"
              :key="thumb.id"
              :src="thumb.path"
              class="rounded object-fit-cover"
              style="width: 80px; height: 80px;"
              :alt="gallery.name"
            />
            <span v-if="gallery.images_count > 4" class="d-flex align-items-center justify-content-center text-muted small bg-light rounded" style="width: 80px; height: 80px;">
              +{{ gallery.images_count - 4 }}
            </span>
          </div>
          <div class="card-body d-flex flex-column">
            <div class="d-flex align-items-start justify-content-between mb-2">
              <div>
                <h3 class="h5 mb-1">{{ gallery.name }}</h3>
                <span v-if="gallery.is_primary" class="badge bg-primary me-1">Principal</span>
                <span :class="gallery.is_active ? 'badge bg-success' : 'badge bg-secondary'">
                  {{ gallery.is_active ? 'Activa' : 'Inactiva' }}
                </span>
              </div>
              <span class="text-muted small">{{ gallery.images_count }} imagenes</span>
            </div>

            <p v-if="gallery.description" class="text-muted small flex-grow-1">
              {{ gallery.description }}
            </p>

            <div class="d-flex flex-wrap gap-2 mt-3">
              <Link :href="`/member/listings/${listing?.id}/gallery/${gallery.id}`" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-images me-1"></i>Ver imagenes
              </Link>
              <Link :href="`/member/listings/${listing?.id}/galleries/${gallery.id}/edit`" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-pencil me-1"></i>Editar
              </Link>
              <button
                v-if="!gallery.is_primary"
                type="button"
                class="btn btn-sm btn-outline-success"
                @click="setPrimary(gallery)"
              >
                <i class="bi bi-star me-1"></i>Principal
              </button>
              <button
                type="button"
                class="btn btn-sm btn-outline-danger ms-auto"
                @click="confirmDestroy(gallery)"
              >
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const galleries = computed(() => page.props.galleries || [])
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Galería' },
])

const setPrimary = (gallery) => {
  if (confirm(`Marcar "${gallery.name}" como galería principal?`)) {
    router.post(`/member/listings/${listing.value.id}/galleries/${gallery.id}/set-primary`)
  }
}

const confirmDestroy = (gallery) => {
  if (confirm(`Eliminar "${gallery.name}"? Sus imágenes también se eliminarán.`)) {
    router.delete(`/member/listings/${listing.value.id}/galleries/${gallery.id}`)
  }
}
</script>