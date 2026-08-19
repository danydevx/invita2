<template>
  <AdminLayout>
    <Head title="Editar rol" />

    <PageHeader :title="'Editar rol'" :breadcrumbs="breadcrumbs" backHref="/admin/roles" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12">
            <FieldText
              id="role-name"
              label="Nombre del rol"
              placeholder="Ej: Administrador"
              v-model="form.name"
              :formError="form.errors.name"
              required
            />
          </div>

          <div class="col-12">
            <div class="form-check">
              <input
                id="role-blocked"
                v-model="form.blocked"
                class="form-check-input"
                type="checkbox"
              />
              <label class="form-check-label" for="role-blocked">
                Bloquear asignacion a usuarios
              </label>
            </div>
          </div>

          <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Permisos</div>
              <span class="text-muted small">{{ permissions.length }} disponibles</span>
            </div>

            <div v-if="permissions.length === 0" class="alert alert-light mb-0">
              No hay permisos registrados.
            </div>
            <div v-else>
              <div v-for="group in groupedPermissions" :key="group.key" class="mb-3">
                <div class="fw-semibold mb-2">{{ group.label }}</div>
                <div class="row">
                  <div class="col-md-3 mb-2" v-for="item in group.items" :key="item.id">
                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        :id="`perm_${item.id}`"
                        :value="item.id"
                        :checked="form.permissions.includes(item.id)"
                        @change="onPermissionChange($event, item.id)"
                      />
                      <label class="form-check-label" :for="`perm_${item.id}`">
                        {{ item.label }}
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="permissionsError" class="text-danger mt-2">
                {{ permissionsError }}
              </div>
            </div>
          </div>

          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Actualizando...' : 'Actualizar' }}
            </button>
            <Link href="/admin/roles" class="btn btn-outline-secondary">Cancelar</Link>
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

const props = defineProps({
  role: {
    type: Object,
    required: true,
  },
  permissions: {
    type: Array,
    default: () => [],
  },
  rolePermissions: {
    type: Array,
    default: () => [],
  },
})

const form = useForm({
  name: props.role.name,
  permissions: [...props.rolePermissions],
  blocked: props.role.blocked ?? false,
})

const breadcrumbs = [
  { label: 'Roles', href: '/admin/roles' },
  { label: 'Editar', active: true },
]

const permissionsError = computed(() => {
  return form.errors.permissions || form.errors['permissions.0'] || ''
})

// Agrupa permisos por modulo para hacer la seleccion mas clara.
const groupedPermissions = computed(() => {
  const groups = {}

  const moduleLabels = {
    users: 'Usuarios',
    roles: 'Roles',
    permissions: 'Permisos',
    settings: 'Settings',
    plans: 'Planes',
    subscriptions: 'Suscripciones',
    payments: 'Pagos',
    coupons: 'Cupones',
    invoices: 'Comprobantes',
    support: 'Soporte',
    help: 'Centro de ayuda',
    reports: 'Reportes',
    activity: 'Actividad',
    exports: 'Exportaciones',
    'system-errors': 'Errores del sistema',
    'api-keys': 'API Keys',
    webhooks: 'Webhooks',
    queues: 'Colas',
    'feature-flags': 'Feature Flags',
    'security-events': 'Seguridad',
    invitations: 'Invitaciones',
    'legal-documents': 'Legales',
    announcements: 'Anuncios',
    automations: 'Automatizaciones',
    templates: 'Plantillas',
  }

  props.permissions.forEach((permission) => {
    const parts = permission.label.split('.')
    const key = parts[0] || 'otros'
    if (!groups[key]) {
      groups[key] = {
        key,
        label: moduleLabels[key] || key,
        items: [],
      }
    }
    groups[key].items.push(permission)
  })

  return Object.values(groups)
})

// Maneja la seleccion de permisos de forma manual.
const onPermissionChange = (event, id) => {
  const values = [...form.permissions]
  if (event.target.checked) {
    if (!values.includes(id)) values.push(id)
  } else {
    const idx = values.indexOf(id)
    if (idx !== -1) values.splice(idx, 1)
  }
  form.permissions = values
}

const submit = () => {
  form.put(`/admin/roles/${props.role.id}`)
}
</script>
