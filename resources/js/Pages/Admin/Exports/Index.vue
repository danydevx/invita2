<template>
  <AdminLayout>
    <Head title="Exportaciones" />

    <PageHeader :title="'Exportaciones'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <div class="row g-3">
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
              <div>
                <h2 class="h5 mb-1">Usuarios</h2>
                <p class="text-muted mb-0">Exporta usuarios con filtros basicos.</p>
              </div>
              <button form="export-users" type="submit" class="btn btn-primary">Descargar CSV</button>
            </div>
            <form id="export-users" method="get" action="/admin/exports/users" class="row g-2 mt-2">
              <div class="col-12 col-md-4">
                <label class="form-label">Buscar</label>
                <input name="q" type="text" class="form-control" placeholder="Nombre, email o ID" />
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label">Estado</label>
                <select name="is_active" class="form-select">
                  <option value="">Todos</option>
                  <option value="active">Activo</option>
                  <option value="inactive">Inactivo</option>
                </select>
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label">Verificacion</label>
                <select name="email_verified" class="form-select">
                  <option value="">Todos</option>
                  <option value="verified">Verificado</option>
                  <option value="unverified">No verificado</option>
                </select>
              </div>
              <div class="col-12 col-md-2">
                <label class="form-label">Rol</label>
                <select name="role" class="form-select">
                  <option value="">Todos</option>
                  <option v-for="role in roles" :key="role.id" :value="role.id">
                    {{ role.label }}
                  </option>
                </select>
              </div>
              <div class="col-6 col-md-1">
                <label class="form-label">Desde</label>
                <input name="date_from" type="date" class="form-control" />
              </div>
              <div class="col-6 col-md-1">
                <label class="form-label">Hasta</label>
                <input name="date_to" type="date" class="form-control" />
              </div>
              <div class="col-12">
                <button type="reset" class="btn btn-outline-secondary btn-sm">Limpiar filtros</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
              <div>
                <h2 class="h5 mb-1">Suscripciones</h2>
                <p class="text-muted mb-0">Exporta el historico de suscripciones.</p>
              </div>
              <button form="export-subscriptions" type="submit" class="btn btn-primary">Descargar CSV</button>
            </div>
            <form
              id="export-subscriptions"
              method="get"
              action="/admin/exports/subscriptions"
              class="row g-2 mt-2"
            >
              <div class="col-6 col-md-3">
                <label class="form-label">Estado</label>
                <select name="status" class="form-select">
                  <option value="">Todos</option>
                  <option v-for="item in subscriptionStatuses" :key="item" :value="item">
                    {{ item }}
                  </option>
                </select>
              </div>
              <div class="col-6 col-md-3">
                <label class="form-label">Plan</label>
                <select name="plan_id" class="form-select">
                  <option value="">Todos</option>
                  <option v-for="plan in plans" :key="plan.id" :value="plan.id">
                    {{ plan.label }}
                  </option>
                </select>
              </div>
              <div class="col-6 col-md-3">
                <label class="form-label">Desde</label>
                <input name="date_from" type="date" class="form-control" />
              </div>
              <div class="col-6 col-md-3">
                <label class="form-label">Hasta</label>
                <input name="date_to" type="date" class="form-control" />
              </div>
              <div class="col-12">
                <button type="reset" class="btn btn-outline-secondary btn-sm">Limpiar filtros</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
              <div>
                <h2 class="h5 mb-1">Pagos</h2>
                <p class="text-muted mb-0">Exporta movimientos de pagos registrados.</p>
              </div>
              <button form="export-payments" type="submit" class="btn btn-primary">Descargar CSV</button>
            </div>
            <form id="export-payments" method="get" action="/admin/exports/payments" class="row g-2 mt-2">
              <div class="col-6 col-md-3">
                <label class="form-label">Estado</label>
                <select name="status" class="form-select">
                  <option value="">Todos</option>
                  <option v-for="item in paymentStatuses" :key="item" :value="item">
                    {{ item }}
                  </option>
                </select>
              </div>
              <div class="col-6 col-md-3">
                <label class="form-label">Proveedor</label>
                <select name="provider" class="form-select">
                  <option value="">Todos</option>
                  <option v-for="item in providers" :key="item" :value="item">
                    {{ item }}
                  </option>
                </select>
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label">Plan</label>
                <select name="plan_id" class="form-select">
                  <option value="">Todos</option>
                  <option v-for="plan in plans" :key="plan.id" :value="plan.id">
                    {{ plan.label }}
                  </option>
                </select>
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label">Desde</label>
                <input name="date_from" type="date" class="form-control" />
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label">Hasta</label>
                <input name="date_to" type="date" class="form-control" />
              </div>
              <div class="col-12">
                <button type="reset" class="btn btn-outline-secondary btn-sm">Limpiar filtros</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
              <div>
                <h2 class="h5 mb-1">Tickets</h2>
                <p class="text-muted mb-0">Exporta el detalle de soporte.</p>
              </div>
              <button form="export-tickets" type="submit" class="btn btn-primary">Descargar CSV</button>
            </div>
            <form id="export-tickets" method="get" action="/admin/exports/tickets" class="row g-2 mt-2">
              <div class="col-6 col-md-2">
                <label class="form-label">Estado</label>
                <select name="status" class="form-select">
                  <option value="">Todos</option>
                  <option v-for="item in ticketStatuses" :key="item" :value="item">
                    {{ item }}
                  </option>
                </select>
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label">Prioridad</label>
                <select name="priority" class="form-select">
                  <option value="">Todas</option>
                  <option v-for="item in ticketPriorities" :key="item" :value="item">
                    {{ item }}
                  </option>
                </select>
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label">Categoria</label>
                <select name="category" class="form-select">
                  <option value="">Todas</option>
                  <option v-for="item in ticketCategories" :key="item" :value="item">
                    {{ item }}
                  </option>
                </select>
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label">Desde</label>
                <input name="date_from" type="date" class="form-control" />
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label">Hasta</label>
                <input name="date_to" type="date" class="form-control" />
              </div>
              <div class="col-12">
                <button type="reset" class="btn btn-outline-secondary btn-sm">Limpiar filtros</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
              <div>
                <h2 class="h5 mb-1">Actividad</h2>
                <p class="text-muted mb-0">Exporta registros de actividad.</p>
              </div>
              <button form="export-activities" type="submit" class="btn btn-primary">Descargar CSV</button>
            </div>
            <form
              id="export-activities"
              method="get"
              action="/admin/exports/activities"
              class="row g-2 mt-2"
            >
              <div class="col-6 col-md-3">
                <label class="form-label">Usuario ID</label>
                <input name="user_id" type="number" class="form-control" placeholder="ID de usuario" />
              </div>
              <div class="col-6 col-md-3">
                <label class="form-label">Tipo</label>
                <select name="type" class="form-select">
                  <option value="">Todos</option>
                  <option v-for="item in activityTypes" :key="item" :value="item">
                    {{ item }}
                  </option>
                </select>
              </div>
              <div class="col-6 col-md-3">
                <label class="form-label">Desde</label>
                <input name="date_from" type="date" class="form-control" />
              </div>
              <div class="col-6 col-md-3">
                <label class="form-label">Hasta</label>
                <input name="date_to" type="date" class="form-control" />
              </div>
              <div class="col-12">
                <button type="reset" class="btn btn-outline-secondary btn-sm">Limpiar filtros</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  roles: {
    type: Array,
    default: () => [],
  },
  plans: {
    type: Array,
    default: () => [],
  },
  subscriptionStatuses: {
    type: Array,
    default: () => [],
  },
  paymentStatuses: {
    type: Array,
    default: () => [],
  },
  providers: {
    type: Array,
    default: () => [],
  },
  ticketStatuses: {
    type: Array,
    default: () => [],
  },
  ticketPriorities: {
    type: Array,
    default: () => [],
  },
  ticketCategories: {
    type: Array,
    default: () => [],
  },
  activityTypes: {
    type: Array,
    default: () => [],
  },
})

const breadcrumbs = [
  { label: 'Exportaciones', active: true },
]
</script>
