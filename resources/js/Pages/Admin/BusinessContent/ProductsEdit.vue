<template>
  <AdminLayout>
    <Head :title="`Editar Producto - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link :href="`/admin/businesses/${business.id}/products`" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
        <h1 class="h4 mb-1 mt-1">Editar Producto</h1>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre *</label>
              <input type="text" v-model="form.name" class="form-control" required />
              <div v-if="errors.name" class="text-danger small">{{ errors.name }}</div>
            </div>

            <div class="col-md-6">
              <label class="form-label">SKU</label>
              <input type="text" v-model="form.sku" class="form-control" />
            </div>

            <div class="col-12">
              <label class="form-label">Descripcion</label>
              <textarea v-model="form.description" class="form-control" rows="3"></textarea>
            </div>

            <div class="col-md-4">
              <label class="form-label">Precio</label>
              <input type="number" v-model="form.price" class="form-control" min="0" step="0.01" />
            </div>

            <div class="col-md-4">
              <label class="form-label">Stock</label>
              <input type="number" v-model="form.quantity" class="form-control" min="0" />
            </div>

            <div class="col-md-4">
              <label class="form-label">Orden</label>
              <input type="number" v-model="form.sort_order" class="form-control" min="0" />
            </div>

            <div class="col-12">
              <div class="form-check">
                <input type="checkbox" v-model="form.is_active" class="form-check-input" id="is_active" />
                <label class="form-check-label" for="is_active">Activo</label>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input type="checkbox" v-model="form.whatsapp_contact" class="form-check-input" id="whatsapp_contact" />
                <label class="form-check-label" for="whatsapp_contact">Contactar por WhatsApp</label>
              </div>
            </div>

            <div class="col-12">
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Guardando...' : 'Guardar' }}
              </button>
              <Link :href="`/admin/businesses/${business.id}/products`" class="btn btn-outline-secondary ms-2">
                Cancelar
              </Link>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, reactive } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const business = computed(() => page.props.business)
const product = computed(() => page.props.product)
const errors = computed(() => page.props.errors || {})
const sending = computed(() => false)

const form = reactive({
  name: product.value.name,
  description: product.value.description || '',
  price: product.value.price || '',
  sku: product.value.sku || '',
  quantity: product.value.quantity ?? null,
  is_active: product.value.is_active || false,
  sort_order: product.value.sort_order || 0,
})

const submit = () => {
  router.put(`/admin/businesses/${business.value.id}/products/${product.value.id}`, form)
}
</script>
