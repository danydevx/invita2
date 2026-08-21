<template>
  <AdminLayout>
    <Head :title="`Productos - ${listing.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link href="/admin/listings" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Negocios
        </Link>
        <h1 class="h4 mb-1 mt-1">{{ listing.name }} - Productos</h1>
      </div>
      <div class="d-flex gap-2">
        <Link :href="`/admin/listings/${listing.id}/product-categories`" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-folder me-1"></i>
          Categorias
        </Link>
        <Link :href="`/admin/listings/${listing.id}/products/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nuevo Producto
        </Link>
      </div>
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
                <th scope="col">SKU</th>
                <th scope="col">Precio</th>
                <th scope="col">Stock</th>
                <th scope="col">Activo</th>
                <th scope="col" class="text-end">Accion</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="products.data.length === 0">
                <td colspan="6" class="text-center text-muted py-4">
                  No hay productos registrados.
                </td>
              </tr>
              <tr v-for="prod in products.data" :key="prod.id">
                <td class="fw-semibold">{{ prod.name }}</td>
                <td>{{ prod.sku || '-' }}</td>
                <td>{{ formatPrice(prod.price) }}</td>
                <td>
                  <span v-if="prod.quantity !== null" :class="prod.quantity > 0 ? 'text-success' : 'text-danger'">
                    {{ prod.quantity }}
                  </span>
                  <span v-else>-</span>
                </td>
                <td>
                  <span v-if="prod.is_active" class="badge bg-success">Activo</span>
                  <span v-else class="badge bg-secondary">Inactivo</span>
                </td>
                <td class="text-end">
                  <Link :href="`/admin/listings/${listing.id}/products/${prod.id}/edit`" class="btn btn-sm btn-outline-primary">
                    Editar
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="products.links" class="d-flex justify-content-center mt-4">
          <Pagination :links="products.links" />
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
const listing = computed(() => page.props.listing)
const products = computed(() => page.props.products || { data: [], links: [] })

const formatPrice = (price) => {
  if (!price && price !== 0) return '-'
  return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(price)
}
</script>
