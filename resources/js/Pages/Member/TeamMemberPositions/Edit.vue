<template>
  <MemberLayout>
    <Head :title="`Editar Puesto - ${listing?.name || ''}`" />

    <PageHeader
      title="Editar Puesto"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/team-member-positions`"
    />

    <div class="row">
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <form @submit.prevent="submit">
              <div class="mb-3">
                <FieldText
                  id="position-name"
                  label="Nombre del puesto"
                  placeholder="Ej: Recepcionista"
                  v-model="form.name"
                  :formError="form.errors.name"
                  required
                />
              </div>

              <div class="mb-3">
                <FieldSelect
                  id="position-parent"
                  label="Puesto padre (opcional)"
                  v-model="form.parent_id"
                  :options="parentPositionOptions"
                  :formError="form.errors.parent_id"
                />
              </div>

              <div class="mb-3">
                <FieldTextarea
                  id="position-description"
                  label="Descripción (opcional)"
                  placeholder="Describe las responsabilidades del puesto"
                  v-model="form.description"
                  :formError="form.errors.description"
                  rows="3"
                />
              </div>

              <div class="mb-3">
                <FieldSwitch
                  id="position-active"
                  label="Activo"
                  v-model="form.is_active"
                  :formError="form.errors.is_active"
                />
                <div class="form-text">Los puestos inactivos no aparecerán en las opciones de filtro.</div>
              </div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                  <i class="bi bi-check me-1"></i>
                  {{ form.processing ? 'Guardando...' : 'Guardar' }}
                </button>
                <Link :href="`/member/listings/${listing?.id}/team-member-positions`" class="btn btn-outline-secondary">
                  Cancelar
                </Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const position = computed(() => page.props.position)
const parentPositions = computed(() => page.props.parentPositions || [])
const businessMenu = computed(() => page.props.businessMenu || [])

const parentPositionOptions = computed(() => {
  const options = parentPositions.value.map(p => ({
    value: p.id,
    label: p.name,
  }))
  return [{ value: '', label: 'Sin puesto padre' }, ...options]
})

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Mi Equipo', href: `/member/listings/${listing.value?.id}/team-members` },
  { label: 'Puestos', href: `/member/listings/${listing.value?.id}/team-member-positions` },
  { label: position.value?.name || 'Editar' },
])

const form = useForm({
  name: position.value?.name || '',
  parent_id: position.value?.parent_id || '',
  description: position.value?.description || '',
  is_active: position.value?.is_active ?? true,
})

const submit = () => {
  form.put(`/member/listings/${listing.value.id}/team-member-positions/${position.value.id}`, {
    preserveScroll: true,
  })
}
</script>
