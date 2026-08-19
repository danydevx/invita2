<template>
  <component :is="layout">
    <Head title="Perfil" />

    <PageHeader :title="'Perfil'" :breadcrumbs="breadcrumbs" backHref="/admin/dashboard" />

    <div class="row g-3">
      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body text-center">
            <h2 class="h6 mb-3">Foto de perfil</h2>

            <div class="mb-3">
              <div class="avatar-preview mx-auto mb-3">
                <img
                  v-if="avatarPreview || form.avatar"
                  :src="avatarPreview || `/storage/${form.avatar}`"
                  alt="Avatar"
                  class="rounded-circle"
                  style="width: 120px; height: 120px; object-fit: cover;"
                />
                <div v-else class="avatar-placeholder rounded-circle">
                  <i class="bi bi-person fs-1"></i>
                </div>
              </div>

              <input
                ref="avatarInput"
                type="file"
                accept="image/jpeg,image/png"
                class="d-none"
                @change="handleAvatarChange"
              />

              <button
                type="button"
                class="btn btn-outline-secondary btn-sm"
                @click="$refs.avatarInput.click()"
              >
                <i class="bi bi-upload me-1"></i>
                {{ form.avatar || avatarPreview ? 'Cambiar' : 'Subir' }}
              </button>

              <button
                v-if="form.avatar || avatarPreview"
                type="button"
                class="btn btn-outline-danger btn-sm ms-2"
                @click="removeAvatar"
              >
                <i class="bi bi-trash"></i>
              </button>

              <div v-if="form.errors.avatar" class="text-danger small mt-2">
                {{ form.errors.avatar }}
              </div>
              <div class="text-muted small mt-2">
                JPG o PNG, max 2MB
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Personales</h2>

            <div class="row g-3">
              <div class="col-12 col-md-6">
                <FieldText
                  id="profile-name"
                  label="Nombre"
                  placeholder="Tu nombre"
                  v-model="form.name"
                  :formError="form.errors.name"
                />
              </div>

              <div class="col-12 col-md-6">
                <FieldSelect
                  id="profile-country"
                  label="Pais"
                  v-model="form.country"
                  :options="countryOptions"
                  :formError="form.errors.country"
                />
              </div>

              <div class="col-12 col-md-6">
                <FieldText
                  id="profile-phone"
                  label="Telefono"
                  placeholder="+52 555 000 0000"
                  v-model="form.phone"
                  :formError="form.errors.phone"
                />
              </div>

              <div class="col-12 col-md-6">
                <FieldWhatsapp
                  id="profile-whatsapp"
                  label="WhatsApp"
                  placeholder="555 000 0000"
                  v-model="form.whatsapp"
                  :countryValue="form.whatsapp_country"
                  :formError="form.errors.whatsapp"
                  @update:countryValue="form.whatsapp_country = $event"
                />
              </div>

              <div class="col-12">
                <FieldEmail
                  id="profile-personal-email"
                  label="E-Mail personal"
                  placeholder="tu@email.com"
                  v-model="form.personal_email"
                  :formError="form.errors.personal_email"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
          <div class="card-body">
            <h2 class="h6 mb-3">Redes sociales</h2>

            <div class="row g-3">
              <div class="col-12 col-md-4">
                <FieldText
                  id="profile-facebook"
                  label="Facebook"
                  placeholder="https://facebook.com/usuario"
                  v-model="form.facebook"
                  :formError="form.errors.facebook"
                />
              </div>

              <div class="col-12 col-md-4">
                <FieldText
                  id="profile-instagram"
                  label="Instagram"
                  placeholder="https://instagram.com/usuario"
                  v-model="form.instagram"
                  :formError="form.errors.instagram"
                />
              </div>

              <div class="col-12 col-md-4">
                <FieldText
                  id="profile-x"
                  label="X"
                  placeholder="https://x.com/usuario"
                  v-model="form.x"
                  :formError="form.errors.x"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="mt-3">
          <button type="button" class="btn btn-primary" :disabled="form.processing" @click="submit">
            {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
          </button>
        </div>
      </div>
    </div>
  </component>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldWhatsapp from '@/Components/Fields/FieldWhatsapp.vue'

const props = defineProps({
  profile: {
    type: Object,
    required: true,
  },
})

const page = usePage()
const avatarPreview = ref(null)

const breadcrumbs = [
  { label: 'Perfil' },
]

const countryOptions = [
  { value: 'US', label: 'Estados Unidos' },
  { value: 'CA', label: 'Canada' },
  { value: 'MX', label: 'Mexico' },
  { value: 'CU', label: 'Cuba' },
  { value: 'AR', label: 'Argentina' },
  { value: 'BR', label: 'Brasil' },
  { value: 'CL', label: 'Chile' },
  { value: 'CO', label: 'Colombia' },
  { value: 'VE', label: 'Venezuela' },
  { value: 'BO', label: 'Bolivia' },
  { value: 'EC', label: 'Ecuador' },
  { value: 'PY', label: 'Paraguay' },
  { value: 'UY', label: 'Uruguay' },
  { value: 'PE', label: 'Peru' },
  { value: 'BZ', label: 'Belice' },
  { value: 'GT', label: 'Guatemala' },
  { value: 'SV', label: 'El Salvador' },
  { value: 'HN', label: 'Honduras' },
  { value: 'NI', label: 'Nicaragua' },
  { value: 'CR', label: 'Costa Rica' },
  { value: 'PA', label: 'Panama' },
  { value: 'HT', label: 'Haiti' },
  { value: 'PR', label: 'Puerto Rico' },
  { value: 'DO', label: 'Republica Dominicana' },
  { value: 'JM', label: 'Jamaica' },
  { value: 'TT', label: 'Trinidad y Tobago' },
  { value: 'BS', label: 'Bahamas' },
  { value: 'BB', label: 'Barbados' },
  { value: 'GY', label: 'Guyana' },
]

const form = useForm({
  name: props.profile.name || '',
  phone: props.profile.phone || '',
  whatsapp: props.profile.whatsapp || '',
  whatsapp_country: props.profile.whatsapp_country || '+1',
  facebook: props.profile.facebook || '',
  instagram: props.profile.instagram || '',
  x: props.profile.x || '',
  personal_email: props.profile.personal_email || '',
  country: props.profile.country || '',
  avatar: props.profile.avatar || '',
})

const layout = computed(() => {
  const roles = page.props.auth?.roles || []
  const isAdmin = roles.includes('admin') || roles.includes('superadmin')
  return isAdmin ? AdminLayout : MemberLayout
})

const handleAvatarChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.avatar = file
    avatarPreview.value = URL.createObjectURL(file)
  }
}

const avatarDeleted = ref(false)

const removeAvatar = () => {
  form.avatar = ''
  avatarDeleted.value = true
  avatarPreview.value = null
}

const submit = () => {
  const data = { ...form.data() }
  if (avatarDeleted.value) {
    data.avatar_delete = true
  }
  if (typeof data.avatar === 'string' && data.avatar !== '') {
    delete data.avatar
  }
  form.post('/admin/profile', {
    preserveScroll: true,
    data: data,
  })
}
</script>

<style scoped>
.avatar-placeholder {
  width: 120px;
  height: 120px;
  background-color: #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #adb5bd;
  margin: 0 auto;
}
</style>
