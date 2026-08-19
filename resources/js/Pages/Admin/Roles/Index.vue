<template>
  <AdminLayout> 
    <Head title="Roles" />

    <PageHeader :title="'Roles'" :breadcrumbs="breadcrumbs" backHref="/dashboard">
      <template #actions>
        <button
          type="button"
          class="btn btn-primary"
          data-bs-toggle="modal"
          data-bs-target="#createRoleModal"
        >
          Agregar Rol
        </button>
        <Link href="/admin/roles/create" class="btn btn-outline-secondary">Crear en pagina</Link>
      </template>
    </PageHeader>

    <div v-if="deleteError" class="alert alert-danger">
      {{ deleteError }}
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-sm-6 col-lg-4">
            <label class="form-label">Buscar</label>
            <input
              v-model="search"
              type="text"
              class="form-control"
              placeholder="Buscar por ID o nombre"
            />
          </div>
          <div class="col-auto">
            <button class="btn btn-outline-primary" type="submit">Buscar</button>
          </div>
          <div class="col-auto">
            <button class="btn btn-outline-secondary" type="button" @click="clearSearch">Limpiar</button>
          </div>
        </form>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col" class="text-nowrap">
                <Link :href="sortHref('id')" class="text-decoration-none text-dark d-inline-flex gap-1">
                  ID
                  <i class="bi" :class="sortIcon('id')"></i>
                </Link>
              </th>
              <th scope="col">
                <Link :href="sortHref('name')" class="text-decoration-none text-dark d-inline-flex gap-1">
                  Nombre de rol
                  <i class="bi" :class="sortIcon('name')"></i>
                </Link>
              </th>
              <th scope="col" class="text-nowrap">Bloqueado</th>
              <th scope="col" class="text-nowrap">Usuarios</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="roles.data.length === 0">
              <td colspan="5" class="text-center text-muted py-4">No hay roles registrados.</td>
            </tr>
            <tr v-for="role in roles.data" :key="role.id">
              <td class="text-muted">{{ role.id }}</td>
              <td class="fw-semibold">{{ role.name }}</td>
              <td>
                <span v-if="role.blocked" class="badge text-bg-warning">Si</span>
                <span v-else class="text-muted">No</span>
              </td>
              <td>
                <span class="badge text-bg-light border">{{ role.users_count }}</span>
              </td>
              <td class="text-end">
                <div class="d-inline-flex align-items-center gap-2">
                  <Link :href="`/admin/roles/${role.id}/edit`" class="btn btn-sm btn-outline-primary">
                    Editar
                  </Link>
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-danger"
                    :disabled="role.users_count > 0 || role.protected"
                    @click="openDelete(role)"
                  >
                    Eliminar
                  </button>
                </div>
                <div v-if="role.users_count > 0" class="text-muted small mt-1">
                  No se puede eliminar: rol asignado.
                </div>
                <div v-else-if="role.protected" class="text-muted small mt-1">
                  No se puede eliminar: rol protegido.
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">
          Mostrando {{ roles.data.length }} de {{ roles.total }} registros
        </div>

        <Pagination :links="roles.links" />
      </div>
    </div>

    <CreateModal />
    <ConfirmDialog
      v-model="confirmOpen"
      title="Eliminar rol"
      :message="confirmMessage"
      confirmLabel="Eliminar"
      cancelLabel="Cancelar"
      @confirm="confirmDelete"
      @cancel="clearPending"
    />
  </AdminLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import CreateModal from './CreateModal.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import ConfirmDialog from '@/Components/Admin/ConfirmDialog.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const props = defineProps({
  roles: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const page = usePage()
const search = ref(props.filters.search ?? '')
const confirmOpen = ref(false)
const pendingRole = ref(null)

const breadcrumbs = [
  { label: 'Roles' },
  { label: 'Registros', active: true },
]

const deleteError = computed(() => page.props.errors?.delete)
const confirmMessage = computed(() => {
  if (!pendingRole.value) return 'Estas seguro de eliminar este rol?'
  return `Estas seguro de eliminar el rol "${pendingRole.value.name}"?`
})

const submitSearch = () => {
  router.get(
    '/admin/roles',
    {
      search: search.value,
      sort: props.filters.sort ?? 'id',
      direction: props.filters.direction ?? 'asc',
    },
    {
      preserveState: true,
      replace: true,
    }
  )
}

const clearSearch = () => {
  search.value = ''
  submitSearch()
}

const buildQuery = (overrides = {}) => {
  const params = new URLSearchParams()
  const searchValue = (overrides.search ?? search.value ?? '').toString().trim()

  if (searchValue) {
    params.set('search', searchValue)
  }

  const sort = overrides.sort ?? props.filters.sort ?? 'id'
  const direction = overrides.direction ?? props.filters.direction ?? 'asc'

  params.set('sort', sort)
  params.set('direction', direction)

  return params.toString()
}

const sortHref = (field) => {
  const currentSort = props.filters.sort ?? 'id'
  const currentDirection = props.filters.direction ?? 'asc'
  const direction = currentSort === field && currentDirection === 'asc' ? 'desc' : 'asc'
  const query = buildQuery({ sort: field, direction })
  return query ? `/admin/roles?${query}` : '/admin/roles'
}

const sortIcon = (field) => {
  if ((props.filters.sort ?? 'id') !== field) return 'bi-arrow-down-up'
  return (props.filters.direction ?? 'asc') === 'asc' ? 'bi-sort-up' : 'bi-sort-down'
}

const openDelete = (role) => {
  if (role.users_count > 0 || role.protected) return
  pendingRole.value = role
  confirmOpen.value = true
}

const clearPending = () => {
  pendingRole.value = null
}

const confirmDelete = () => {
  if (!pendingRole.value) return
  router.delete(`/admin/roles/${pendingRole.value.id}`, {
    preserveScroll: true,
    onSuccess: (page) => {
      if (page?.props?.errors?.delete) return
      toast.success('Rol eliminado correctamente.')
    },
    onFinish: () => {
      pendingRole.value = null
    },
  })
}
</script>
