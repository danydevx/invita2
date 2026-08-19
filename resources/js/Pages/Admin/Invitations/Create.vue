<template>
  <AdminLayout>
    <Head title="Nueva invitacion" />

    <PageHeader :title="'Nueva invitacion'" :breadcrumbs="breadcrumbs" backHref="/admin/invitations" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <FieldEmail
              id="invite-email"
              label="Email"
              v-model="form.email"
              :formError="form.errors.email"
              required
            />
          </div>
          <div class="col-12 col-md-6">
            <FieldText id="invite-role" label="Rol (opcional)" v-model="form.role_name" />
          </div>
          <div class="col-12 col-md-6">
            <FieldDate id="invite-expires" label="Expira" v-model="form.expires_at" />
          </div>
          <div class="col-12 col-md-6">
            <FieldSelect
              id="invite-plan"
              label="Plan por defecto"
              v-model="form.default_plan_id"
              :options="planOptions"
            />
          </div>
          <div class="col-12">
            <FieldText id="invite-feature" label="Feature flag" v-model="form.feature_flag" />
          </div>
          <div class="col-12">
            <FieldText id="invite-redirect" label="Redirect" v-model="form.redirect_to" />
          </div>
          <div class="col-12">
            <FieldTextarea id="invite-message" label="Mensaje" v-model="form.message" />
          </div>
          <div class="col-12">
            <button class="btn btn-primary" type="submit" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Crear invitacion' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldDate from '@/Components/Fields/FieldDate.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'

const props = defineProps({
  plans: {
    type: Array,
    default: () => [],
  },
})

const form = useForm({
  email: '',
  role_name: '',
  expires_at: '',
  message: '',
  redirect_to: '',
  default_plan_id: '',
  feature_flag: '',
})

const planOptions = [{ value: '', label: 'Sin plan' }, ...props.plans.map((plan) => ({
  value: plan.id,
  label: plan.name,
}))]

const breadcrumbs = [
  { label: 'Invitaciones', href: '/admin/invitations' },
  { label: 'Nueva', active: true },
]

const submit = () => {
  form.post('/admin/invitations')
}
</script>
