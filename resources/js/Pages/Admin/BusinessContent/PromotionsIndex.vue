<template>
  <AdminLayout>
    <Head :title="`Promociones - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link href="/admin/businesses" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Negocios
        </Link>
        <h1 class="h4 mb-1 mt-1">{{ business.name }} - Promociones</h1>
      </div>
      <Link :href="`/admin/businesses/${business.id}/promotions/create`" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>
        Nueva Promocion
      </Link>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div v-if="promotions.data.length === 0" class="text-center text-muted py-5">
          No hay promociones registradas.
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Imagen</th>
                <th scope="col">Nombre</th>
                <th scope="col">Precio Regular</th>
                <th scope="col">Precio Promo</th>
                <th scope="col">Cupon</th>
                <th scope="col">Expira</th>
                <th scope="col">Activo</th>
                <th scope="col" class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="promo in promotions.data" :key="promo.id">
                <td>
                  <img v-if="promo.image" :src="promo.image" class="rounded" style="width: 50px; height: 50px; object-fit: cover;" />
                  <i v-else class="bi bi-image text-muted fs-4"></i>
                </td>
                <td class="fw-semibold">{{ promo.name }}</td>
                <td>{{ formatPrice(promo.regular_price) }}</td>
                <td class="text-success fw-bold">{{ formatPrice(promo.promotion_price) }}</td>
                <td>
                  <span v-if="promo.coupon_code" class="badge bg-warning text-dark">{{ promo.coupon_code }}</span>
                  <span v-else>-</span>
                </td>
                <td>
                  <span v-if="promo.expires_at" :class="isExpired(promo.expires_at) ? 'text-danger' : 'text-muted'">
                    {{ formatDate(promo.expires_at) }}
                  </span>
                  <span v-else>-</span>
                </td>
                <td>
                  <span v-if="promo.is_active" class="badge bg-success">Activa</span>
                  <span v-else class="badge bg-secondary">Inactiva</span>
                </td>
                <td class="text-end">
                  <Link :href="`/admin/businesses/${business.id}/promotions/${promo.id}/edit`" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil"></i>
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="promotions.links" class="d-flex justify-content-center mt-4">
          <Pagination :links="promotions.links" />
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
const promotions = computed(() => page.props.promotions || { data: [], links: [] })

const formatPrice = (price) => {
  if (!price && price !== 0) return '-'
  return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(price)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('es-AR')
}

const isExpired = (date) => {
  return new Date(date) < new Date()
}
</script>
