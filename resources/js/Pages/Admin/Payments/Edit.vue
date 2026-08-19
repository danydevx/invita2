<template>
  <AdminLayout>
    <Head title="Editar pago" />

    <PageHeader :title="'Editar pago'" :breadcrumbs="breadcrumbs" backHref="/admin/payments" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12">
            <FieldSelect
              id="payment-user"
              label="Usuario"
              v-model="form.user_id"
              :options="users"
              :formError="form.errors.user_id"
              required
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldSelect
              id="payment-plan"
              label="Plan"
              v-model="form.plan_id"
              :options="plans"
              :formError="form.errors.plan_id"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldSelect
              id="payment-subscription"
              label="Suscripcion"
              v-model="form.subscription_id"
              :options="subscriptions"
              :formError="form.errors.subscription_id"
            />
          </div>

          <div class="col-12 col-md-4">
            <FieldMoney
              id="payment-amount"
              label="Monto"
              v-model="form.amount"
              :formError="form.errors.amount"
              required
            />
          </div>

          <div class="col-12 col-md-4">
            <FieldText
              id="payment-currency"
              label="Moneda"
              placeholder="USD"
              v-model="form.currency"
              :formError="form.errors.currency"
            />
          </div>

          <div class="col-12 col-md-4">
            <FieldSelect
              id="payment-status"
              label="Estado"
              v-model="form.status"
              :options="statusOptions"
              :formError="form.errors.status"
              required
            />
          </div>

          <div class="col-12 col-md-4">
            <FieldText
              id="payment-provider"
              label="Proveedor"
              placeholder="manual"
              v-model="form.provider"
              :formError="form.errors.provider"
            />
          </div>

          <div class="col-12 col-md-4">
            <FieldText
              id="payment-reference"
              label="Referencia"
              placeholder="ref-123"
              v-model="form.provider_reference"
              :formError="form.errors.provider_reference"
            />
          </div>

          <div class="col-12 col-md-4">
            <FieldText
              id="payment-method"
              label="Metodo"
              placeholder="transferencia"
              v-model="form.payment_method"
              :formError="form.errors.payment_method"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldDate
              id="payment-paid-at"
              label="Pagado el"
              v-model="form.paid_at"
              :formError="form.errors.paid_at"
            />
          </div>

          <div class="col-12">
            <FieldTextarea
              id="payment-description"
              label="Descripcion"
              placeholder="Detalle del pago"
              v-model="form.description"
              :formError="form.errors.description"
            />
          </div>

          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
            </button>
            <Link href="/admin/payments" class="btn btn-outline-secondary">Cancelar</Link>
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
import FieldMoney from '@/Components/Fields/FieldMoney.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldDate from '@/Components/Fields/FieldDate.vue'

const props = defineProps({
  payment: {
    type: Object,
    required: true,
  },
  users: {
    type: Array,
    default: () => [],
  },
  plans: {
    type: Array,
    default: () => [],
  },
  subscriptions: {
    type: Array,
    default: () => [],
  },
  statuses: {
    type: Array,
    default: () => [],
  },
})

const form = useForm({
  user_id: props.payment.user_id ?? '',
  subscription_id: props.payment.subscription_id ?? '',
  plan_id: props.payment.plan_id ?? '',
  amount: props.payment.amount ?? '',
  currency: props.payment.currency ?? 'USD',
  status: props.payment.status ?? 'pending',
  provider: props.payment.provider ?? 'manual',
  provider_reference: props.payment.provider_reference ?? '',
  payment_method: props.payment.payment_method ?? '',
  description: props.payment.description ?? '',
  paid_at: props.payment.paid_at ?? '',
})

const statusOptions = props.statuses.map((status) => ({
  value: status,
  label: status,
}))

const breadcrumbs = [
  { label: 'Pagos', href: '/admin/payments' },
  { label: `Pago #${props.payment.id}`, active: true },
]

const submit = () => {
  form.put(`/admin/payments/${props.payment.id}`)
}
</script>
