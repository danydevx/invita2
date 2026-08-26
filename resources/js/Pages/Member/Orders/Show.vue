<template>
  <MemberLayout>
    <Head :title="`Pedido ${order?.order_number} - ${listing?.name || ''}`" />

    <PageHeader
      title="Detalle del Pedido"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/orders`"
    />

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div v-if="order" class="row">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
              <h5 class="mb-0">{{ order.order_number }}</h5>
              <small class="text-muted">{{ formatDate(order.created_at) }}</small>
            </div>
            <div>
              <select
                class="form-select form-select-sm"
                :class="getStatusClass(order.status)"
                v-model="currentStatus"
                @change="updateStatus"
                :disabled="updating"
              >
                <option v-for="status in statuses" :key="status.value" :value="status.value">
                  {{ status.label }}
                </option>
              </select>
            </div>
          </div>

          <div class="card-body">
            <h6 class="border-bottom pb-2 mb-3">Items del pedido</h6>

            <table class="table">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th class="text-center">Cantidad</th>
                  <th class="text-end">Precio</th>
                  <th class="text-end">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in order.items" :key="item.id">
                  <td>
                    <div>{{ item.title }}</div>
                    <small v-if="item.options && item.options.extras" class="text-muted">
                      {{ item.options.extras.map(e => e.name).join(', ') }}
                    </small>
                  </td>
                  <td class="text-center">{{ item.quantity }}</td>
                  <td class="text-end" v-format-price="item.unit_price"></td>
                  <td class="text-end fw-bold" v-format-price="item.subtotal"></td>
                </tr>
              </tbody>
              <tfoot class="table-light">
                <tr>
                  <td colspan="3" class="text-end">Subtotal:</td>
                  <td class="text-end" v-format-price="order.subtotal"></td>
                </tr>
                <tr v-if="order.order_type === 'delivery'">
                  <td colspan="3" class="text-end">Delivery:</td>
                  <td class="text-end" v-format-price="order.delivery_fee"></td>
                </tr>
                <tr class="fw-bold fs-5">
                  <td colspan="3" class="text-end">Total:</td>
                  <td class="text-end" v-format-price="order.total"></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white">
            <h6 class="mb-0">Datos del cliente</h6>
          </div>
          <div class="card-body">
            <p class="mb-1">
              <i class="bi bi-person me-2"></i>
              <strong>{{ order.customer_name }}</strong>
            </p>
            <p class="mb-1">
              <i class="bi bi-telephone me-2"></i>
              {{ order.customer_phone }}
            </p>
            <p v-if="order.customer_email" class="mb-1">
              <i class="bi bi-envelope me-2"></i>
              {{ order.customer_email }}
            </p>
          </div>
        </div>

        <div v-if="order.order_type === 'delivery' && order.delivery_address" class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white">
            <h6 class="mb-0">Dirección de entrega</h6>
          </div>
          <div class="card-body">
            <p class="mb-1">{{ order.delivery_address.full_address }}</p>
            <p v-if="order.delivery_address.references" class="mb-1 text-muted">
              <small>{{ order.delivery_address.references }}</small>
            </p>
            <p v-if="order.distance_km" class="mb-0 text-muted">
              <i class="bi bi-geo-alt me-1"></i>
              {{ parseFloat(order.distance_km).toFixed(1) }} km
            </p>
          </div>
        </div>

        <div v-if="order.order_type === 'pickup' && order.pickup_location" class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white">
            <h6 class="mb-0">Punto de recogida</h6>
          </div>
          <div class="card-body">
            <p class="mb-1 fw-bold">{{ order.pickup_location.location_name }}</p>
            <p class="mb-1 text-muted">{{ order.pickup_location.location_address }}</p>
            <p v-if="order.pickup_location.pickup_time" class="mb-0">
              <i class="bi bi-clock me-1"></i>
              {{ order.pickup_location.pickup_time }}
            </p>
          </div>
        </div>

        <div v-if="order.notes" class="card border-0 shadow-sm">
          <div class="card-header bg-white">
            <h6 class="mb-0">Notas</h6>
          </div>
          <div class="card-body">
            <p class="mb-0">{{ order.notes }}</p>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const order = computed(() => page.props.order)
const statuses = computed(() => page.props.statuses || [])
const businessMenu = computed(() => page.props.businessMenu || [])

const currentStatus = ref(order.value?.status)
const updating = ref(false)

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Pedidos', href: `/member/listings/${listing.value?.id}/orders` },
  { label: order.value?.order_number },
])

const formatDate = (dateStr) => {
  const date = new Date(dateStr)
  return date.toLocaleDateString('es-MX', {
    weekday: 'long',
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-warning text-dark',
    confirmed: 'bg-info',
    preparing: 'bg-primary',
    ready: 'bg-success',
    completed: 'bg-secondary',
    cancelled: 'bg-danger',
  }
  return classes[status] || 'bg-secondary'
}

const updateStatus = () => {
  if (!confirm('¿Actualizar el estado del pedido?')) {
    currentStatus.value = order.value.status
    return
  }

  updating.value = true

  router.post(`/member/listings/${listing.value.id}/orders/${order.value.id}/status`, {
    status: currentStatus.value,
  }, {
    preserveScroll: true,
    onFinish: () => {
      updating.value = false
    },
  })
}
</script>
