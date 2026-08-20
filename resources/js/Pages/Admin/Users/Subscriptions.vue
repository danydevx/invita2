<template>
  <AdminLayout>
    <Head title="Suscripcion de Usuario" />

    <PageHeader :title="'Suscripcion: ' + user.name" :breadcrumbs="breadcrumbs" :backHref="`/admin/users/${user.id}/edit`" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div v-if="subscription">
          <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
              <label class="form-label">Plan</label>
              <div class="form-control-plaintext">{{ planName }}</div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Estado</label>
              <div class="form-control-plaintext">
                <span :class="statusClass">{{ subscription.status }}</span>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Inicio</label>
              <div class="form-control-plaintext">{{ subscription.starts_at || '-' }}</div>
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Fin</label>
              <div class="form-control-plaintext">{{ subscription.ends_at || '-' }}</div>
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Periodo de prueba</label>
              <div class="form-control-plaintext">{{ subscription.trial_ends_at || '-' }}</div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Precio</label>
              <div class="form-control-plaintext">{{ subscription.price ? '$' + subscription.price : '-' }}</div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Billing Period</label>
              <div class="form-control-plaintext">{{ subscription.billing_period || '-' }}</div>
            </div>
          </div>

          <hr class="my-4">

          <h4 class="h6 mb-3">Actualizar Suscripcion</h4>
        </div>

        <form v-if="hasSubscription" @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Plan</label>
              <select v-model="form.plan_id" class="form-select" :class="{ 'is-invalid': form.errors.plan_id }">
                <option value="">Sin plan</option>
                <option v-for="plan in plans" :key="plan.id" :value="plan.id">
                  {{ plan.label }}
                </option>
              </select>
              <div v-if="form.errors.plan_id" class="invalid-feedback">{{ form.errors.plan_id }}</div>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Estado</label>
              <select v-model="form.status" class="form-select" :class="{ 'is-invalid': form.errors.status }">
                <option value="pending">Pending</option>
                <option value="trial">Trial</option>
                <option value="active">Active</option>
                <option value="expired">Expired</option>
                <option value="canceled">Canceled</option>
              </select>
              <div v-if="form.errors.status" class="invalid-feedback">{{ form.errors.status }}</div>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Fecha de inicio</label>
              <input v-model="form.starts_at" type="date" class="form-control" :class="{ 'is-invalid': form.errors.starts_at }" />
              <div v-if="form.errors.starts_at" class="invalid-feedback">{{ form.errors.starts_at }}</div>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Fecha de fin</label>
              <input v-model="form.ends_at" type="date" class="form-control" :class="{ 'is-invalid': form.errors.ends_at }" />
              <div v-if="form.errors.ends_at" class="invalid-feedback">{{ form.errors.ends_at }}</div>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Trial ends</label>
              <input v-model="form.trial_ends_at" type="date" class="form-control" :class="{ 'is-invalid': form.errors.trial_ends_at }" />
              <div v-if="form.errors.trial_ends_at" class="invalid-feedback">{{ form.errors.trial_ends_at }}</div>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Precio</label>
              <input v-model="form.price" type="number" class="form-control" min="0" step="0.01" />
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Billing Period</label>
              <input v-model="form.billing_period" type="text" class="form-control" placeholder="monthly, yearly, etc." />
            </div>
          </div>

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
            </button>
          </div>
        </form>

        <form v-else @submit.prevent="submitNew">
          <div class="alert alert-info mb-4">
            Este usuario no tiene una suscripcion activa. Asigna una para habilitar el acceso a funcionalidades de pago.
          </div>
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Plan</label>
              <select v-model="form.plan_id" class="form-select" :class="{ 'is-invalid': form.errors.plan_id }">
                <option value="">Sin plan</option>
                <option v-for="plan in plans" :key="plan.id" :value="plan.id">
                  {{ plan.label }}
                </option>
              </select>
              <div v-if="form.errors.plan_id" class="invalid-feedback">{{ form.errors.plan_id }}</div>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Estado</label>
              <select v-model="form.status" class="form-select" :class="{ 'is-invalid': form.errors.status }">
                <option value="pending">Pending</option>
                <option value="trial">Trial</option>
                <option value="active">Active</option>
                <option value="expired">Expired</option>
                <option value="canceled">Canceled</option>
              </select>
              <div v-if="form.errors.status" class="invalid-feedback">{{ form.errors.status }}</div>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Fecha de inicio</label>
              <input v-model="form.starts_at" type="date" class="form-control" />
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Fecha de fin</label>
              <input v-model="form.ends_at" type="date" class="form-control" />
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Trial ends</label>
              <input v-model="form.trial_ends_at" type="date" class="form-control" />
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Precio</label>
              <input v-model="form.price" type="number" class="form-control" min="0" step="0.01" />
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Billing Period</label>
              <input v-model="form.billing_period" type="text" class="form-control" placeholder="monthly, yearly, etc." />
            </div>
          </div>

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Creando...' : 'Crear Suscripcion' }}
            </button>
          </div>
        </form>

        <div v-if="subscription" class="mt-4">
          <hr class="my-4">
          <form @submit.prevent="destroySubscription">
            <button type="submit" class="btn btn-outline-danger" :disabled="form.processing" onclick="return confirm('Estas seguro de eliminar esta suscripcion?')">
              Eliminar Suscripcion
            </button>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  plans: {
    type: Array,
    default: () => [],
  },
  subscription: {
    type: Object,
    default: null,
  },
})

const hasSubscription = computed(() => !!props.subscription)

const planName = computed(() => {
  if (!props.subscription || !props.subscription.plan_id) return '-'
  const plan = props.plans.find(p => p.id === props.subscription.plan_id)
  return plan ? plan.label : '-'
})

const statusClass = computed(() => {
  const status = props.subscription?.status
  const classes = {
    'active': 'badge bg-success',
    'trial': 'badge bg-info',
    'pending': 'badge bg-warning',
    'expired': 'badge bg-secondary',
    'canceled': 'badge bg-danger',
  }
  return classes[status] || 'badge bg-secondary'
})

const form = useForm({
  plan_id: props.subscription?.plan_id || '',
  status: props.subscription?.status || 'active',
  starts_at: props.subscription?.starts_at || '',
  ends_at: props.subscription?.ends_at || '',
  trial_ends_at: props.subscription?.trial_ends_at || '',
  price: props.subscription?.price || '',
  billing_period: props.subscription?.billing_period || '',
})

const submit = () => {
  form.put(`/admin/users/${props.user.id}/subscriptions`)
}

const submitNew = () => {
  form.post(`/admin/users/${props.user.id}/subscriptions`)
}

const destroySubscription = () => {
  if (confirm('Estas seguro de eliminar esta suscripcion?')) {
    form.delete(`/admin/users/${props.user.id}/subscriptions`)
  }
}

const breadcrumbs = [
  { label: 'Usuarios', href: '/admin/users' },
  { label: props.user.name, href: `/admin/users/${props.user.id}/edit` },
  { label: 'Suscripcion', active: true },
]
</script>
