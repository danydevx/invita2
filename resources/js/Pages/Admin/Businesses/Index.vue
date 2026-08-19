<template>
  <AdminLayout>
    <Head title="Negocios" />

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h4 mb-0">Negocios</h1>
      <Link href="/admin/businesses/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>
        Nuevo Negocio
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
            placeholder="Buscar negocios..."
            v-model="searchQuery"
            @keyup.enter="doSearch"
          />
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Propietario</th>
                <th scope="col">Tipo</th>
                <th scope="col">Estado</th>
                <th scope="col">Acciones de Contenido</th>
                <th scope="col" class="text-end">Accion</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="businesses.data.length === 0">
                <td colspan="6" class="text-center text-muted py-4">
                  No hay negocios registrados.
                </td>
              </tr>
              <tr v-for="biz in businesses.data" :key="biz.id">
                <td class="fw-semibold">
                  {{ biz.name }}
                  <br><small class="text-muted">{{ biz.slug }}</small>
                </td>
                <td>
                  <span v-if="biz.user">{{ biz.user.name }}</span>
                  <br><small class="text-muted" v-if="biz.user">{{ biz.user.email }}</small>
                  <span v-else class="text-muted">-</span>
                </td>
                <td>{{ biz.business_type }}</td>
                <td>
                  <span v-if="biz.is_active" class="badge bg-success">Activo</span>
                  <span v-else class="badge bg-secondary">Inactivo</span>
                </td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <Link :href="`/admin/businesses/${biz.id}/hero`" class="btn btn-outline-secondary" title="Hero">
                      <i class="bi bi-house"></i>
                    </Link>
                    <Link :href="`/admin/businesses/${biz.id}/locations`" class="btn btn-outline-secondary" title="Ubicaciones">
                      <i class="bi bi-geo-alt"></i>
                    </Link>
                    <Link :href="`/admin/businesses/${biz.id}/services`" class="btn btn-outline-secondary" title="Servicios">
                      <i class="bi bi-scissors"></i>
                    </Link>
                    <Link :href="`/admin/businesses/${biz.id}/products`" class="btn btn-outline-secondary" title="Productos">
                      <i class="bi bi-bag"></i>
                    </Link>
                    <Link :href="`/admin/businesses/${biz.id}/gallery`" class="btn btn-outline-secondary" title="Galeria">
                      <i class="bi bi-images"></i>
                    </Link>
                    <Link :href="`/admin/businesses/${biz.id}/appointments`" class="btn btn-outline-secondary" title="Citas">
                      <i class="bi bi-calendar-check"></i>
                    </Link>
                    <Link :href="`/admin/businesses/${biz.id}/slots`" class="btn btn-outline-secondary" title="Turnos">
                      <i class="bi bi-clock"></i>
                    </Link>
                    <Link :href="`/admin/businesses/${biz.id}/leads`" class="btn btn-outline-secondary" title="Leads">
                      <i class="bi bi-people"></i>
                    </Link>
                    <Link :href="`/admin/businesses/${biz.id}/contact-form/submissions`" class="btn btn-outline-secondary" title="Contactos">
                      <i class="bi bi-envelope"></i>
                    </Link>
                    <Link :href="`/admin/businesses/${biz.id}/ai-chatbot`" class="btn btn-outline-secondary" title="AI Chatbot">
                      <i class="bi bi-robot"></i>
                    </Link>
                    <Link :href="`/admin/businesses/${biz.id}/reviews`" class="btn btn-outline-secondary" title="Reviews">
                      <i class="bi bi-star"></i>
                    </Link>
                    <Link :href="`/admin/businesses/${biz.id}/social-networks`" class="btn btn-outline-secondary" title="Redes Sociales">
                      <i class="bi bi-share"></i>
                    </Link>
                    <Link :href="`/admin/businesses/${biz.id}/promotions`" class="btn btn-outline-secondary" title="Promotions">
                      <i class="bi bi-tag"></i>
                    </Link>
                    <Link :href="`/admin/businesses/${biz.id}/menu-categories`" class="btn btn-outline-secondary" title="Menú">
                      <i class="bi bi-list-ul"></i>
                    </Link>
                  </div>
                </td>
                <td class="text-end">
                  <a v-if="biz.is_published" :href="`/b/${biz.slug}`" target="_blank" class="btn btn-sm btn-outline-info me-1">
                    Ver minisitio
                  </a>
                  <Link :href="`/admin/businesses/${biz.id}/edit`" class="btn btn-sm btn-outline-primary me-1">
                    Editar
                  </Link>
                  <Link :href="`/admin/businesses/${biz.id}/modules`" class="btn btn-sm btn-outline-secondary">
                    Modulos
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="businesses.links" class="d-flex justify-content-center mt-4">
          <Pagination :links="businesses.links" />
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
const businesses = computed(() => page.props.businesses || { data: [], links: [] })
const searchQuery = ref(page.props.filters?.search || '')

const doSearch = () => {
  router.get('/admin/businesses', { search: searchQuery.value }, { preserveState: true })
}
</script>
