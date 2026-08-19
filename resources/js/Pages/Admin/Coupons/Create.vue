<template>
  <AdminLayout>
    <Head title="Crear cupon" />

    <PageHeader :title="'Crear cupon'" :breadcrumbs="breadcrumbs" backHref="/admin/coupons" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <FieldText
              id="coupon-code"
              label="Codigo"
              placeholder="PROMO10"
              v-model="form.code"
              :formError="form.errors.code"
              required
            />
          </div>
          <div class="col-12 col-md-6">
            <FieldText
              id="coupon-name"
              label="Nombre"
              placeholder="Descuento lanzamiento"
              v-model="form.name"
              :formError="form.errors.name"
              required
            />
          </div>

          <div class="col-12">
            <FieldTextarea
              id="coupon-description"
              label="Descripcion"
              placeholder="Detalle del cupon"
              v-model="form.description"
              :formError="form.errors.description"
            />
          </div>

          <div class="col-12 col-md-4">
            <FieldSelect
              id="coupon-type"
              label="Tipo"
              v-model="form.type"
              :options="typeOptions"
              :formError="form.errors.type"
              required
            />
          </div>
          <div class="col-12 col-md-4">
            <FieldNumber
              id="coupon-value"
              label="Valor"
              placeholder="10"
              v-model="form.value"
              :formError="form.errors.value"
              :min="0"
              required
            />
          </div>
          <div class="col-12 col-md-4">
            <FieldNumber
              id="coupon-limit"
              label="Limite de uso"
              placeholder="100"
              v-model="form.usage_limit"
              :formError="form.errors.usage_limit"
              :min="1"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldDate
              id="coupon-start"
              label="Inicio"
              v-model="form.starts_at"
              :formError="form.errors.starts_at"
            />
          </div>
          <div class="col-12 col-md-6">
            <FieldDate
              id="coupon-end"
              label="Fin"
              v-model="form.ends_at"
              :formError="form.errors.ends_at"
            />
          </div>

          <div class="col-12">
            <FieldSwitch
              id="coupon-active"
              label="Cupon activo"
              v-model="form.is_active"
            />
          </div>

          <div class="col-12">
            <FieldSwitch
              id="coupon-applies-all"
              label="Aplica a todos los planes"
              v-model="form.applies_to_all_plans"
            />
          </div>

          <div v-if="!form.applies_to_all_plans" class="col-12">
            <FieldCheckboxes
              label="Planes"
              :items="plans"
              v-model="form.plans"
              :formError="form.errors.plans"
            />
          </div>

          <div class="col-12">
            <div class="card border rounded-3">
              <div class="card-body">
                <h3 class="h6 mb-3">Stripe</h3>
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <FieldText
                      id="coupon-stripe-coupon"
                      label="Stripe coupon id"
                      placeholder="coupon_123"
                      v-model="form.stripe_coupon_id"
                      :formError="form.errors.stripe_coupon_id"
                    />
                  </div>
                  <div class="col-12 col-md-6">
                    <FieldText
                      id="coupon-stripe-promo"
                      label="Stripe promotion code id"
                      placeholder="promo_123"
                      v-model="form.stripe_promotion_code_id"
                      :formError="form.errors.stripe_promotion_code_id"
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
            <Link href="/admin/coupons" class="btn btn-outline-secondary">Cancelar</Link>
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
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldDate from '@/Components/Fields/FieldDate.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldCheckboxes from '@/Components/Fields/FieldCheckboxes.vue'

const props = defineProps({
  plans: {
    type: Array,
    default: () => [],
  },
})

const form = useForm({
  code: '',
  name: '',
  description: '',
  type: 'percent',
  value: '',
  is_active: true,
  starts_at: '',
  ends_at: '',
  usage_limit: '',
  applies_to_all_plans: true,
  plans: [],
  stripe_coupon_id: '',
  stripe_promotion_code_id: '',
})

const typeOptions = [
  { value: 'percent', label: 'percent' },
  { value: 'fixed', label: 'fixed' },
]

const breadcrumbs = [
  { label: 'Cupones', href: '/admin/coupons' },
  { label: 'Crear', active: true },
]

const submit = () => {
  form.post('/admin/coupons')
}
</script>
