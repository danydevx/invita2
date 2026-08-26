<template>
  <MemberLayout>
    <Head :title="`Equipos - ${listing?.name || ''}`" />

    <PageHeader
      title="Equipos de vCards"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/vcards`"
    >
      <template #actions>
        <button
          class="btn btn-primary btn-sm"
          @click="showTeamModal = true; editingTeam = null"
        >
          <i class="bi bi-plus-lg me-1"></i>
          Nuevo Equipo
        </button>
      </template>
    </PageHeader>

    <div class="alert alert-info mb-4">
      <i class="bi bi-info-circle me-2"></i>
      Los equipos te permiten organizar las tarjetas de presentación de tu negocio.
    </div>

    <div v-if="teams.length === 0" class="text-center py-5">
      <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
      <p class="text-muted mt-3">No hay equipos creados.</p>
      <button class="btn btn-primary" @click="showTeamModal = true; editingTeam = null">
        <i class="bi bi-plus-lg me-1"></i>
        Crear primer equipo
      </button>
    </div>

    <div v-else class="row g-4">
      <div class="col-12 col-md-6 col-lg-4" v-for="team in teams" :key="team.id">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h5 class="card-title mb-0">{{ team.name }}</h5>
              <span :class="team.active ? 'badge bg-success' : 'badge bg-secondary'">
                {{ team.active ? 'Activo' : 'Inactivo' }}
              </span>
            </div>
            <p v-if="team.description" class="text-muted small mb-3">{{ team.description }}</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted">
                <i class="bi bi-card-text me-1"></i>
                {{ team.vcards_count || 0 }} tarjetas
              </span>
              <div class="btn-group">
                <button class="btn btn-sm btn-outline-secondary" @click="editTeam(team)">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" @click="deleteTeam(team)">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showTeamModal" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" @click.self="closeModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingTeam ? 'Editar Equipo' : 'Nuevo Equipo' }}</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nombre</label>
              <input v-model="form.name" type="text" class="form-control" placeholder="Nombre del equipo">
              <div v-if="errors.name" class="text-danger small mt-1">{{ errors.name }}</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <textarea v-model="form.description" class="form-control" rows="3" placeholder="Descripción opcional"></textarea>
            </div>
            <div class="mb-3">
              <FieldSwitch
                id="team-active"
                label="Equipo activo"
                v-model="form.active"
              />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
            <button type="button" class="btn btn-primary" @click="saveTeam" :disabled="saving">
              {{ saving ? 'Guardando...' : 'Guardar' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import axios from 'axios'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const props = defineProps({
  listing: Object,
  teams: Array,
})

const showTeamModal = ref(false)
const editingTeam = ref(null)
const saving = ref(false)
const errors = ref({})

const form = reactive({
  name: '',
  description: '',
  active: true,
})

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'vCards', href: `/member/listings/${props.listing?.id}/vcards` },
  { label: 'Equipos' },
])

function editTeam(team) {
  editingTeam.value = team
  form.name = team.name
  form.description = team.description || ''
  form.active = team.active
  showTeamModal.value = true
}

function closeModal() {
  showTeamModal.value = false
  editingTeam.value = null
  form.name = ''
  form.description = ''
  form.active = true
  errors.value = {}
}

function saveTeam() {
  if (!form.name.trim()) {
    errors.value.name = 'El nombre es requerido'
    return
  }

  saving.value = true
  errors.value = {}

  if (editingTeam.value) {
    axios.put(`/member/listings/${props.listing.id}/vcard-teams/${editingTeam.value.id}`, form)
      .then(() => {
        toast.success('Equipo actualizado correctamente')
        closeModal()
        router.reload({ only: ['teams'] })
      })
      .catch((err) => {
        if (err.response?.data?.errors) {
          errors.value = err.response.data.errors
        } else {
          toast.error('Error al actualizar el equipo')
        }
      })
      .finally(() => {
        saving.value = false
      })
  } else {
    axios.post(`/member/listings/${props.listing.id}/vcard-teams`, form)
      .then(() => {
        toast.success('Equipo creado correctamente')
        closeModal()
        router.reload({ only: ['teams'] })
      })
      .catch((err) => {
        if (err.response?.data?.errors) {
          errors.value = err.response.data.errors
        } else {
          toast.error('Error al crear el equipo')
        }
      })
      .finally(() => {
        saving.value = false
      })
  }
}

function deleteTeam(team) {
  if (!confirm(`¿Eliminar el equipo "${team.name}"? Las tarjetas asociadas no serán eliminadas.`)) return

  axios.delete(`/member/listings/${props.listing.id}/vcard-teams/${team.id}`)
    .then(() => {
      toast.success('Equipo eliminado correctamente')
      router.reload({ only: ['teams'] })
    })
    .catch(() => {
      toast.error('Error al eliminar el equipo')
    })
}
</script>
