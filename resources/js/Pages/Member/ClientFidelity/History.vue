<template>
  <MemberLayout>
    <Head title="Historial de Fidelización" />

    <PageHeader
      title="Historial"
      :breadcrumbs="breadcrumbs"
    >
      <template #actions>
        <Link :href="`/member/listings/${listing?.id}/fidelity-rewards`" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-gift me-1"></i>
          Ver recompensas
        </Link>
      </template>
    </PageHeader>

    <div class="row g-4">
      <div class="col-12 col-md-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header">
            <h6 class="mb-0">Estadísticas</h6>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <span class="text-muted small d-block">Total completaciones</span>
              <strong class="fs-4">{{ stats.total_completions }}</strong>
            </div>
            <div class="mb-3">
              <span class="text-muted small d-block">Clientes únicos</span>
              <strong class="fs-4">{{ stats.unique_clients }}</strong>
            </div>
            <div class="mb-3">
              <span class="text-muted small d-block">Cliente más leal</span>
              <strong>{{ stats.top_client || '-' }}</strong>
              <span v-if="stats.top_client" class="text-muted small d-block">{{ stats.top_client_count }} completaciones</span>
            </div>
            <div>
              <span class="text-muted small d-block">Recompensa más popular</span>
              <strong>{{ stats.most_popular_reward || '-' }}</strong>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-9">
        <div class="card border-0 shadow-sm">
          <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
              <li class="nav-item">
                <button
                  class="nav-link"
                  :class="{ active: activeTab === 'clients' }"
                  type="button"
                  @click="activeTab = 'clients'"
                >
                  <i class="bi bi-people me-1"></i>
                  Por cliente
                </button>
              </li>
              <li class="nav-item">
                <button
                  class="nav-link"
                  :class="{ active: activeTab === 'completions' }"
                  type="button"
                  @click="activeTab = 'completions'"
                >
                  <i class="bi bi-list-check me-1"></i>
                  Completaciones
                </button>
              </li>
            </ul>
          </div>

          <div class="card-body">
            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <input
                  v-model="search"
                  type="search"
                  class="form-control"
                  placeholder="Buscar por cliente..."
                  @search="applyFilters"
                />
              </div>
              <div class="col-md-3">
                <select v-model="filters.reward_id" class="form-select" @change="applyFilters">
                  <option value="">Todas las recompensas</option>
                  <option v-for="reward in rewards" :key="reward.id" :value="reward.id">
                    {{ reward.title }}
                  </option>
                </select>
              </div>
              <div class="col-md-2">
                <input
                  v-model="filters.date_from"
                  type="date"
                  class="form-control"
                  placeholder="Desde"
                  @change="applyFilters"
                />
              </div>
              <div class="col-md-2">
                <input
                  v-model="filters.date_to"
                  type="date"
                  class="form-control"
                  placeholder="Hasta"
                  @change="applyFilters"
                />
              </div>
              <div class="col-md-1">
                <button class="btn btn-outline-secondary" type="button" @click="clearFilters">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>
            </div>

            <div v-if="activeTab === 'clients'" class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th>Cliente</th>
                    <th class="text-center">Completaciones</th>
                    <th class="text-center">Última</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="client in groupedByClient" :key="client.client_name">
                    <td>
                      <strong>{{ client.client_name }}</strong>
                    </td>
                    <td class="text-center">
                      <span class="badge bg-primary">{{ client.total_completions }}</span>
                    </td>
                    <td class="text-center text-muted small">
                      {{ formatDate(client.last_completion) }}
                    </td>
                  </tr>
                </tbody>
              </table>
              <div v-if="groupedByClient.length === 0" class="text-center py-4 text-muted">
                No hay datos
              </div>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th>Cliente</th>
                    <th>Recompensa</th>
                    <th>Visitas</th>
                    <th>Fecha</th>
                    <th>Completado por</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="completion in completions.data" :key="completion.id">
                    <td>
                      <strong>{{ completion.client_name }}</strong>
                    </td>
                    <td>
                      <span v-if="completion.reward">{{ completion.reward.title }}</span>
                      <span v-else class="text-muted">-</span>
                    </td>
                    <td>
                      <span class="badge bg-secondary">{{ completion.visits_completed }}</span>
                    </td>
                    <td class="text-muted small">
                      {{ formatDate(completion.created_at) }}
                    </td>
                    <td>
                      <span v-if="completion.completed_by" class="text-muted small">
                        {{ completion.completed_by.name }}
                      </span>
                      <span v-else class="text-muted">-</span>
                    </td>
                  </tr>
                </tbody>
              </table>

              <div v-if="completions.data.length === 0" class="text-center py-4 text-muted">
                No hay datos
              </div>

              <div v-if="completions.links" class="d-flex justify-content-center mt-4">
                <component
                  :is="Link"
                  v-for="link in completions.links"
                  :key="link.label"
                  :href="link.url || '#'"
                  class="btn btn-sm mx-1"
                  :class="[link.active ? 'btn-primary' : 'btn-outline-secondary', !link.url ? 'disabled' : '']"
                  v-html="link.label"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const rewards = computed(() => page.props.rewards || [])
const completions = computed(() => page.props.completions || { data: [], links: [] })
const groupedByClient = computed(() => page.props.groupedByClient || [])
const stats = computed(() => page.props.stats || {})
const activeTab = ref('clients')

const search = ref(page.props.filters?.search || '')
const filters = ref({
  reward_id: page.props.filters?.reward_id || '',
  date_from: page.props.filters?.date_from || '',
  date_to: page.props.filters?.date_to || '',
})

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Fidelización', href: `/member/listings/${listing.value?.id}/fidelity-cards` },
  { label: 'Historial' },
])

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('es-MX', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const applyFilters = () => {
  router.get(`/member/listings/${listing.value?.id}/fidelity-cards/history`, {
    search: search.value,
    reward_id: filters.value.reward_id,
    date_from: filters.value.date_from,
    date_to: filters.value.date_to,
  }, { preserveState: true })
}

const clearFilters = () => {
  search.value = ''
  filters.value = {
    reward_id: '',
    date_from: '',
    date_to: '',
  }
  applyFilters()
}

watch(search, () => {
  applyFilters()
})
</script>
