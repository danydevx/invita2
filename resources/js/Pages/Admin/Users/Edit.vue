<template>
  <AdminLayout>
    <Head title="Editar usuario" />

    <PageHeader :title="'Editar usuario'" :breadcrumbs="breadcrumbs" backHref="/admin/users">
      <template #actions>
        <Link :href="`/admin/users/${user.id}/subscriptions`" class="btn btn-outline-secondary">
          Suscripcion
        </Link>
      </template>
    </PageHeader>

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
              label="Password (opcional)"
              v-model="form.password"
              v-model:confirmation="form.password_confirmation"
              :form-error="form.errors.password"
              :confirm-form-error="form.errors.password_confirmation"
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
              label="Selecciona los roles"
              idPrefix="role_"
              :formError="rolesError"
            />
          </div>

          <div class="col-12">
            <div class="row g-3">
              <div class="col-12 col-md-6">
                <FieldSwitch
                  id="user-active"
                  label="Usuario activo"
                  v-model="form.is_active"
                />
              </div>
              <div class="col-12 col-md-6">
                <div class="text-muted">
                  Email verificado: {{ user.email_verified_at ? 'Si' : 'No' }}
                </div>
                <div class="text-muted">
                  Telefono: {{ user.phone || '-' }}
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2">
                  <button
                    v-if="!user.email_verified_at"
                    type="button"
                    class="btn btn-sm btn-outline-success"
                    @click="verifyEmail"
                  >
                    Verificar usuario manualmente
                  </button>
                  <button
                    v-if="!user.email_verified_at"
                    type="button"
                    class="btn btn-sm btn-outline-warning"
                    @click="resendVerification"
                  >
                    Reenviar verificacion
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Actualizando...' : 'Actualizar' }}
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
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldGeneratePass from '@/Components/Fields/FieldGeneratePass.vue'
import FieldCheckboxes from '@/Components/Fields/FieldCheckboxes.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  roles: {
    type: Array,
    default: () => [],
  },
  userRoles: {
    type: Array,
    default: () => [],
  },
})

const visibleRoleIds = new Set(props.roles.map((role) => role.id))

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',
  password_confirmation: '',
  roles: props.userRoles.filter((roleId) => visibleRoleIds.has(roleId)),
  is_active: props.user.is_active ?? true,
})

const breadcrumbs = [
  { label: 'Usuarios', href: '/admin/users' },
  { label: 'Editar', active: true },
]

const rolesError = computed(() => {
  return form.errors.roles || form.errors['roles.0'] || ''
})

const submit = () => {
  form.put(`/admin/users/${props.user.id}`)
}

const verifyEmail = () => {
  router.put(`/admin/users/${props.user.id}/verify-email`, {}, {
    preserveScroll: true,
    onSuccess: () => router.reload({ only: ['user'] }),
  })
}

const resendVerification = () => {
  router.post(`/admin/users/${props.user.id}/resend-verification`)
}
</script>
