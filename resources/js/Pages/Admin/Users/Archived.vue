<template>
  <AdminLayout>
    <Head title="Usuarios Archivados" />

    <PageHeader title="Usuarios Archivados" :breadcrumbs="breadcrumbs" backHref="/admin/users">
      <template #actions>
        <Link href="/admin/users" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-2"></i>Volver a usuarios
        </Link>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-6">
            <label class="form-label">Buscar</label>
            <input
              v-model="search"
              type="text"
              class="form-control"
              placeholder="Nombre, email o ID"
            />
          </div>
          <div class="col-12 col-md-4 d-flex gap-2">
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
              <th scope="col">ID</th>
              <th scope="col">Usuario</th>
              <th scope="col">Email</th>
              <th scope="col">Telefono</th>
              <th scope="col">Roles</th>
              <th scope="col">Eliminado el</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="users.data.length === 0">
              <td colspan="7" class="text-center text-muted py-4">No hay usuarios archivados.</td>
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
              <td class="text-muted">{{ user.deleted_at }}</td>
              <td class="text-end">
                <div class="d-inline-flex align-items-center gap-2">
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-success"
                    @click="openRestore(user)"
                  >
                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    Restaurar
                  </button>
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-danger"
                    @click="openForceDelete(user)"
                  >
                    <i class="bi bi-trash me-1"></i>
                    Eliminar permanentemente
                  </button>
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
      :danger="confirmAction === 'force-delete'"
      @confirm="confirmAction === 'restore' ? doRestore() : doForceDelete()"
      @cancel="clearPending"
    />

    <ForceDeleteDialog
      v-model="forceDeleteOpen"
      :user="pendingUser"
      @confirm="doForceDelete"
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
import ForceDeleteDialog from '@/Components/Admin/ForceDeleteDialog.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const props = defineProps({
  users: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const search = ref(props.filters.search ?? '')

const breadcrumbs = [
  { label: 'Admin', href: '/admin/dashboard' },
  { label: 'Usuarios', href: '/admin/users' },
  { label: 'Archivados', active: true },
]

const confirmOpen = ref(false)
const forceDeleteOpen = ref(false)
const pendingUser = ref(null)
const confirmAction = ref('restore')

const confirmMessage = computed(() => {
  if (!pendingUser.value) return 'Estas seguro de continuar?'
  if (confirmAction.value === 'restore') {
    return `Estas seguro de restaurar el usuario "${pendingUser.value.name}"?`
  }
  return `Estas seguro de eliminar permanentemente el usuario "${pendingUser.value.name}"? Esta accion no se puede deshacer.`
})

const confirmTitle = computed(() => {
  if (confirmAction.value === 'restore') return 'Restaurar usuario'
  return 'Eliminar permanentemente'
})

const submitSearch = () => {
  router.get(
    '/admin/users/archived',
    { search: search.value },
    {
      preserveScroll: true,
      preserveState: true,
      replace: true,
    }
  )
}

const clearFilters = () => {
  search.value = ''
  submitSearch()
}

const openRestore = (user) => {
  pendingUser.value = user
  confirmAction.value = 'restore'
  confirmOpen.value = true
}

const openForceDelete = (user) => {
  pendingUser.value = user
  confirmAction.value = 'force-delete'
  forceDeleteOpen.value = true
}

const clearPending = () => {
  pendingUser.value = null
  confirmAction.value = 'restore'
}

const doRestore = () => {
  if (!pendingUser.value) return
  router.post(`/admin/users/${pendingUser.value.id}/restore`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Usuario restaurado correctamente.')
      clearPending()
    },
  })
}

const doForceDelete = () => {
  if (!pendingUser.value) return
  router.delete(`/admin/users/${pendingUser.value.id}/force`, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Usuario eliminado permanentemente.')
      clearPending()
    },
  })
}
</script>
