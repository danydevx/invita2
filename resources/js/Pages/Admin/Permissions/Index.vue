<template>
  <AdminLayout>
    <Head title="Permisos" />

    <PageHeader :title="'Permisos'" :breadcrumbs="breadcrumbs" backHref="/dashboard">
      <template #actions>
        <button
          type="button"
          class="btn btn-primary"
          data-bs-toggle="modal"
          data-bs-target="#createPermissionModal"
        >
          Agregar Permiso
        </button>
        <Link href="/admin/permissions/create" class="btn btn-outline-secondary">Crear en pagina</Link>
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
                <Link :href="sortHref('order')" class="text-decoration-none text-dark d-inline-flex gap-1">
                  Orden
                  <i class="bi" :class="sortIcon('order')"></i>
                </Link>
              </th>
              <th scope="col" class="text-nowrap">
                <Link :href="sortHref('id')" class="text-decoration-none text-dark d-inline-flex gap-1">
                  ID
                  <i class="bi" :class="sortIcon('id')"></i>
                </Link>
              </th>
              <th scope="col">
                <Link :href="sortHref('name')" class="text-decoration-none text-dark d-inline-flex gap-1">
                  Nombre del permiso
                  <i class="bi" :class="sortIcon('name')"></i>
                </Link>
              </th>
              <th scope="col" class="text-nowrap">Roles</th>
              <th scope="col" class="text-nowrap">Usuarios</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody v-if="rows.length === 0">
            <tr>
              <td colspan="6" class="text-center text-muted py-4">No hay permisos registrados.</td>
            </tr>
          </tbody>
          <Draggable
            v-else
            v-model="rows"
            item-key="id"
            tag="tbody"
            handle=".drag-handle"
            :disabled="!canReorder"
            @end="onReorder"
          >
            <template #item="{ element: permission }">
              <tr>
                <td class="text-muted">
                  <span class="d-inline-flex align-items-center gap-2">
                    <i v-if="canReorder" class="bi bi-grip-vertical drag-handle"></i>
                    <span>{{ permission.order }}</span>
                  </span>
                </td>
                <td class="text-muted">{{ permission.id }}</td>
                <td class="fw-semibold">{{ permission.name }}</td>
                <td>
                  <span class="badge text-bg-light border">{{ permission.roles_count }}</span>
                </td>
                <td>
                  <span class="badge text-bg-light border">{{ permission.users_count }}</span>
                </td>
                <td class="text-end">
                  <div class="d-inline-flex align-items-center gap-2">
                    <Link
                      :href="`/admin/permissions/${permission.id}/edit`"
                      class="btn btn-sm btn-outline-primary"
                    >
                      Editar
                    </Link>
                    <button
                      type="button"
                      class="btn btn-sm btn-outline-danger"
                      :disabled="permission.roles_count + permission.users_count > 0"
                      @click="openDelete(permission)"
                    >
                      Eliminar
                    </button>
                  </div>
                  <div v-if="permission.roles_count + permission.users_count > 0" class="text-muted small mt-1">
                    No se puede eliminar: permiso asignado.
                  </div>
                </td>
              </tr>
            </template>
          </Draggable>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">
          Mostrando {{ rows.length }} de {{ permissions.total }} registros
        </div>

        <Pagination :links="permissions.links" />
      </div>
    </div>

    <CreateModal />
    <ConfirmDialog
      v-model="confirmOpen"
      title="Eliminar permiso"
      :message="confirmMessage"
      confirmLabel="Eliminar"
      cancelLabel="Cancelar"
      @confirm="confirmDelete"
      @cancel="clearPending"
    />
  </AdminLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import CreateModal from './CreateModal.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import ConfirmDialog from '@/Components/Admin/ConfirmDialog.vue'
import Pagination from '@/Components/Admin/Pagination.vue'
import Draggable from 'vuedraggable'

const props = defineProps({
  permissions: {
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
const pendingPermission = ref(null)
const rows = ref([...props.permissions.data])

const breadcrumbs = [
  { label: 'Permisos' },
  { label: 'Registros', active: true },
]

const deleteError = computed(() => page.props.errors?.delete)
const currentPage = computed(() => props.permissions.current_page ?? 1)
const perPage = computed(() => (props.permissions.per_page ?? rows.value.length) || 1)
const canReorder = computed(() => {
  const sort = props.filters.sort ?? 'order'
  const direction = props.filters.direction ?? 'asc'
  return sort === 'order' && direction === 'asc'
})
const confirmMessage = computed(() => {
  if (!pendingPermission.value) return 'Estas seguro de eliminar este permiso?'
  return `Estas seguro de eliminar el permiso "${pendingPermission.value.name}"?`
})

const submitSearch = () => {
  router.get(
    '/admin/permissions',
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

  const sort = overrides.sort ?? props.filters.sort ?? 'order'
  const direction = overrides.direction ?? props.filters.direction ?? 'asc'

  params.set('sort', sort)
  params.set('direction', direction)

  return params.toString()
}

const sortHref = (field) => {
  const currentSort = props.filters.sort ?? 'order'
  const currentDirection = props.filters.direction ?? 'asc'
  const direction = currentSort === field && currentDirection === 'asc' ? 'desc' : 'asc'
  const query = buildQuery({ sort: field, direction })
  return query ? `/admin/permissions?${query}` : '/admin/permissions'
}

const sortIcon = (field) => {
  if ((props.filters.sort ?? 'order') !== field) return 'bi-arrow-down-up'
  return (props.filters.direction ?? 'asc') === 'asc' ? 'bi-sort-up' : 'bi-sort-down'
}

watch(
  () => props.permissions.data,
  (value) => {
    rows.value = [...value]
  }
)

const openDelete = (permission) => {
  if (permission.roles_count + permission.users_count > 0) return
  pendingPermission.value = permission
  confirmOpen.value = true
}

const clearPending = () => {
  pendingPermission.value = null
}

const confirmDelete = () => {
  if (!pendingPermission.value) return
  router.delete(`/admin/permissions/${pendingPermission.value.id}`, {
    preserveScroll: true,
    onSuccess: (page) => {
      if (page?.props?.errors?.delete) return
      toast.success('Permiso eliminado correctamente.')
    },
    onFinish: () => {
      pendingPermission.value = null
    },
  })
}

const applyLocalOrder = () => {
  const start = ((currentPage.value - 1) * perPage.value) + 1
  rows.value = rows.value.map((item, index) => ({
    ...item,
    order: start + index,
  }))
}

const onReorder = () => {
  if (!canReorder.value) return
  applyLocalOrder()
  router.post('/admin/permissions/reorder', {
    ids: rows.value.map((item) => item.id),
    page: currentPage.value,
    perPage: perPage.value,
  }, {
    preserveScroll: true,
    preserveState: true,
  })
}
</script>
