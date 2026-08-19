<template>
  <AdminLayout>
    <Head title="Usuarios" />

    <PageHeader :title="'Usuarios'" :breadcrumbs="breadcrumbs" backHref="/dashboard">
      <template #actions>
        <Link href="/admin/users/create" class="btn btn-primary">Agregar usuario</Link>
        <Link href="/admin/users/archived" class="btn btn-outline-warning">
          <i class="bi bi-archive me-1"></i>Archivados
        </Link>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-4">
            <label class="form-label">Buscar</label>
            <input
              v-model="search"
              type="text"
              class="form-control"
              placeholder="Nombre o email"
            />
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label">Estado</label>
            <select v-model="status" class="form-select">
              <option value="">Todos</option>
              <option value="active">Activo</option>
              <option value="inactive">Inactivo</option>
            </select>
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label">Verificacion</label>
            <select v-model="verified" class="form-select">
              <option value="">Todos</option>
              <option value="verified">Verificado</option>
              <option value="unverified">No verificado</option>
            </select>
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label">Rol</label>
            <select v-model="role" class="form-select">
              <option value="">Todos</option>
              <option v-for="roleItem in roles" :key="roleItem.id" :value="roleItem.id">
                {{ roleItem.label }}
              </option>
            </select>
          </div>
          <div class="col-12 col-md-2 d-flex gap-2">
            <button class="btn btn-outline-primary" type="submit">Filtrar</button>
            <button class="btn btn-outline-secondary" type="button" @click="clearFilters">Limpiar</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col" class="text-nowrap">ID</th>
              <th scope="col">Usuario</th>
              <th scope="col">Email</th>
              <th scope="col">Telefono</th>
              <th scope="col">Roles</th>
              <th scope="col">Estado</th>
              <th scope="col">Verificado</th>
              <th scope="col">Alta</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="users.data.length === 0">
              <td colspan="9" class="text-center text-muted py-4">No hay usuarios registrados.</td>
            </tr>
            <tr v-for="user in users.data" :key="user.id">
              <td class="text-muted">{{ user.id }}</td>
              <td class="fw-semibold">{{ user.name }}</td>
              <td>{{ user.email }}</td>
              <td>{{ user.phone || '-' }}</td>
              <td>
                <div class="d-flex flex-wrap gap-1">
                  <span v-if="user.roles.length === 0" class="text-muted">Sin roles</span>
                  <span v-for="role in user.roles" :key="role" class="badge text-bg-light border">
                    {{ role }}
                  </span>
                </div>
              </td>
              <td>
                <span v-if="user.is_active" class="badge text-bg-success">Activo</span>
                <span v-else class="badge text-bg-secondary">Inactivo</span>
              </td>
              <td>
                <span v-if="user.email_verified_at" class="badge text-bg-success">Verificado</span>
                <span v-else class="badge text-bg-warning">Pendiente</span>
              </td>
              <td class="text-muted">{{ user.created_at }}</td>
              <td class="text-end">
                <div class="d-inline-flex align-items-center gap-2">
                  <Link :href="`/admin/users/${user.id}/edit`" class="btn btn-sm btn-outline-primary">
                    Editar
                  </Link>
                  <button
                    v-if="user.is_active"
                    type="button"
                    class="btn btn-sm btn-outline-secondary"
                    :disabled="user.id === 1"
                    @click="openToggle(user, 'deactivate')"
                  >
                    Desactivar
                  </button>
                  <button
                    v-else
                    type="button"
                    class="btn btn-sm btn-outline-success"
                    :disabled="user.id === 1"
                    @click="openToggle(user, 'activate')"
                  >
                    Activar
                  </button>
                  <button
                    v-if="!user.email_verified_at"
                    type="button"
                    class="btn btn-sm btn-outline-warning"
                    @click="resendVerification(user)"
                  >
                    Reenviar verificacion
                  </button>
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-danger"
                    :disabled="user.id === 1"
                    @click="openDelete(user)"
                  >
                    Eliminar
                  </button>
                </div>
                <div v-if="user.id === 1" class="text-muted small mt-1">
                  No se puede eliminar: usuario protegido.
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">
          Mostrando {{ users.data.length }} de {{ users.total }} registros
        </div>

        <Pagination :links="users.links" />
      </div>
    </div>

    <ConfirmDialog
      v-model="confirmOpen"
      :title="confirmTitle"
      :message="confirmMessage"
      confirmLabel="Confirmar"
      cancelLabel="Cancelar"
      @confirm="confirmAction"
      @cancel="clearPending"
    />
  </AdminLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import ConfirmDialog from '@/Components/Admin/ConfirmDialog.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const props = defineProps({
  users: {
    type: Object,
    required: true,
  },
  roles: {
    type: Array,
    default: () => [],
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const confirmOpen = ref(false)
const pendingUser = ref(null)
const pendingAction = ref('delete')

const search = ref(props.filters.search ?? '')
const status = ref(props.filters.status ?? '')
const verified = ref(props.filters.verified ?? '')
const role = ref(props.filters.role ?? '')

const breadcrumbs = [
  { label: 'Usuarios' },
  { label: 'Registros', active: true },
]

const confirmMessage = computed(() => {
  if (!pendingUser.value) return 'Estas seguro de continuar?'
  if (pendingAction.value === 'delete') {
    return `Estas seguro de eliminar el usuario "${pendingUser.value.name}"?`
  }
  if (pendingAction.value === 'deactivate') {
    return `Estas seguro de desactivar el usuario "${pendingUser.value.name}"?`
  }
  return `Estas seguro de activar el usuario "${pendingUser.value.name}"?`
})

const confirmTitle = computed(() => {
  if (pendingAction.value === 'delete') return 'Eliminar usuario'
  if (pendingAction.value === 'deactivate') return 'Desactivar usuario'
  return 'Activar usuario'
})

const submitSearch = () => {
  router.get(
    '/admin/users',
    {
      search: search.value,
      status: status.value,
      verified: verified.value,
      role: role.value,
    },
    {
      preserveScroll: true,
      preserveState: true,
      replace: true,
    }
  )
}

const clearFilters = () => {
  search.value = ''
  status.value = ''
  verified.value = ''
  role.value = ''
  submitSearch()
}

const openDelete = (user) => {
  if (user.id === 1) return
  pendingUser.value = user
  pendingAction.value = 'delete'
  confirmOpen.value = true
}

const openToggle = (user, action) => {
  if (user.id === 1) return
  pendingUser.value = user
  pendingAction.value = action
  confirmOpen.value = true
}

const clearPending = () => {
  pendingUser.value = null
  pendingAction.value = 'delete'
}

const confirmAction = () => {
  if (!pendingUser.value) return
  if (pendingAction.value === 'delete') {
    router.delete(`/admin/users/${pendingUser.value.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        toast.success('Usuario eliminado correctamente.')
      },
      onFinish: () => {
        pendingUser.value = null
      },
    })
    return
  }

  const endpoint = pendingAction.value === 'deactivate'
    ? `/admin/users/${pendingUser.value.id}/deactivate`
    : `/admin/users/${pendingUser.value.id}/activate`

  router.put(endpoint, {}, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success(
        pendingAction.value === 'deactivate'
          ? 'Usuario desactivado correctamente.'
          : 'Usuario activado correctamente.'
      )
    },
    onFinish: () => {
      pendingUser.value = null
    },
  })
}

const resendVerification = (user) => {
  router.post(`/admin/users/${user.id}/resend-verification`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Correo de verificacion enviado.')
    },
  })
}
</script>
