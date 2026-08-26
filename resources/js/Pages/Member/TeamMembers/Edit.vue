<template>
  <MemberLayout>
    <Head :title="`Editar Miembro - ${listing?.name || ''}`" />

    <PageHeader
      title="Editar Miembro"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/team-members`"
    />

    <div class="row">
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <form @submit.prevent="submit">
              <div class="row g-3">
                <div class="col-md-8">
                  <FieldText
                    id="member-name"
                    label="Nombre completo"
                    placeholder="Ej: Juan Pérez"
                    v-model="form.name"
                    :formError="form.errors.name"
                    required
                  />
                </div>
                <div class="col-md-4">
                  <FieldSelect
                    id="member-position"
                    label="Puesto"
                    v-model="form.position_id"
                    :options="positionOptions"
                    :formError="form.errors.position_id"
                  />
                </div>
              </div>

              <div class="row g-3 mt-3">
                <div class="col-md-6">
                  <FieldEmail
                    id="member-email"
                    label="Correo electrónico (opcional)"
                    placeholder="juan@ejemplo.com"
                    v-model="form.email"
                    :formError="form.errors.email"
                  />
                </div>
                <div class="col-md-6">
                  <FieldText
                    id="member-phone"
                    label="Teléfono (opcional)"
                    placeholder="+52 555 123 4567"
                    v-model="form.phone"
                    :formError="form.errors.phone"
                  />
                </div>
              </div>

              <div class="mb-3 mt-3">
                <FieldTextarea
                  id="member-bio"
                  label="Biografía (opcional)"
                  placeholder="Cuéntanos sobre este miembro del equipo"
                  v-model="form.bio"
                  :formError="form.errors.bio"
                  :rows="3"
                />
              </div>

              <div class="mb-3">
                <label class="form-label">Foto (opcional)</label>
                <input
                  type="file"
                  class="form-control"
                  accept="image/jpeg,image/png"
                  @change="handleImageChange"
                />
                <div v-if="form.errors.image" class="text-danger small mt-1">{{ form.errors.image }}</div>
                <div v-if="imagePreview || member?.image" class="mt-2">
                  <img 
                    :src="imagePreview || member?.image" 
                    class="rounded" 
                    style="width: 80px; height: 80px; object-fit: cover;" 
                  />
                  <button 
                    v-if="member?.image && !imagePreview"
                    type="button" 
                    class="btn btn-outline-danger btn-sm ms-2"
                    @click="removeImage"
                  >
                    <i class="bi bi-trash"></i> Eliminar
                  </button>
                </div>
              </div>

              <div class="mb-3">
                <FieldSwitch
                  id="member-active"
                  label="Activo"
                  v-model="form.is_active"
                  :formError="form.errors.is_active"
                />
                <div class="form-text">Los miembros inactivos no aparecerán en el minisite.</div>
              </div>

              <FormActions :submitText="'Guardar'" :submittingText="'Guardando...'" :cancelHref="`/member/listings/${listing?.id}/team-members`" :sending="form.processing" />
            </form>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FormActions from '@/Components/FormActions.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const member = computed(() => page.props.member)
const positions = computed(() => page.props.positions || [])
const businessMenu = computed(() => page.props.businessMenu || [])

const positionOptions = computed(() => {
  return [
    { value: '', label: 'Sin puesto' },
    ...positions.value.map(p => ({
      value: p.id,
      label: p.name,
    }))
  ]
})

const imagePreview = ref(null)

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Mi Equipo', href: `/member/listings/${listing.value?.id}/team-members` },
  { label: member.value?.name || 'Editar' },
])

const form = useForm({
  name: member.value?.name || '',
  email: member.value?.email || '',
  phone: member.value?.phone || '',
  bio: member.value?.bio || '',
  position_id: member.value?.position_id || '',
  image: null,
  is_active: member.value?.is_active ?? true,
})

const handleImageChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.image = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

const removeImage = () => {
  form.image = ''
  imagePreview.value = null
}

const submit = () => {
  form.post(`/member/listings/${listing.value.id}/team-members/${member.value.id}`, {
    preserveScroll: true,
  })
}
</script>
