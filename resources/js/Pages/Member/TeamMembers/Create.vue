<template>
  <MemberLayout>
    <Head :title="`Nuevo Miembro - ${listing?.name || ''}`" />

    <PageHeader
      title="Nuevo Miembro"
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
                    :formError="errors.name"
                    required
                  />
                </div>
                <div class="col-md-4">
                  <FieldSelect
                    id="member-position"
                    label="Puesto"
                    v-model="form.position_id"
                    :options="positionOptions"
                    :formError="errors.position_id"
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
                    :formError="errors.email"
                    autocomplete="email"
                  />
                </div>
                <div class="col-md-6">
                  <FieldText
                    id="member-phone"
                    label="Teléfono (opcional)"
                    placeholder="+52 555 123 4567"
                    v-model="form.phone"
                    :formError="errors.phone"
                    autocomplete="tel"
                  />
                </div>
              </div>

              <div class="mb-3 mt-3">
                <FieldTextarea
                  id="member-bio"
                  label="Biografía (opcional)"
                  placeholder="Cuéntanos sobre este miembro del equipo"
                  v-model="form.bio"
                  :formError="errors.bio"
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
                <div v-if="imagePreview" class="mt-2">
                  <img :src="imagePreview" class="rounded" style="width: 80px; height: 80px; object-fit: cover;" />
                </div>
              </div>

              <div class="mb-3">
                <FieldSwitch
                  id="member-active"
                  label="Activo"
                  v-model="form.is_active"
                />
                <div class="form-text">Los miembros inactivos no aparecerán en el minisite.</div>
              </div>

              <FormActions :submitText="'Guardar'" :submittingText="'Guardando...'" :cancelHref="`/member/listings/${listing?.id}/team-members`" :sending="sending" />
            </form>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
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
const sending = ref(false)

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Mi Equipo', href: `/member/listings/${listing.value?.id}/team-members` },
  { label: 'Nuevo' },
])

const form = reactive({
  name: '',
  email: '',
  phone: '',
  bio: '',
  position_id: '',
  image: null,
  is_active: true,
})

const errors = reactive({
  name: '',
  email: '',
  phone: '',
  bio: '',
  position_id: '',
})

const validateForm = () => {
  let isValid = true

  errors.name = ''
  errors.email = ''
  errors.phone = ''
  errors.bio = ''
  errors.position_id = ''

  if (!form.name || form.name.trim() === '') {
    errors.name = 'El nombre es obligatorio.'
    isValid = false
  } else if (form.name.length > 150) {
    errors.name = 'El nombre no puede tener más de 150 caracteres.'
    isValid = false
  }

  if (form.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = 'El email no es válido.'
    isValid = false
  }

  return isValid
}

const handleImageChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.image = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

const submit = () => {
  if (!validateForm()) {
    toast.warning('Por favor completa los campos requeridos')
    return
  }

  sending.value = true
  const formData = new FormData()
  formData.append('name', form.name)
  formData.append('email', form.email || '')
  formData.append('phone', form.phone || '')
  formData.append('bio', form.bio || '')
  formData.append('position_id', form.position_id || '')
  formData.append('is_active', form.is_active ? '1' : '0')

  if (form.image instanceof File) {
    formData.append('image', form.image)
  }

  router.post(`/member/listings/${listing.value.id}/team-members`, formData, {
    preserveScroll: true,
    onSuccess: () => {
      sending.value = false
    },
    onError: (errs) => {
      sending.value = false
      Object.keys(errs).forEach(key => {
        if (key in errors) {
          errors[key] = errs[key]
        }
      })
      toast.warning('Por favor completa los campos requeridos')
    },
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
