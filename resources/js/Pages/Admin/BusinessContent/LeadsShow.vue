<template>
  <AdminLayout>
    <Head :title="`Lead: ${lead.name} - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link :href="`/admin/businesses/${business.id}/leads`" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Volver a Leads
        </Link>
        <h1 class="h4 mb-1 mt-1">{{ lead.name }}</h1>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-md-8">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white">
            <h5 class="mb-0">Detalles del Lead</h5>
          </div>
          <div class="card-body">
            <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
              {{ $page.props.flash.success }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <form @submit.prevent="submit">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Estado</label>
                  <select v-model="form.status" class="form-select">
                    <option value="new">Nuevo</option>
                    <option value="contacted">Contactado</option>
                    <option value="qualified">Calificado</option>
                    <option value="converted">Convertido</option>
                    <option value="lost">Perdido</option>
                  </select>
                </div>

                <div class="col-12">
                  <label class="form-label">Notas</label>
                  <textarea v-model="form.notes" class="form-control" rows="4"></textarea>
                </div>

                <div class="col-12">
                  <button type="submit" class="btn btn-primary">
                    Actualizar Lead
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white">
            <h5 class="mb-0">Informacion de Contacto</h5>
          </div>
          <div class="card-body">
            <dl class="mb-0">
              <dt>Email</dt>
              <dd>{{ lead.email }}</dd>

              <dt>Telefono</dt>
              <dd>{{ lead.phone || '-' }}</dd>

              <dt>Fuente</dt>
              <dd>{{ lead.source_label }}</dd>

              <dt>Ubicacion</dt>
              <dd>{{ lead.location?.name || '-' }}</dd>

              <dt>Fecha de Creacion</dt>
              <dd>{{ formatDate(lead.created_at) }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, reactive } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const business = computed(() => page.props.business)
const lead = computed(() => page.props.lead)

const form = reactive({
  status: lead.value.status,
  notes: lead.value.notes || '',
})

const submit = () => {
  router.put(`/admin/businesses/${business.value.id}/leads/${lead.value.id}`, form)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleString('es-AR')
}
</script>
