<template>
  <AdminLayout>
    <Head title="Detalle automatizacion" />

    <PageHeader :title="automation.name" :breadcrumbs="breadcrumbs" backHref="/admin/automations" />

    <div class="row g-3">
      <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6">Configuracion</h2>
            <dl class="row mb-0">
              <dt class="col-5 text-muted">Evento</dt>
              <dd class="col-7">{{ automation.event_key }}</dd>
              <dt class="col-5 text-muted">Accion</dt>
              <dd class="col-7">{{ automation.action_key }}</dd>
              <dt class="col-5 text-muted">Activo</dt>
              <dd class="col-7">
                <span v-if="automation.is_active" class="badge text-bg-success">Activo</span>
                <span v-else class="badge text-bg-secondary">Inactivo</span>
              </dd>
            </dl>
            <div class="mt-3">
              <label class="form-label">Config</label>
              <div class="bg-light border rounded p-3" style="white-space: pre-wrap">
                {{ formattedConfig }}
              </div>
            </div>
            <div class="mt-3">
              <button class="btn btn-primary" type="button" :disabled="form.processing" @click="toggle">
                {{ form.processing ? 'Actualizando...' : (automation.is_active ? 'Desactivar' : 'Activar') }}
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6">Ejecuciones recientes</h2>
            <div v-if="runs.data.length === 0" class="text-muted">Sin ejecuciones.</div>
            <div v-else class="table-responsive">
              <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th scope="col">Estado</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Metadata</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="run in runs.data" :key="run.id">
                    <td class="text-muted">{{ run.status }}</td>
                    <td class="text-muted">{{ run.executed_at }}</td>
                    <td class="text-muted">
                      <span v-if="run.metadata">{{ JSON.stringify(run.metadata) }}</span>
                      <span v-else>-</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div v-if="runs.data.length" class="card-footer">
            <Pagination :links="runs.links" />
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const props = defineProps({
  automation: {
    type: Object,
    required: true,
  },
  runs: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  is_active: !!props.automation.is_active,
})

const breadcrumbs = [
  { label: 'Automatizaciones', href: '/admin/automations' },
  { label: props.automation.name, active: true },
]

const formattedConfig = computed(() => {
  if (!props.automation.config || Object.keys(props.automation.config).length === 0) {
    return 'Sin config.'
  }
  return JSON.stringify(props.automation.config, null, 2)
})

const toggle = () => {
  form.is_active = !form.is_active
  form.put(`/admin/automations/${props.automation.id}`)
}
</script>
