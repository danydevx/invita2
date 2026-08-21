<template>
  <MemberLayout>
    <Head title="Horarios de Atención" />

    <PageHeader
      title="Horarios de Atención"
      :breadcrumbs="breadcrumbs"
      backHref="/member"
    />

    <div v-if="flashSuccess" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ flashSuccess }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <p class="text-muted mb-0">
        Gestiona los horarios de atención para cada ubicación.
      </p>
    </div>

    <div v-if="businessesWithLocations.length === 0" class="alert alert-info">
      No tienes ubicaciones configuradas. Crea una ubicación primero para poder agregar horarios.
    </div>

    <div v-else class="row g-4">
      <div v-for="biz in businessesWithLocations" :key="biz.id" class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0">{{ biz.name }}</h5>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Ubicación</th>
                    <th>Horarios</th>
                    <th class="text-end">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="location in biz.locations" :key="location.id">
                    <td>
                      <strong>{{ location.name }}</strong>
                      <br>
                      <small class="text-muted">{{ location.city }}</small>
                    </td>
                    <td>
                      <div v-if="location.schedules && location.schedules.length">
                        <span v-for="(sched, idx) in location.schedules.slice(0, 3)" :key="sched.id" class="badge bg-light text-dark me-1">
                          {{ sched.name }}
                        </span>
                        <span v-if="location.schedules.length > 3" class="badge bg-secondary">
                          +{{ location.schedules.length - 3 }} más
                        </span>
                      </div>
                      <span v-else class="text-muted">Sin horarios</span>
                    </td>
                    <td class="text-end">
                      <Link
                        :href="`/member/listings/${biz.id}/locations/${location.id}/schedules`"
                        class="btn btn-outline-primary btn-sm"
                      >
                        <i class="bi bi-clock me-1"></i>
                        Gestionar Horarios
                      </Link>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const page = usePage()

const businesses = computed(() => page.props.businesses || [])
const flashSuccess = computed(() => page.props.flash?.success || null)

const businessesWithLocations = computed(() => {
  return businesses.value
    .map(biz => ({
      ...biz,
      locations: biz.locations || []
    }))
    .filter(biz => biz.locations && biz.locations.length > 0)
})

const breadcrumbs = [
  { label: 'Horarios', active: true },
]
</script>
