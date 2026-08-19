<template>
  <AdminLayout>
    <Head title="Crear usuario" />

    <PageHeader :title="'Crear usuario'" :breadcrumbs="breadcrumbs" backHref="/admin/users" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <FieldText
              id="user-name"
              label="Usuario"
              placeholder="Ej: Juan Perez"
              v-model="form.name"
              :formError="form.errors.name"
              required
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldEmail
              id="user-email"
              label="Email"
              placeholder="usuario@empresa.com"
              v-model="form.email"
              :formError="form.errors.email"
              required
            />
          </div>

          <div class="col-12">
            <FieldGeneratePass
              id="user-password"
              confirm-id="user-password-confirmation"
              v-model="form.password"
              v-model:confirmation="form.password_confirmation"
              :form-error="form.errors.password"
              :confirm-form-error="form.errors.password_confirmation"
              required
            />
          </div>

          <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Roles</div>
              <span class="text-muted small">{{ roles.length }} disponibles</span>
            </div>

            <div v-if="roles.length === 0" class="alert alert-light mb-0">
              No hay roles registrados.
            </div>
            <FieldCheckboxes
              v-else
              v-model="form.roles"
              :items="roles"
              label="Selecciona el rol"
              idPrefix="role_"
              :formError="rolesError"
            />
          </div>

          <div class="col-12">
            <FieldSwitch
              id="user-active"
              label="Usuario activo"
              v-model="form.is_active"
            />
          </div>

          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Guardar' }}
            </button>
            <Link href="/admin/users" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldGeneratePass from '@/Components/Fields/FieldGeneratePass.vue'
import FieldCheckboxes from '@/Components/Fields/FieldCheckboxes.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const props = defineProps({
  roles: {
    type: Array,
    default: () => [],
  },
})

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  roles: [],
  is_active: true,
})

const breadcrumbs = [
  { label: 'Usuarios', href: '/admin/users' },
  { label: 'Crear', active: true },
]

const rolesError = computed(() => {
  return form.errors.roles || form.errors['roles.0'] || ''
})

const submit = () => {
  form.post('/admin/users')
}
</script>
