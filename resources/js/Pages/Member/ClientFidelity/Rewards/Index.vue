<template>
  <MemberLayout>
    <Head title="Recompensas" />

    <PageHeader
      title="Recompensas"
      :breadcrumbs="breadcrumbs"
    >
      <template #actions>
        <Link :href="`/member/listings/${listing?.id}/fidelity-rewards/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nueva recompensa
        </Link>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm">
      <div class="card-header">
        <div class="row g-3">
          <div class="col-md-6">
            <input
              v-model="search"
              type="search"
              class="form-control"
              placeholder="Buscar por título..."
              @search="searchRewards"
            />
          </div>
        </div>
      </div>
      <div class="card-body">
        <div v-if="rewards.data.length === 0" class="text-center py-5">
          <i class="bi bi-gift text-muted" style="font-size: 3rem;"></i>
          <h3 class="h5 mt-3">No hay recompensas</h3>
          <p class="text-muted">Crea tu primera recompensa para empezar.</p>
          <Link :href="`/member/listings/${listing?.id}/fidelity-rewards/create`" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Crear recompensa
          </Link>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Imagen</th>
                <th>Título</th>
                <th>Visitas</th>
                <th>Estado</th>
                <th style="width: 120px;">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="reward in rewards.data" :key="reward.id">
                <td>
                  <img
                    v-if="reward.image"
                    :src="`/storage/${reward.image}`"
                    :alt="reward.title"
                    class="img-thumbnail"
                    style="width: 60px; height: 60px; object-fit: cover;"
                  />
                  <span v-else class="text-muted">
                    <i class="bi bi-image" style="font-size: 2rem;"></i>
                  </span>
                </td>
                <td>
                  <strong>{{ reward.title }}</strong>
                  <p v-if="reward.description" class="text-muted small mb-0">{{ reward.description }}</p>
                </td>
                <td>
                  <span class="badge bg-primary">{{ reward.max_visits }} visitas</span>
                </td>
                <td>
                  <span :class="reward.is_active ? 'badge bg-success' : 'badge bg-secondary'">
                    {{ reward.is_active ? 'Activa' : 'Inactiva' }}
                  </span>
                </td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <Link
                      :href="`/member/listings/${listing?.id}/fidelity-rewards/${reward.id}/edit`"
                      class="btn btn-outline-secondary"
                      title="Editar"
                    >
                      <i class="bi bi-pencil"></i>
                    </Link>
                    <button
                      type="button"
                      class="btn btn-outline-danger"
                      title="Eliminar"
                      :disabled="reward.cards_count > 0"
                      @click="confirmDelete(reward)"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="rewards.links" class="d-flex justify-content-center mt-4">
          <component
            :is="Link"
            v-for="link in rewards.links"
            :key="link.label"
            :href="link.url || '#'"
            class="btn btn-sm mx-1"
            :class="[link.active ? 'btn-primary' : 'btn-outline-secondary', !link.url ? 'disabled' : '']"
            v-html="link.label"
          />
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
const rewards = computed(() => page.props.rewards || { data: [], links: [] })
const search = ref(page.props.filters?.search || '')

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Fidelización', href: `/member/listings/${listing.value?.id}/fidelity-cards` },
  { label: 'Recompensas' },
])

const searchRewards = () => {
  router.get(`/member/listings/${listing.value?.id}/fidelity-rewards`, {
    search: search.value,
  }, { preserveState: true })
}

watch(search, () => {
  searchRewards()
})

const confirmDelete = (reward) => {
  if (reward.cards_count > 0) {
    alert('No se puede eliminar una recompensa que tiene tarjetas asociadas.')
    return
  }
  if (confirm(`¿Eliminar la recompensa "${reward.title}"?`)) {
    router.delete(`/member/listings/${listing.value?.id}/fidelity-rewards/${reward.id}`)
  }
}
</script>
