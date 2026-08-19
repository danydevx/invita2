<template>
  <AdminLayout>
    <Head title="Planes" />

    <PageHeader :title="'Planes'" :breadcrumbs="breadcrumbs" backHref="/dashboard">
      <template #actions>
        <Link href="/admin/plans/create" class="btn btn-primary">Agregar plan</Link>
      </template>
    </PageHeader>

    <div v-if="deleteError" class="alert alert-danger">
      {{ deleteError }}
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-6">
            <label class="form-label">Buscar</label>
            <input
              v-model="search"
              type="text"
              class="form-control"
              placeholder="Nombre o slug"
            />
          </div>
          <div class="col-12 col-md-6 d-flex gap-2">
            <button class="btn btn-outline-primary" type="submit">Buscar</button>
            <button class="btn btn-outline-secondary" type="button" @click="clearSearch">Limpiar</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">Nombre</th>
              <th scope="col">Slug</th>
              <th scope="col">Precio</th>
              <th scope="col">Periodo</th>
              <th scope="col">Activo</th>
              <th scope="col">Orden</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="plans.data.length === 0">
              <td colspan="7" class="text-center text-muted py-4">No hay planes registrados.</td>
            </tr>
            <tr v-for="plan in plans.data" :key="plan.id">
              <td class="fw-semibold">{{ plan.name }}</td>
              <td class="text-muted">{{ plan.slug }}</td>
              <td v-format-price="plan.price"></td>
              <td>{{ plan.billing_period || '-' }}</td>
              <td>
                <span v-if="plan.is_active" class="badge text-bg-success">Activo</span>
                <span v-else class="badge text-bg-secondary">Inactivo</span>
              </td>
              <td>{{ plan.sort_order ?? '-' }}</td>
              <td class="text-end">
                <div class="d-inline-flex align-items-center gap-2">
                  <Link :href="`/admin/plans/${plan.id}/edit`" class="btn btn-sm btn-outline-primary">
                    Editar
                  </Link>
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-danger"
                    @click="openDelete(plan)"
                  >
                    Eliminar
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">
          Mostrando {{ plans.data.length }} de {{ plans.total }} registros
        </div>

        <Pagination :links="plans.links" />
      </div>
    </div>

    <ConfirmDialog
      v-model="confirmOpen"
      title="Eliminar plan"
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
import PageHeader from '@/Components/Admin/PageHeader.vue'
import Pagination from '@/Components/Admin/Pagination.vue'
import ConfirmDialog from '@/Components/Admin/ConfirmDialog.vue'

const props = defineProps({
  plans: {
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
const pendingPlan = ref(null)

const breadcrumbs = [
  { label: 'Planes' },
  { label: 'Registros', active: true },
]

const deleteError = computed(() => page.props.errors?.delete)
const confirmMessage = computed(() => {
  if (!pendingPlan.value) return 'Estas seguro de eliminar este plan?'
  return `Estas seguro de eliminar el plan "${pendingPlan.value.name}"?`
})

const submitSearch = () => {
  router.get(
    '/admin/plans',
    { search: search.value },
    { preserveState: true, replace: true, preserveScroll: true }
  )
}

const clearSearch = () => {
  search.value = ''
  submitSearch()
}

const openDelete = (plan) => {
  pendingPlan.value = plan
  confirmOpen.value = true
}

const clearPending = () => {
  pendingPlan.value = null
}

const confirmDelete = () => {
  if (!pendingPlan.value) return
  router.delete(`/admin/plans/${pendingPlan.value.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Plan eliminado correctamente.')
    },
    onFinish: () => {
      pendingPlan.value = null
    },
  })
}
</script>
