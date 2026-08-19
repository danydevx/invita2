<template>
  <AdminLayout>
    <Head title="Crear plan" />

    <PageHeader :title="'Crear plan'" :breadcrumbs="breadcrumbs" backHref="/admin/plans" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <FieldText
              id="plan-name"
              label="Nombre"
              placeholder="Plan basico"
              v-model="form.name"
              :formError="form.errors.name"
              required
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldText
              id="plan-slug"
              label="Slug"
              placeholder="plan-basico"
              v-model="form.slug"
              :formError="form.errors.slug"
              required
            />
          </div>

          <div class="col-12">
            <FieldText
              id="plan-description"
              label="Descripcion"
              placeholder="Describe el plan"
              v-model="form.description"
              :formError="form.errors.description"
            />
          </div>

          <div class="col-12 col-md-4">
            <FieldNumber
              id="plan-price"
              label="Precio"
              placeholder="0"
              v-model="form.price"
              :formError="form.errors.price"
              :min="0"
            />
          </div>

          <div class="col-12 col-md-4">
            <FieldText
              id="plan-billing"
              label="Periodo"
              placeholder="mensual"
              v-model="form.billing_period"
              :formError="form.errors.billing_period"
            />
          </div>

          <div class="col-12 col-md-4">
            <FieldNumber
              id="plan-sort"
              label="Orden"
              placeholder="0"
              v-model="form.sort_order"
              :formError="form.errors.sort_order"
              :min="0"
            />
          </div>

          <div class="col-12">
            <FieldSwitch
              id="plan-active"
              label="Plan activo"
              v-model="form.is_active"
            />
          </div>

          <div class="col-12">
            <div class="card border rounded-3">
              <div class="card-body">
                <h3 class="h6 mb-3">Stripe</h3>
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <FieldText
                      id="plan-stripe-product"
                      label="Stripe product id"
                      placeholder="prod_123"
                      v-model="form.stripe_product_id"
                      :formError="form.errors.stripe_product_id"
                    />
                  </div>
                  <div class="col-12 col-md-6">
                    <FieldText
                      id="plan-stripe-price"
                      label="Stripe price id"
                      placeholder="price_123"
                      v-model="form.stripe_price_id"
                      :formError="form.errors.stripe_price_id"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card border rounded-3">
              <div class="card-body">
                <h3 class="h6 mb-3">Limites</h3>
                <div class="row g-3">
                  <div class="col-12 col-md-4">
                    <FieldNumber
                      id="limit-max-items"
                      label="Max items"
                      placeholder="0"
                      v-model="form.limits.max_items"
                      :formError="form.errors['limits.max_items']"
                      :min="0"
                    />
                  </div>
                  <div class="col-12 col-md-4">
                    <FieldNumber
                      id="limit-max-businesses"
                      label="Max negocios"
                      placeholder="Ilimitado"
                      v-model="form.limits.max_businesses"
                      :formError="form.errors['limits.max_businesses']"
                      :min="0"
                    />
                    <div class="form-text">Vacio: ilimitado. 0: no permite crear negocios.</div>
                  </div>
                  <div class="col-12 col-md-4">
                    <FieldNumber
                      id="limit-max-requests"
                      label="Max requests/dia"
                      placeholder="0"
                      v-model="form.limits.max_requests_per_day"
                      :formError="form.errors['limits.max_requests_per_day']"
                      :min="0"
                    />
                  </div>
                  <div class="col-12 col-md-4">
                    <FieldSwitch
                      id="limit-can-ai"
                      label="Puede usar AI"
                      v-model="form.limits.can_use_ai"
                    />
                  </div>
                  <div class="col-12 col-md-4">
                    <FieldSwitch
                      id="limit-can-export"
                      label="Puede exportar"
                      v-model="form.limits.can_export"
                    />
                  </div>
                  <div class="col-12 col-md-4">
                    <FieldSwitch
                      id="limit-can-upload"
                      label="Puede subir archivos"
                      v-model="form.limits.can_upload_files"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Guardar' }}
            </button>
            <Link href="/admin/plans" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const form = useForm({
  name: '',
  slug: '',
  description: '',
  price: '',
  billing_period: '',
  is_active: true,
  sort_order: '',
  stripe_product_id: '',
  stripe_price_id: '',
  limits: {
    max_items: '',
    max_businesses: '',
    max_requests_per_day: '',
    can_use_ai: false,
    can_export: false,
    can_upload_files: false,
  },
})

const breadcrumbs = [
  { label: 'Planes', href: '/admin/plans' },
  { label: 'Crear', active: true },
]

const submit = () => {
  form.post('/admin/plans')
}
</script>
